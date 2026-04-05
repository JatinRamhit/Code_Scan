# config_loader.py
import json, pathlib, os

ALLOWED_DIR = pathlib.Path("/var/app/configs").resolve()
ALLOWED_EXT = {".json"}
MAX_BYTES   = 64 * 1024

def load_config(user_path: str) -> dict:
    target = (ALLOWED_DIR / user_path).resolve()

    # Confine to allowed directory
    try:
        target.relative_to(ALLOWED_DIR)
    except ValueError:
        raise PermissionError("Config file is outside the allowed directory.")

    # Allow only .json — never .py, .pkl, .yaml with !!python/object
    if target.suffix.lower() not in ALLOWED_EXT:
        raise ValueError(f"File type '{target.suffix}' not permitted.")

    if not target.is_file():
        raise FileNotFoundError(f"Config not found: {user_path}")

    if target.stat().st_size > MAX_BYTES:
        raise ValueError("Config file exceeds 64 KB limit.")

    raw = target.read_text(encoding="utf-8")
    config = json.loads(raw)   # json.loads cannot execute code

    if not isinstance(config, dict):
        raise ValueError("Config must be a JSON object.")

    return config

if __name__ == "__main__":
    path = input("Config filename: ").strip()
    try:
        cfg = load_config(path)
        print("Loaded config:", cfg)
    except Exception as e:
        print(f"Error: {e}")
