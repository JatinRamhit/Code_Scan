# flask_json_store.py
from flask import Flask, request, jsonify, abort
import sqlite3, html

app = Flask(__name__)
DB  = "records.db"

with sqlite3.connect(DB) as db:
    db.execute("CREATE TABLE IF NOT EXISTS records"
               "(id INTEGER PRIMARY KEY, name TEXT, value TEXT)")

def validate_record(data: dict) -> tuple[str, str]:
    name  = data.get("name",  "")
    value = data.get("value", "")

    if not isinstance(name, str) or not isinstance(value, str):
        raise ValueError("'name' and 'value' must be strings.")
    if not name.strip():
        raise ValueError("'name' must not be empty.")
    if len(name) > 100 or len(value) > 1000:
        raise ValueError("Field length exceeded.")

    return name.strip(), value.strip()

@app.route("/store", methods=["POST"])
def store():
    data = request.get_json(silent=True)
    if not data:
        abort(400, "Request body must be valid JSON.")

    try:
        name, value = validate_record(data)
    except ValueError as e:
        abort(422, str(e))

    with sqlite3.connect(DB) as db:
        cur = db.execute(
            "INSERT INTO records (name, value) VALUES (?, ?)",
            (name, value)   # parameterized — no string interpolation
        )
        db.commit()

    return jsonify(id=cur.lastrowid, name=name), 201

if __name__ == "__main__":
    app.run(debug=False)
