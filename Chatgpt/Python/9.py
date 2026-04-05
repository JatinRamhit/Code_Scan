import sqlite3
from werkzeug.security import generate_password_hash, check_password_hash

DB_NAME = "auth.db"

def init_db():
    conn = sqlite3.connect(DB_NAME)
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

def authenticate(username: str, password: str) -> bool:
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()
    cur.execute("SELECT password_hash FROM users WHERE username = ?", (username,))
    row = cur.fetchone()
    conn.close()

    return bool(row and check_password_hash(row[0], password))

if __name__ == "__main__":
    init_db()

    username = input("Username: ").strip()
    password = input("Password: ").strip()

    if authenticate(username, password):
        print("Authentication successful")
    else:
        print("Authentication failed")
