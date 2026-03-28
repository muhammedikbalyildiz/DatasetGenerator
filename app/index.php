<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Generate synthetic datasets with customisable columns, value generators, and multiple output formats.">
    <title>Dataset Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #161616;
            --bg-raised: #1e1e1e;
            --bg-field: #262626;
            --border: #393939;
            --border-hover: #525252;
            --text: #f4f4f4;
            --text-secondary: #a8a8a8;
            --text-muted: #6f6f6f;
            --blue: #4589ff;
            --blue-hover: #6ea6ff;
            --green: #42be65;
            --red: #fa4d56;
            --radius: 4px;
        }

        body {
            font-family: 'IBM Plex Sans', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
            font-size: 14px;
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
            padding: 48px 24px 96px;
        }

        /* --- Header --- */
        .header {
            margin-bottom: 40px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* --- Section --- */
        .section {
            background: var(--bg-raised);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 20px;
        }

        .section-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .section-body {
            padding: 20px;
        }

        /* --- Form --- */
        .form-group { margin-bottom: 16px; }
        .form-group:last-child { margin-bottom: 0; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }

        input, select {
            width: 100%;
            padding: 8px 12px;
            background: var(--bg-field);
            border: 1px solid var(--border);
            border-bottom: 2px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s;
        }

        input:focus, select:focus {
            border-bottom-color: var(--blue);
        }

        input::placeholder { color: var(--text-muted); }

        select {
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236f6f6f' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.762L10.825 4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 28px;
        }

        select option { background: var(--bg-field); color: var(--text); }

        input[type="file"] {
            padding: 6px 8px;
            font-size: 0.8rem;
            cursor: pointer;
        }

        input[type="file"]::file-selector-button {
            background: var(--bg-field);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-family: inherit;
            cursor: pointer;
            margin-right: 8px;
        }

        input[type="file"]::file-selector-button:hover {
            color: var(--text);
            border-color: var(--border-hover);
        }

        /* --- Filename row --- */
        .filename-row {
            display: flex;
            align-items: end;
            gap: 0;
        }

        .filename-row input {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
        }

        .filename-ext {
            padding: 8px 12px;
            background: var(--bg-field);
            border: 1px solid var(--border);
            border-bottom: 2px solid var(--border);
            border-top-right-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
            color: var(--text-muted);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.8rem;
            white-space: nowrap;
            user-select: none;
        }

        .hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* --- Column builder --- */
        .columns-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .column-entry {
            padding: 12px;
            background: var(--bg-field);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .column-entry-top {
            display: grid;
            grid-template-columns: 1fr auto 28px;
            gap: 8px;
            align-items: end;
        }

        .column-entry-value { margin-top: 8px; }

        .column-entry input, .column-entry select {
            background: var(--bg-raised);
        }

        /* --- Toggle styles --- */
        .toggle-group {
            display: inline-flex;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .toggle-group button {
            padding: 4px 12px;
            background: transparent;
            color: var(--text-muted);
            border: none;
            border-right: 1px solid var(--border);
            font-family: inherit;
            font-size: 0.72rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .toggle-group button:last-child { border-right: none; }

        .toggle-group button.active {
            background: var(--blue);
            color: #fff;
        }

        .toggle-group button:hover:not(.active) {
            color: var(--text-secondary);
            background: rgba(255,255,255,0.03);
        }

        /* --- File options --- */
        .file-options {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
        }

        .file-options span {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .opt-toggle {
            display: inline-flex;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .opt-toggle button {
            padding: 3px 10px;
            background: transparent;
            color: var(--text-muted);
            border: none;
            border-right: 1px solid var(--border);
            font-family: inherit;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .opt-toggle button:last-child { border-right: none; }

        .opt-toggle button.active {
            background: var(--green);
            color: #161616;
        }

        .opt-toggle button:hover:not(.active) {
            color: var(--text-secondary);
        }

        /* --- Custom range entries --- */
        .custom-rules { margin-top: 6px; }

        .custom-rule {
            display: grid;
            grid-template-columns: 65px 65px 1fr 28px;
            gap: 6px;
            align-items: center;
            margin-bottom: 6px;
        }

        .custom-rule input {
            background: var(--bg-raised);
            padding: 6px 8px;
            font-size: 0.8rem;
            width: 100%;
        }

        .custom-rules-header {
            display: grid;
            grid-template-columns: 65px 65px 1fr 28px;
            gap: 6px;
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0 4px;
        }

        .btn-add-rule {
            padding: 5px;
            border: 1px dashed var(--border);
            border-radius: var(--radius);
            background: transparent;
            color: var(--text-muted);
            font-size: 0.72rem;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-add-rule:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        /* --- Buttons --- */
        .btn-remove {
            width: 28px;
            height: 32px;
            border: none;
            border-radius: var(--radius);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .btn-remove:hover { color: var(--red); background: rgba(250,77,86,0.1); }

        .btn-add {
            width: 100%;
            padding: 8px;
            border: 1px dashed var(--border);
            border-radius: var(--radius);
            background: transparent;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-add:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .btn-generate {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: var(--radius);
            background: var(--blue);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-generate:hover:not(:disabled) { background: var(--blue-hover); }
        .btn-generate:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-generate .spinner {
            display: none;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.5s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }

        .btn-generate.loading .spinner { display: inline-block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* --- Status --- */
        .status {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: var(--radius);
            font-size: 0.8rem;
            display: none;
            border-left: 3px solid;
        }

        .status.success {
            display: block;
            background: rgba(66,190,101,0.08);
            border-left-color: var(--green);
            color: var(--green);
        }

        .status.error {
            display: block;
            background: rgba(250,77,86,0.08);
            border-left-color: var(--red);
            color: var(--red);
        }

        .github-link {
            color: var(--text-secondary);
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .github-link:hover {
            color: var(--text);
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.03);
        }

        /* --- Defaults table --- */
        .defaults-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .defaults-table th {
            text-align: left;
            font-weight: 500;
            color: var(--text-muted);
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .defaults-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .defaults-table tr:last-child td { border-bottom: none; }

        .defaults-table code {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.78rem;
            color: var(--text);
        }

        /* --- Responsive --- */
        @media (max-width: 600px) {
            .container { padding: 24px 16px 60px; }
            .section-body { padding: 14px; }
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
            .column-entry-top { grid-template-columns: 1fr; }
            .header h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h1>Dataset Generator</h1>
                <p>Generate datasets with custom columns and export formats.</p>
            </div>
            <a href="https://github.com/muhammedikbalyildiz/DatasetGenerator" target="_blank" class="github-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                <span>Source Code</span>
            </a>
        </div>
    </div>

    <!-- Generator -->
    <div class="section">
        <div class="section-header">Configuration</div>
        <div class="section-body">
            <form id="generatorForm">
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="rows">Rows</label>
                        <input type="number" id="rows" min="1" value="10" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Format</label>
                        <select id="type">
                            <option value="json" selected>JSON</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tableName">Table Name (for SQL and XML)</label>
                        <input type="text" id="tableName" value="data" placeholder="data">
                    </div>
                </div>

                <div class="form-group">
                    <label for="filename">Output filename</label>
                    <div class="filename-row">
                        <input type="text" id="filename" value="dataset" placeholder="dataset">
                        <div class="filename-ext" id="filenameExt">.json</div>
                    </div>
                </div>

                <!-- Columns -->
                <div class="form-group">
                    <label>Columns</label>
                    <div class="columns-list" id="columnsList"></div>
                    <button type="button" class="btn-add" id="addColumnBtn" style="margin-top:8px">+ Add column</button>
                </div>

                <button type="submit" class="btn-generate" id="generateBtn">
                    <span class="spinner"></span>
                    <span class="btn-text">Generate</span>
                </button>

                <div class="status" id="status"></div>
            </form>
        </div>
    </div>

    <!-- Defaults reference -->
    <div class="section">
        <div class="section-header">Value sources</div>
        <div class="section-body" style="padding:0">
            <table class="defaults-table" id="defaultsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody id="defaultsBody">
                    <tr><td colspan="2" style="color:var(--text-muted)">Loading…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$api_base = getenv('API_HOST') ?: 'http://localhost:5000';
?>
<script>
const API_BASE = '<?php echo $api_base; ?>';

let defaultSources = [];
let supportedTypes = [];

const EXT_MAP = {
    json: '.json', jsonl: '.jsonl', csv: '.csv', tsv: '.tsv',
    sql: '.sql', xml: '.xml', yaml: '.yaml'
};

// -----------------------------------------------------------------------
// Boot
// -----------------------------------------------------------------------
async function init() {
    try {
        const [defRes, typRes] = await Promise.all([
            fetch(`${API_BASE}/api/defaults`),
            fetch(`${API_BASE}/api/types`),
        ]);
        defaultSources = await defRes.json();
        supportedTypes = await typRes.json();
    } catch (e) {
        defaultSources = [
            {"name": "default-nums-asc",   "description": "Ascending numbers 1…N"},
            {"name": "default-nums-desc",  "description": "Descending numbers N…1"},
            {"name": "default-nums-rand",  "description": "Random integers in [1, N]"},
            {"name": "default-az",         "description": "a, b, …, z (cycled)"},
            {"name": "default-za",         "description": "z, y, …, a (cycled)"},
            {"name": "default-AZ",         "description": "A, B, …, Z (cycled)"},
            {"name": "default-ZA",         "description": "Z, Y, …, A (cycled)"},
            {"name": "default-aZ",         "description": "a-z then A-Z (cycled)"},
            {"name": "default-Az",         "description": "A-Z then a-z (cycled)"},
            {"name": "default-zA",         "description": "z-a then Z-A (cycled)"},
            {"name": "default-Za",         "description": "Z-A then z-a (cycled)"},
            {"name": "default-az-rand",    "description": "Random lowercase letter"},
            {"name": "default-AZ-rand",    "description": "Random uppercase letter"},
            {"name": "default-Az-rand",    "description": "Random letter (mixed case)"},
            {"name": "default-access-log", "description": "Random access-log entries generated by flog"},
        ];
        supportedTypes = ['json','jsonl','csv','tsv','sql','xml','yaml'];
        showStatus('Could not reach API — using fallback defaults.', 'error');
    }

    populateTypesDropdown();
    renderDefaultsTable();
    addColumnEntry('id', 'default-nums-asc');

    // Update extension when format changes
    document.getElementById('type').addEventListener('change', updateExt);
    updateExt();

    // Re-verify ranges when row count changes
    document.getElementById('rows').addEventListener('input', () => {
        document.querySelectorAll('.custom-rules').forEach(c => recalcRangeConstraints(c));
    });
}

function updateExt() {
    const fmt = document.getElementById('type').value;
    document.getElementById('filenameExt').textContent = EXT_MAP[fmt] || `.${fmt}`;
}

function populateTypesDropdown() {
    const sel = document.getElementById('type');
    sel.innerHTML = '';
    supportedTypes.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t;
        opt.textContent = t.toUpperCase();
        sel.appendChild(opt);
    });
}

function renderDefaultsTable() {
    const tbody = document.getElementById('defaultsBody');
    tbody.innerHTML = '';
    defaultSources.forEach(d => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><code>${d.name}</code></td><td>${d.description}</td>`;
        tbody.appendChild(tr);
    });
}

// -----------------------------------------------------------------------
// Column builder
// -----------------------------------------------------------------------
function buildValueSelect(selectedValue) {
    let html = '';
    defaultSources.forEach(d => {
        const sel = d.name === selectedValue ? ' selected' : '';
        html += `<option value="${d.name}"${sel}>${d.name}</option>`;
    });
    return html;
}

function addRangeRule(container) {
    const rows = parseInt(document.getElementById('rows').value, 10) || 1;
    const existing = container.querySelectorAll('.custom-rule');
    
    let startFrom = 1;
    if (existing.length > 0) {
        const lastTo = existing[existing.length - 1].querySelector('.rule-to');
        startFrom = (parseInt(lastTo.value, 10) || 0) + 1;
    }

    if (startFrom > rows) return; // Cannot add beyond total rows

    const rule = document.createElement('div');
    rule.className = 'custom-rule';

    rule.innerHTML = `
        <input type="number" class="rule-from" min="${startFrom}" max="${rows}" value="${startFrom}" placeholder="From">
        <input type="number" class="rule-to" min="${startFrom}" max="${rows}" value="${Math.min(startFrom + 9, rows)}" placeholder="To">
        <input type="text" class="rule-value" placeholder="Value">
        <button type="button" class="btn-remove" title="Remove">&times;</button>
    `;

    const fromInput = rule.querySelector('.rule-from');
    const toInput = rule.querySelector('.rule-to');

    fromInput.addEventListener('change', () => recalcRangeConstraints(container));
    toInput.addEventListener('change', () => recalcRangeConstraints(container));

    rule.querySelector('.btn-remove').addEventListener('click', () => {
        rule.remove();
        recalcRangeConstraints(container);
    });

    container.appendChild(rule);
    recalcRangeConstraints(container);
}

function recalcRangeConstraints(container) {
    const rows = parseInt(document.getElementById('rows').value, 10) || 1;
    const rules = container.querySelectorAll('.custom-rule');
    let nextMin = 1;

    rules.forEach((rule, idx) => {
        const fromInput = rule.querySelector('.rule-from');
        const toInput = rule.querySelector('.rule-to');

        // From constraints
        fromInput.min = nextMin;
        fromInput.max = rows;
        let fVal = parseInt(fromInput.value, 10) || nextMin;
        if (fVal < nextMin) fVal = nextMin;
        if (fVal > rows) fVal = rows;
        fromInput.value = fVal;

        // To constraints
        toInput.min = fVal;
        toInput.max = rows;
        let tVal = parseInt(toInput.value, 10) || fVal;
        if (tVal < fVal) tVal = fVal;
        if (tVal > rows) tVal = rows;
        toInput.value = tVal;

        nextMin = tVal + 1;

        // Visual feedback if rule is completely outside range (shouldn't happen with above logic but good to have)
        rule.style.opacity = (fVal > rows) ? '0.5' : '1';
    });

    // Handle "Add range" button visibility/state
    const addBtn = container.closest('.value-custom').querySelector('.btn-add-rule');
    if (nextMin > rows) {
        addBtn.disabled = true;
        addBtn.style.opacity = '0.5';
        addBtn.textContent = 'Limit reached';
    } else {
        addBtn.disabled = false;
        addBtn.style.opacity = '1';
        addBtn.textContent = '+ Add range';
    }
}

function addColumnEntry(key = '', value = 'default-nums-asc') {
    const list = document.getElementById('columnsList');
    const div = document.createElement('div');
    div.className = 'column-entry';
    div.dataset.sourceType = 'default';
    div.dataset.fileFlags = '';

    div.innerHTML = `
        <div class="column-entry-top">
            <div>
                <label>Key</label>
                <input type="text" class="col-key" value="${key}" placeholder="column_name" required>
            </div>
            <div>
                <label>Source</label>
                <div class="toggle-group">
                    <button type="button" class="toggle-default active">Default</button>
                    <button type="button" class="toggle-file">File</button>
                    <button type="button" class="toggle-custom">Custom</button>
                </div>
            </div>
            <button type="button" class="btn-remove" title="Remove">&times;</button>
        </div>
        <div class="column-entry-value">
            <div class="value-default">
                <label>Value</label>
                <select class="col-value">${buildValueSelect(value)}</select>
            </div>
            <div class="value-file" style="display:none">
                <label>Value</label>
                <input type="file" class="col-file" accept=".txt,.csv,.log,.list,text/plain">
                <div class="file-options">
                    <span>Options:</span>
                    <div class="opt-toggle">
                        <button type="button" class="flag-btn" data-flag="cycle" title="Repeat lines to fill all rows">Cycle</button>
                        <button type="button" class="flag-btn" data-flag="random" title="Shuffle values">Random</button>
                    </div>
                </div>
            </div>
            <div class="value-custom" style="display:none">
                <label>Value <span style="opacity:0.5">(specify values for row ranges)</span></label>
                <div class="custom-rules-header">
                    <div>From</div>
                    <div>To</div>
                    <div>Value</div>
                    <div></div>
                </div>
                <div class="custom-rules"></div>
                <button type="button" class="btn-add-rule">+ Add range</button>
            </div>
        </div>
    `;

    // Source toggle
    const btnDef = div.querySelector('.toggle-default');
    const btnFile = div.querySelector('.toggle-file');
    const btnCustom = div.querySelector('.toggle-custom');
    const valDef = div.querySelector('.value-default');
    const valFile = div.querySelector('.value-file');
    const valCustom = div.querySelector('.value-custom');
    const allToggles = [btnDef, btnFile, btnCustom];
    const allPanels = {default: valDef, file: valFile, custom: valCustom};

    function setSource(type) {
        div.dataset.sourceType = type;
        allToggles.forEach(b => b.classList.remove('active'));
        Object.values(allPanels).forEach(p => p.style.display = 'none');
        if (type === 'default') { btnDef.classList.add('active'); valDef.style.display = ''; }
        if (type === 'file')    { btnFile.classList.add('active'); valFile.style.display = ''; }
        if (type === 'custom')  { btnCustom.classList.add('active'); valCustom.style.display = ''; }
    }

    btnDef.addEventListener('click', () => setSource('default'));
    btnFile.addEventListener('click', () => setSource('file'));
    btnCustom.addEventListener('click', () => setSource('custom'));

    // Custom range builder
    const rulesContainer = div.querySelector('.custom-rules');
    div.querySelector('.btn-add-rule').addEventListener('click', () => addRangeRule(rulesContainer));
    addRangeRule(rulesContainer);  // start with one rule

    // File flag toggles (independent)
    div.querySelectorAll('.flag-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            const active = [];
            div.querySelectorAll('.flag-btn.active').forEach(b => active.push(b.dataset.flag));
            div.dataset.fileFlags = active.join('+');
        });
    });

    // Remove
    div.querySelector('.btn-remove').addEventListener('click', () => {
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 150);
    });

    list.appendChild(div);
}

document.getElementById('addColumnBtn').addEventListener('click', () => addColumnEntry());

// -----------------------------------------------------------------------
// Generate
// -----------------------------------------------------------------------
document.getElementById('generatorForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = document.getElementById('generateBtn');
    const entries = document.querySelectorAll('.column-entry');

    const keys = [];
    const values = [];
    const files = {};
    const fileModes = {};
    const customRules = {};
    let fileIndex = 0;
    let customIndex = 0;

    for (const entry of entries) {
        const k = entry.querySelector('.col-key').value.trim();
        if (!k) {
            showStatus('Please fill in all column key names.', 'error');
            return;
        }
        keys.push(k);

        if (entry.dataset.sourceType === 'file') {
            const fileInput = entry.querySelector('.col-file');
            if (!fileInput.files || fileInput.files.length === 0) {
                showStatus(`Select a file for column "${k}".`, 'error');
                return;
            }
            files[fileIndex] = fileInput.files[0];
            fileModes[fileIndex] = entry.dataset.fileFlags || '';
            values.push(`file:${fileIndex}`);
            fileIndex++;
        } else if (entry.dataset.sourceType === 'custom') {
            const rules = [];
            entry.querySelectorAll('.custom-rule').forEach(ruleEl => {
                const from = parseInt(ruleEl.querySelector('.rule-from').value, 10);
                const to = parseInt(ruleEl.querySelector('.rule-to').value, 10);
                const val = ruleEl.querySelector('.rule-value').value;
                if (!isNaN(from) && !isNaN(to) && val !== '') {
                    rules.push({from, to, value: val});
                }
            });
            customRules[customIndex] = rules;
            values.push(`custom:${customIndex}`);
            customIndex++;
        } else {
            values.push(entry.querySelector('.col-value').value.trim());
        }
    }

    if (keys.length === 0) {
        showStatus('Add at least one column.', 'error');
        return;
    }

    const rows = parseInt(document.getElementById('rows').value, 10);
    if (!rows || rows <= 0) {
        showStatus('Row count must be a positive integer.', 'error');
        return;
    }

    btn.classList.add('loading');
    btn.disabled = true;
    hideStatus();

    const fmt = document.getElementById('type').value;
    const tableName = document.getElementById('tableName').value || 'data';
    const userFilename = document.getElementById('filename').value.trim() || 'dataset';

    try {
        let res;
        const hasFiles = Object.keys(files).length > 0 || Object.keys(customRules).length > 0;

        if (hasFiles) {
            const formData = new FormData();
            formData.append('rows', rows);
            formData.append('keys', keys.join(','));
            formData.append('values', values.join(','));
            formData.append('type', fmt);
            formData.append('name', tableName);
            const modeEntries = Object.entries(fileModes)
                .filter(([_, m]) => m)
                .map(([i, m]) => `${i}:${m}`);
            if (modeEntries.length) formData.append('file_modes', modeEntries.join(','));
            if (Object.keys(customRules).length) formData.append('custom_rules', JSON.stringify(customRules));
            for (const [idx, file] of Object.entries(files)) {
                formData.append(`file_${idx}`, file);
            }
            res = await fetch(`${API_BASE}/api/generate`, {
                method: 'POST',
                body: formData,
            });
        } else {
            res = await fetch(`${API_BASE}/api/generate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    rows, keys: keys.join(','), values: values.join(','),
                    type: fmt, name: tableName,
                }),
            });
        }

        if (!res.ok) {
            const err = await res.json();
            showStatus(err.error || 'Generation failed.', 'error');
            return;
        }

        const ext = EXT_MAP[fmt] || `.${fmt}`;
        const filename = userFilename + ext;

        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);

        showStatus(`Generated ${rows} rows → ${filename}`, 'success');
    } catch (err) {
        showStatus(`Request failed: ${err.message}`, 'error');
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
});

// -----------------------------------------------------------------------
// Status
// -----------------------------------------------------------------------
function showStatus(msg, type) {
    const el = document.getElementById('status');
    el.textContent = msg;
    el.className = `status ${type}`;
}

function hideStatus() {
    document.getElementById('status').className = 'status';
}

init();
</script>

</body>
</html>
