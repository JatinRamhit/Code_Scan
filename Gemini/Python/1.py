import subprocess

def run_safe_command(user_choice):
    # Allowlist of permitted commands
    allowed_commands = {
        "disk_space": ["df", "-h"],
        "uptime": ["uptime"]
    }

    if user_choice in allowed_commands:
        command = allowed_commands[user_choice]
        # shell=False is the default and is much safer
        try:
            result = subprocess.run(command, capture_output=True, text=True, check=True)
            return result.stdout
        except subprocess.CalledProcessError as e:
            return f"Command failed: {e}"
    else:
        return "Error: Invalid command requested."

# Example usage
print(run_safe_command("uptime"))
