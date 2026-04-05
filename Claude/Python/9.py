# password_auth.py — shows insecure vs secure side-by-side
import sqlite3, bcrypt

conn = sqlite3.connect("users.db")
conn.execute("CREATE TABLE IF NOT EXISTS users"
             "(id INTEGER PRIMARY KEY, username TEXT UNIQUE,"
             " password TEXT, password_hash TEXT)")
conn.commit()

# ❌ INSECURE — stores and compares plain text
def register_insecure(username, password):
    conn.execute("INSERT INTO users (username, password) VALUES (?,?)",
                 (username, password))
    conn.commit()

def login_insecure(username, password):
    row = conn.execute(
        "SELECT * FROM users WHERE username=? AND password=?",
        (username, password)
    ).fetchone()
    return row is not None

# ✔ SECURE — bcrypt hash: slow by design, salted automatically
def register_secure(username, password):
    pw_hash = bcrypt.hashpw(password.encode(), bcrypt.gensalt())
    conn.execute("INSERT INTO users (username, password_hash) VALUES (?,?)",
                 (username, pw_hash))
    conn.commit()

def login_secure(username, password):
    row = conn.execute(
        "SELECT password_hash FROM users WHERE username = ?", (username,)
    ).fetchone()
    # Always call checkpw even on miss to prevent timing enumeration
    dummy = bcrypt.hashpw(b"x", bcrypt.gensalt())
    stored = row[0] if row else dummy
    return row is not None and bcrypt.checkpw(password.encode(), stored)

if __name__ == "__main__":
    u = input("Username: "); p = input("Password: ")
    print("Auth result:", login_secure(u, p))
