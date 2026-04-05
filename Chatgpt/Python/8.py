from pathlib import Path

BASE_DIR = Path("safe_files").resolve()
BASE_DIR.mkdir(exist_ok=True)

sample_file = BASE_DIR / "notes.txt"
if not sample_file.exists():
    sample_file.write_text("Sample safe file content.\n", encoding="utf-8")

def read_safe_file(filename: str) -> str:
    target = (BASE_DIR / filename).resolve()

    if not str(target).startswith(str(BASE_DIR)):
        raise PermissionError("Access denied")

    if not target.is_file():
        raise FileNotFoundError("File not found")

    return target.read_text(encoding="utf-8")

if __name__ == "__main__":
    filename = input("Enter filename inside safe_files/: ").strip()

    try:
        content = read_safe_file(filename)
        print("\n--- File Content ---")
        print(content)
    except Exception as e:
        print("Error:", str(e))
