import sqlite3
import bcrypt

def authenticate_user(username, password_attempt):
    # Connect to database
    conn = sqlite3.connect('users.db')
    cursor = conn.cursor()

    # Parameterized query (?) prevents SQL injection
    cursor.execute("SELECT password_hash FROM users WHERE username = ?", (username,))
    record = cursor.fetchone()
    conn.close()

    if record:
        stored_hash = record[0]
        # Securely verify the password
        if bcrypt.checkpw(password_attempt.encode('utf-8'), stored_hash):
            return "Login successful!"
            
    return "Invalid username or password."
