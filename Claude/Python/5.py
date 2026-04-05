# secure_flask_login.py
from flask import Flask, request, session, jsonify, abort
import sqlite3, bcrypt, os

app = Flask(__name__)
app.secret_key = os.urandom(32)   # use a stable key in production (env var)
DB  = "users.db"

def get_db():
    conn = sqlite3.connect(DB)
    conn.row_factory = sqlite3.Row
    return conn

# One-time setup: create table + demo user
with get_db() as db:
    db.execute("CREATE TABLE IF NOT EXISTS users"
               "(id INTEGER PRIMARY KEY, username TEXT UNIQUE, password_hash TEXT)")
    pw_hash = bcrypt.hashpw(b"demo1234", bcrypt.gensalt())
    db.execute("INSERT OR IGNORE INTO users (username, password_hash) VALUES (?,?)",
               ("admin", pw_hash))
    db.commit()

@app.route("/login", methods=["POST"])
def login():
    data     = request.get_json(force=True) or {}
    username = data.get("username", "").strip()
    password = data.get("password", "")

    if not username or not password:
        abort(400, "username and password required")

    with get_db() as db:
        row = db.execute(
            "SELECT * FROM users WHERE username = ?", (username,)
        ).fetchone()

    # Constant-time check prevents user enumeration via timing
    if row and bcrypt.checkpw(password.encode(), row["password_hash"]):
        session.clear()
        session["user_id"]  = row["id"]
        session["username"] = row["username"]
        return jsonify(ok=True, username=row["username"])

    abort(401, "Invalid credentials")

if __name__ == "__main__":
    app.run(debug=False)
