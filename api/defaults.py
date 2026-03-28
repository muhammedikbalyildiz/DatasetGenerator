"""
defaults.py — Default value generators for DatasetGeneratorTool.

Each generator function accepts `row_count` (int) and returns a list[str] of that length.
The `resolve_default` dispatcher maps default-* names to the appropriate generator.
"""

import random
import string
import subprocess
import sys
import itertools

def nums_asc(row_count: int) -> list[str]:
    return [str(i) for i in range(1, row_count + 1)]


def nums_desc(row_count: int) -> list[str]:
    return [str(i) for i in range(row_count, 0, -1)]


def nums_rand(row_count: int) -> list[str]:
    return [str(random.randint(1, row_count)) for _ in range(row_count)]


def _cycle_to(seq: list[str], n: int) -> list[str]:
    return list(itertools.islice(itertools.cycle(seq), n))


def az(row_count: int) -> list[str]:
    return _cycle_to(list(string.ascii_lowercase), row_count)


def za(row_count: int) -> list[str]:
    return _cycle_to(list(reversed(string.ascii_lowercase)), row_count)


def AZ(row_count: int) -> list[str]:
    return _cycle_to(list(string.ascii_uppercase), row_count)


def ZA(row_count: int) -> list[str]:
    return _cycle_to(list(reversed(string.ascii_uppercase)), row_count)


def aZ(row_count: int) -> list[str]:
    seq = list(string.ascii_lowercase) + list(string.ascii_uppercase)
    return _cycle_to(seq, row_count)


def Az(row_count: int) -> list[str]:
    seq = list(string.ascii_uppercase) + list(string.ascii_lowercase)
    return _cycle_to(seq, row_count)


def zA(row_count: int) -> list[str]:
    seq = list(reversed(string.ascii_lowercase)) + list(reversed(string.ascii_uppercase))
    return _cycle_to(seq, row_count)


def Za(row_count: int) -> list[str]:
    seq = list(reversed(string.ascii_uppercase)) + list(reversed(string.ascii_lowercase))
    return _cycle_to(seq, row_count)


def az_rand(row_count: int) -> list[str]:
    return [random.choice(string.ascii_lowercase) for _ in range(row_count)]


def AZ_rand(row_count: int) -> list[str]:
    return [random.choice(string.ascii_uppercase) for _ in range(row_count)]


def Az_rand(row_count: int) -> list[str]:
    return [random.choice(string.ascii_letters) for _ in range(row_count)]


def access_log(row_count: int) -> list[str]:
    lines: list[str] = []
    max_attempts = 50  # safety valve

    for _ in range(max_attempts):
        try:
            result = subprocess.run(
                ["flog", "-n", str(row_count)],
                capture_output=True, text=True, check=True,
            )
        except FileNotFoundError:
            print("Error: 'flog' is not installed or not on PATH.", file=sys.stderr)
            print("Install it (e.g. `go install github.com/mingrammer/flog@latest`) and try again.", file=sys.stderr)
            sys.exit(1)
        except subprocess.CalledProcessError as exc:
            print(f"Error: flog exited with code {exc.returncode}: {exc.stderr}", file=sys.stderr)
            sys.exit(1)

        for line in result.stdout.splitlines():
            # Skip inappropriate content
            if not any(w in line.lower() for w in ("sex", "porn", "cum")):
                lines.append(line)
            if len(lines) >= row_count:
                break

        if len(lines) >= row_count:
            break

    return lines[:row_count]


_REGISTRY: dict[str, callable] = {
    "default-nums-asc":    nums_asc,
    "default-nums-desc":   nums_desc,
    "default-nums-rand":   nums_rand,
    "default-az":          az,
    "default-za":          za,
    "default-AZ":          AZ,
    "default-ZA":          ZA,
    "default-aZ":          aZ,
    "default-Az":          Az,
    "default-zA":          zA,
    "default-Za":          Za,
    "default-az-rand":     az_rand,
    "default-AZ-rand":     AZ_rand,
    "default-Az-rand":     Az_rand,
    "default-access-log":  access_log,
}


def resolve_default(name: str, row_count: int) -> list[str]:
    fn = _REGISTRY.get(name)
    if fn is None:
        print(f"Error: Unknown default value '{name}'.", file=sys.stderr)
        print(f"Available defaults: {', '.join(sorted(_REGISTRY.keys()))}", file=sys.stderr)
        sys.exit(1)
    return fn(row_count)
