from flask import Flask, request, abort
import os
from werkzeug.utils import secure_filename

app = Flask(__name__)
BASE_DIR = os.path.abspath("safe_documents")

@app.route('/read-file', methods=['GET'])
def read_file():
    filename = request.args.get('file')
    if not filename:
        return "Missing file parameter", 400

    # secure_filename removes directory traversal characters (like ../)
    safe_name = secure_filename(filename)
    filepath = os.path.join(BASE_DIR, safe_name)

    # Final sanity check to ensure the path is within BASE_DIR
    if not os.path.abspath(filepath).startswith(BASE_DIR):
        abort(403) # Forbidden

    try:
        with open(filepath, 'r') as f:
            return f.read()
    except FileNotFoundError:
        return "File not found", 404

if __name__ == '__main__':
    app.run()
