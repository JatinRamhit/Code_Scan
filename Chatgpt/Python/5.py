from flask import Flask, request, session, redirect, url_for, render_template_string
import sqlite3
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.secret_key = "change-this-secret-key"
DB_NAME = "secure_users.db"

HTML = """
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Secure Login</title></head>
<body>
{% if session.get('user') %}
    <h2>Welcome, {{ session['user'] }}</h2>
    <p>You are logged in.</p>
    <a href="{{ url_for('logout') }}">Logout</a>
{% else %}
    <h2>Secure Login</h2>
    <p>Demo account: admin / secret123</p>
    {% if error %}<p style="color:red;">{{ error }}</p>{% endif %}
    <form method="post">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit">Login</button>
    </form>
{% endif %}
</body>
</html>
"""

def get_db():
    return sqlite3.connect(DB_NAME)

def init_db():
    conn = get_db()
    cur = conn.cursor()

    cur.execute("""
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        )
    """)

    cur.execute("SELECT COUNT(*) FROM users WHERE username = ?", ("admin",))
    if cur.fetchone()[0] == 0:
        cur.execute(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)",
            ("admin", generate_password_hash("secret123"))
        )

    conn.commit()
    conn.close()

@app.route("/", methods=["GET", "POST"])
def login():
    error = ""

    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "")

        conn = get_db()
        cur = conn.cursor()
        cur.execute("SELECT id, username, password_hash FROM users WHERE username = ?", (username,))
        row = cur.fetchone()
        conn.close()

        if row and check_password_hash(row[2], password):
            session.clear()
            session["user"] = row[1]
            return redirect(url_for("login"))
        else:
            error = "Invalid username or password."

    return render_template_string(HTML, error=error)

@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))

if __name__ == "__main__":
    init_db()
    app.run(debug=True)
