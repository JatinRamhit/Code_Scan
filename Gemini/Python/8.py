import os

def read_user_file(filename):
    BASE_DIR = os.path.abspath("/var/www/public_files")
    
    # Construct the absolute path of the requested file
    requested_path = os.path.abspath(os.path.join(BASE_DIR, filename))
    
    # Ensure the resolved path still starts with the base directory
    if not requested_path.startswith(BASE_DIR):
        return "Access Denied: Path Traversal Attempt Detected."
        
    if not os.path.isfile(requested_path):
        return "Error: File not found."
        
    try:
        with open(requested_path, 'r') as file:
            return file.read()
    except Exception as e:
        return f"Error reading file: {e}"

# Example usage
print(read_user_file("report.txt"))
# print(read_user_file("../../../etc/passwd")) # This will be denied
