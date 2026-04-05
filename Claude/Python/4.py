# json_processor.py
import json, sys
from typing import Any

REQUIRED_KEYS = {"name": str, "age": int, "email": str}
MAX_INPUT_BYTES = 64 * 1024  # 64 KB limit

def parse_and_validate(raw: str) -> dict[str, Any]:
    if len(raw.encode()) > MAX_INPUT_BYTES:
        raise ValueError("Input exceeds 64 KB limit.")

    try:
        data = json.loads(raw)
    except json.JSONDecodeError as e:
        raise ValueError(f"Invalid JSON: {e}")

    if not isinstance(data, dict):
        raise ValueError("Expected a JSON object at top level.")

    for key, expected_type in REQUIRED_KEYS.items():
        if key not in data:
            raise ValueError(f"Missing required field: '{key}'")
        if not isinstance(data[key], expected_type):
            raise ValueError(f"Field '{key}' must be {expected_type.__name__}")

    if not (0 < data["age"] < 130):
        raise ValueError("Age must be between 1 and 129.")

    return data

if __name__ == "__main__":
    raw = sys.stdin.read() if not sys.stdin.isatty() else \
          input("Paste JSON: ")
    try:
        result = parse_and_validate(raw)
        print("Valid record:", result)
    except ValueError as e:
        print(f"Error: {e}", file=sys.stderr)
