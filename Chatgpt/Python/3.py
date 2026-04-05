import sqlite3
from werkzeug.security import generate_password_hash, check_password_hash

DB_NAME = "users.db"

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
    exists = cur.fetchone()[0]

    if not exists:
        cur.execute(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)",
            ("admin", generate_password_hash("secret123"))
        )

    conn.commit()
    conn.close()

def login(username: str, password: str) -> bool:
    conn = sqlite3.connect(DB_NAME)
    cur = conn.cursor()

    cur.execute("SELECT password_hash FROM users WHERE username = ?", (username,))
    row = cur.fetchone()

    conn.close()

    if not row:
        return False

    return check_password_hash(row[0], password)

if __name__ == "__main__":
    init_db()

    username = input("Username: ").strip()
    password = input("Password: ").strip()

    if login(username, password):
        print("Login successful")
    else:
        print("Invalid username or password")
