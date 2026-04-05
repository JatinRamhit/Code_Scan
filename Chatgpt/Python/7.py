from flask import Flask, request, jsonify
import sqlite3

app = Flask(__name__)
DB_NAME = "api_data.db"

def init_db():
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("""
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            message TEXT NOT NULL
        )
    """)
    conn.commit()
    conn.close()

@app.route("/submit", methods=["POST"])
def submit():
    data = request.get_json(silent=True)

    if not isinstance(data, dict):
        return jsonify({"error": "Invalid JSON object"}), 400

    name = str(data.get("name", "")).strip()
    email = str(data.get("email", "")).strip()
    message = str(data.get("message", "")).strip()

    if not name or not email or not message:
        return jsonify({"error": "name, email, and message are required"}), 400

    if "@" not in email or len(email) > 255:
        return jsonify({"error": "Invalid email"}), 400

    if len(message) > 1000:
        return jsonify({"error": "Message too long"}), 400

    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute(
        "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)",
        (name, email, message)
    )
    conn.commit()
    new_id = cur.lastrowid
    conn.close()

    return jsonify({"status": "saved", "id": new_id}), 201

if __name__ == "__main__":
    init_db()
    app.run(debug=True)
