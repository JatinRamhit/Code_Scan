import json
from pathlib import Path

CONFIG_DIR = Path("configs").resolve()
CONFIG_DIR.mkdir(exist_ok=True)

sample_config = CONFIG_DIR / "app_config.json"
if not sample_config.exists():
    sample_config.write_text(
        json.dumps({
            "app_name": "DemoApp",
            "debug": False,
            "max_connections": 10
        }, indent=2),
        encoding="utf-8"
    )

def load_config(filename: str) -> dict:
    target = (CONFIG_DIR / filename).resolve()

    if not str(target).startswith(str(CONFIG_DIR)):
        raise PermissionError("Access denied")

    if not target.is_file():
        raise FileNotFoundError("Config file not found")

    with target.open("r", encoding="utf-8") as f:
        data = json.load(f)

    if not isinstance(data, dict):
        raise ValueError("Config must be a JSON object")

    allowed_keys = {"app_name", "debug", "max_connections"}
    cleaned = {}

    for key, value in data.items():
        if key in allowed_keys:
            cleaned[key] = value

    return cleaned

if __name__ == "__main__":
    filename = input("Enter config filename inside configs/: ").strip()

    try:
        config = load_config(filename)
        print("Loaded configuration:")
        print(json.dumps(config, indent=2))
    except Exception as e:
        print("Error:", str(e))
