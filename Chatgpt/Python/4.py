import json

def process_data(data):
    if not isinstance(data, dict):
        return {"error": "Expected a JSON object"}

    name = str(data.get("name", "")).strip()
    age = data.get("age")
    hobbies = data.get("hobbies", [])

    if not name:
        return {"error": "Missing or empty 'name'"}
    if not isinstance(age, int) or age < 0:
        return {"error": "Invalid 'age'"}
    if not isinstance(hobbies, list):
        return {"error": "Invalid 'hobbies'"}

    return {
        "name": name.title(),
        "age_next_year": age + 1,
        "hobby_count": len(hobbies)
    }

if __name__ == "__main__":
    raw = input("Enter JSON: ")

    try:
        parsed = json.loads(raw)
        result = process_data(parsed)
        print(json.dumps(result, indent=2))
    except json.JSONDecodeError:
        print(json.dumps({"error": "Invalid JSON"}, indent=2))
