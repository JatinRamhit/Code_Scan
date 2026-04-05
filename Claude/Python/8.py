# file_reader.py
import pathlib

# Confine all access to one directory
ALLOWED_DIR = pathlib.Path("/var/app/readable").resolve()
MAX_BYTES   = 1 * 1024 * 1024  # 1 MB

def read_file(user_path: str) -> str:
    target = (ALLOWED_DIR / user_path).resolve()

    # Ensure the resolved path is still inside the allowed directory
    try:
        target.relative_to(ALLOWED_DIR)
    except ValueError:
        raise PermissionError(f"Access denied: '{user_path}'")

    if not target.is_file():
        raise FileNotFoundError(f"File not found: '{user_path}'")

    if target.stat().st_size > MAX_BYTES:
        raise ValueError("File exceeds 1 MB read limit.")

    return target.read_text(encoding="utf-8", errors="replace")

if __name__ == "__main__":
    path = input("File path (relative to allowed dir): ").strip()
    try:
        print(read_file(path))
    except (PermissionError, FileNotFoundError, ValueError) as e:
        print(f"Error: {e}")
