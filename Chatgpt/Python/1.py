import platform
import shutil
from datetime import datetime

def run_safe_action(action: str) -> str:
    action = action.strip().lower()

    if action == "python_version":
        return platform.python_version()
    elif action == "system":
        return platform.platform()
    elif action == "time":
        return datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    elif action == "disk_free":
        usage = shutil.disk_usage(".")
        return f"Free space: {usage.free // (1024 * 1024)} MB"
    else:
        return "Unknown action."

if __name__ == "__main__":
    print("Allowed actions: python_version, system, time, disk_free")
    user_input = input("Enter action: ")
    result = run_safe_action(user_input)
    print("Result:", result)
