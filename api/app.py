"""
app.py — Flask REST API for DatasetGeneratorTool.

Endpoints:
    GET  /api/defaults  — list available default value sources
    GET  /api/types     — list supported output formats
    POST /api/generate  — generate a dataset and return as a file download
"""

import os
from dotenv import load_dotenv
from flask import Flask, jsonify, request, Response
from flask_cors import CORS

load_dotenv()

from generator import (
    generate_dataset,
    DEFAULT_SOURCES,
    SUPPORTED_TYPES,
    resolve_file_values,
    resolve_custom_values,
)

app = Flask(__name__)
CORS(app)


@app.route("/api/defaults", methods=["GET"])
def list_defaults():
    return jsonify(DEFAULT_SOURCES)


@app.route("/api/types", methods=["GET"])
def list_types():
    return jsonify(list(SUPPORTED_TYPES.keys()))


@app.route("/api/generate", methods=["POST"])
def generate():
    """
    Generate a dataset file.

    Accepts either JSON or multipart/form-data.

    Form fields (both modes):
        rows   : int
        keys   : comma-separated column names
        values : comma-separated value sources (use "file:N" for uploaded files)
        type   : output format (optional, default "json")
        name   : table/root name (optional, default "data")

    File fields (multipart only):
        file_0, file_1, … : uploaded plain text files
    """
    if request.content_type and "multipart/form-data" in request.content_type:
        data = request.form.to_dict()
        uploaded_files = request.files
    elif request.content_type and "application/json" in request.content_type:
        data = request.get_json(silent=True) or {}
        uploaded_files = {}
    else:
        data = request.form.to_dict() or request.get_json(silent=True) or {}
        uploaded_files = request.files

    missing = [f for f in ("rows", "keys", "values") if f not in data]
    if missing:
        return jsonify({"error": f"Missing required fields: {', '.join(missing)}"}), 400

    try:
        rows = int(data["rows"])
    except (ValueError, TypeError):
        return jsonify({"error": "'rows' must be an integer."}), 400

    keys_str = str(data["keys"]).strip()
    values_str = str(data["values"]).strip()
    fmt = str(data.get("type", "json")).strip()
    table_name = str(data.get("name", "data")).strip()

    keys = [k.strip() for k in keys_str.split(",") if k.strip()]
    values = [v.strip() for v in values_str.split(",") if v.strip()]

    if not keys:
        return jsonify({"error": "'keys' must not be empty."}), 400
    if not values:
        return jsonify({"error": "'values' must not be empty."}), 400

    if len(keys) != len(values):
        return jsonify({
            "error": f"Number of keys ({len(keys)}) does not match number of values ({len(values)})."
        }), 400

    # --- Resolve file-based value sources ---
    # "file:N" placeholders → actual file content
    #
    # Security measures:
    #   1. Max file size enforced (10 MB)
    #   2. Binary content rejected (null-byte check)
    #   3. Strict UTF-8 validation (no silent replacement)
    #   4. User-supplied filename is never used internally
    #
    # file_modes format: "0:cycle+random,1:cycle"
    MAX_UPLOAD_BYTES = 10 * 1024 * 1024  # 10 MB

    file_modes_str = data.get("file_modes", "")
    file_flags = {}
    if file_modes_str:
        for entry in file_modes_str.split(","):
            entry = entry.strip()
            if ":" in entry:
                idx_str, flags_str = entry.split(":", 1)
                flags = {f.strip() for f in flags_str.split("+") if f.strip()}
                file_flags[int(idx_str)] = flags

    resolved_file_values = {}
    for i, v in enumerate(values):
        if v.startswith("file:"):
            parts = v.split(":")
            file_idx = parts[1]
            file_key = f"file_{file_idx}"
            if file_key not in uploaded_files:
                return jsonify({"error": f"Missing uploaded file for '{file_key}'."}), 400

            uploaded = uploaded_files[file_key]

            raw = uploaded.read()
            if len(raw) > MAX_UPLOAD_BYTES:
                return jsonify({
                    "error": f"File too large ({len(raw)} bytes). Max allowed: {MAX_UPLOAD_BYTES} bytes."
                }), 400

            import magic
            mime = magic.from_buffer(raw, mime=True)
            if mime not in ["text/plain", "text/x-log"]:
                return jsonify({
                    "error": f"Invalid file type: '{mime}'. Only plain text files are accepted."
                }), 400

            if not all(c.isprintable() or c in "\n\r\t" for c in raw.decode("utf-8")):
                return jsonify({
                    "error": "File appears to be binary, not plain text. Only text files are accepted."
                }), 400

            try:
                file_text = raw.decode("utf-8")
            except UnicodeDecodeError:
                return jsonify({
                    "error": "File is not valid UTF-8 text. Please upload a plain text file."
                }), 400

            flags = file_flags.get(int(file_idx), set())
            resolved_file_values[i] = resolve_file_values(
                file_text, rows,
                cycle="cycle" in flags,
                randomize="random" in flags,
            )

    # --- Resolve custom value sources ---
    # "custom:N" placeholders → range-based string values
    # Custom rules sent as JSON in form field "custom_rules":
    #   {"0": [{"from":1,"to":50,"value":"val1"}, ...], "1": [...]}
    import json as json_mod
    custom_rules_raw = data.get("custom_rules", "")
    custom_rules = {}
    if custom_rules_raw:
        try:
            custom_rules = json_mod.loads(custom_rules_raw) if isinstance(custom_rules_raw, str) else custom_rules_raw
        except (json_mod.JSONDecodeError, TypeError):
            return jsonify({"error": "Invalid custom_rules JSON."}), 400

    for i, v in enumerate(values):
        if v.startswith("custom:"):
            custom_idx = v.split(":")[1]
            rules = custom_rules.get(custom_idx, [])
            if not isinstance(rules, list):
                return jsonify({"error": f"Custom rules for index {custom_idx} must be a list."}), 400
            resolved_file_values[i] = resolve_custom_values(rules, rows)

    try:
        content, filename, mime_type = generate_dataset(
            rows=rows,
            keys=keys,
            values=values,
            fmt=fmt,
            table_name=table_name,
            file_overrides=resolved_file_values,
        )
    except ValueError as exc:
        return jsonify({"error": str(exc)}), 400
    except Exception as exc:
        return jsonify({"error": f"Generation failed: {exc}"}), 500

    return Response(
        content,
        mimetype=mime_type,
        headers={
            "Content-Disposition": f"attachment; filename={filename}",
        },
    )


if __name__ == "__main__":
    host = os.getenv("HOST", "0.0.0.0")
    port = int(os.getenv("PORT", 5000))
    app.run(debug=False, host=host, port=port)
