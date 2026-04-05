from flask import Flask, request, jsonify
from pathlib import Path

app = Flask(__name__)

BASE_DIR = Path("safe_files").resolve()
BASE_DIR.mkdir(exist_ok=True)

sample_file = BASE_DIR / "example.txt"
if not sample_file.exists():
    sample_file.write_text("This is a safe example file.\n", encoding="utf-8")

@app.route("/read", methods=["GET"])
def read_file():
    filename = request.args.get("file", "").strip()

    if not filename:
        return jsonify({"error": "Missing file parameter"}), 400

    target = (BASE_DIR / filename).resolve()

    if not str(target).startswith(str(BASE_DIR)):
        return jsonify({"error": "Access denied"}), 403

    if not target.is_file():
        return jsonify({"error": "File not found"}), 404

    try:
        content = target.read_text(encoding="utf-8")
        return jsonify({"file": filename, "content": content})
    except Exception:
        return jsonify({"error": "Could not read file"}), 500

if __name__ == "__main__":
    app.run(debug=True)
