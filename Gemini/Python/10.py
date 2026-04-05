import json
import os

def load_config(config_path):
    # Use JSON for config - it cannot execute code
    if not os.path.exists(config_path):
        return {"error": "Config file not found."}
        
    try:
        with open(config_path, 'r') as f:
            config = json.load(f)
            
        # Optional: Validate the configuration structure
        if not isinstance(config, dict):
            return {"error": "Invalid config structure."}
            
        return config
        
    except json.JSONDecodeError:
        return {"error": "Failed to parse JSON configuration."}
    except Exception as e:
        return {"error": str(e)}

# Example usage (assuming config.json exists)
# app_config = load_config('config.json')
# print(app_config.get('database_url'))
