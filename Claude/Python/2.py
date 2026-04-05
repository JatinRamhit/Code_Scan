# flask_file_api.py
from flask import Flask, request, abort
import os, pathlib

app = Flask(__name__)

# Only files inside this directory may be served
SAFE_ROOT = pathlib.Path("/var/app/public_files").resolve()

@app.route("/read")
def read_file():
    filename = request.args.get("file", "")

    if not filename:
        abort(400, "Missing 'file' parameter.")

    # Resolve the full path and confirm it stays inside SAFE_ROOT
    requested = (SAFE_ROOT / filename).resolve()

    try:
        requested.relative_to(SAFE_ROOT)
    except ValueError:
        abort(403, "Access denied — path traversal detected.")

    if not requested.is_file():
        abort(404, "File not found.")

    return requested.read_text(encoding="utf-8")

if __name__ == "__main__":
    app.run(debug=False)
