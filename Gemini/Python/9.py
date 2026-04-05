import bcrypt

# --- Registration Phase ---
def create_user(password):
    # Hash a password for the first time
    # gensalt() generates a random salt, hashpw hashes the password + salt
    hashed_password = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt())
    return hashed_password

# --- Login Phase ---
def authenticate(password_attempt, stored_hash):
    # checkpw securely compares the plaintext attempt against the stored hash
    if bcrypt.checkpw(password_attempt.encode('utf-8'), stored_hash):
        return True
    return False

# Example
stored_db_hash = create_user("my_super_secret_password")
print("Login success?", authenticate("my_super_secret_password", stored_db_hash))
print("Login success?", authenticate("wrong_password", stored_db_hash))
