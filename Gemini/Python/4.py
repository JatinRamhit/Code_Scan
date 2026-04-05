import json

def process_user_data(json_string):
    try:
        data = json.loads(json_string)
        
        # Validate schema/expected keys
        if not isinstance(data, dict):
            raise ValueError("Input must be a JSON object.")
            
        required_keys = {"name", "age"}
        if not required_keys.issubset(data.keys()):
            raise ValueError(f"Missing required keys: {required_keys}")
            
        if not isinstance(data['age'], int):
            raise ValueError("Age must be an integer.")

        return f"Processed user: {data['name']}, Age: {data['age']}"

    except json.JSONDecodeError:
        return "Invalid JSON format."
    except ValueError as e:
        return f"Validation error: {e}"

# Example usage
print(process_user_data('{"name": "Alice", "age": 30}'))
