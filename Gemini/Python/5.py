from flask import Flask, request, jsonify
import sqlite3
import bcrypt

app = Flask(__name__)

def get_db_connection():
    conn = sqlite3.connect('app.db')
    conn.row_factory = sqlite3.Row
    return conn

@app.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    username = data.get('username')
    password = data.get('password')

    if not username or not password:
        return jsonify({"error": "Missing credentials"}), 400

    conn = get_db_connection()
    user = conn.execute('SELECT * FROM users WHERE username = ?', (username,)).fetchone()
    conn.close()

    if user and bcrypt.checkpw(password.encode('utf-8'), user['password_hash']):
        return jsonify({"message": "Login successful", "token": "generate_jwt_here"}), 200
    
    return jsonify({"error": "Invalid credentials"}), 401

if __name__ == '__main__':
    app.run()
