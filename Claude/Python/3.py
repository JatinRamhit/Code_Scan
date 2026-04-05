# raw_sql_login.py  — INSECURE: demonstrates what NOT to do
import sqlite3

conn = sqlite3.connect("users.db")

def login_unsafe(username: str, password: str):
    # ❌ VULNERABLE: f-string interpolation allows SQL injection
    #    e.g. username = "admin' --" bypasses the password check
    query = f"SELECT * FROM users WHERE username='{username}' AND password='{password}'"
    cursor = conn.execute(query)
    return cursor.fetchone()

def login_safe(username: str, password: str):
    # ✔ Parameterized query — the DB driver handles escaping
    cursor = conn.execute(
        "SELECT * FROM users WHERE username = ? AND password = ?",
        (username, password)
    )
    return cursor.fetchone()

if __name__ == "__main__":
    u = input("Username: ")
    p = input("Password: ")
    row = login_safe(u, p)
    print("Logged in:", row if row else "Invalid credentials")
