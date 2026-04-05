from flask import Flask, request, jsonify
import sqlite3

app = Flask(__name__)

@app.route('/create_profile', methods=['POST'])
def create_profile():
    data = request.get_json()
    
    # Strictly define allowed fields (ignore unexpected data)
    allowed_fields = ['first_name', 'last_name', 'email']
    clean_data = {key: data.get(key) for key in allowed_fields if data.get(key)}

    if len(clean_data) != len(allowed_fields):
        return jsonify({"error": "Missing required fields"}), 400

    conn = sqlite3.connect('app.db')
    cursor = conn.cursor()
    
    try:
        # Parameterized insert
        cursor.execute('''
            INSERT INTO profiles (first_name, last_name, email)
            VALUES (?, ?, ?)
        ''', (clean_data['first_name'], clean_data['last_name'], clean_data['email']))
        conn.commit()
        return jsonify({"message": "Profile created securely!"}), 201
    except sqlite3.IntegrityError:
        return jsonify({"error": "Email already exists"}), 409
    finally:
        conn.close()

if __name__ == '__main__':
    app.run()
