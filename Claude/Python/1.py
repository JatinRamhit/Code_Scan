# system_cmd.py
import subprocess

# Allowlist — never pass arbitrary user strings to shell
ALLOWED = {
    "list":  ["ls", "-lh"],
    "date":  ["date"],
    "uptime":["uptime"],
    "who":   ["who"],
}

def run_command(choice: str) -> str:
    cmd = ALLOWED.get(choice)
    if not cmd:
        return f"Unknown command: {choice!r}. Allowed: {list(ALLOWED)}"
    # shell=False + list args prevents injection entirely
    result = subprocess.run(cmd, capture_output=True, text=True,
                              timeout=5, shell=False)
    return result.stdout or result.stderr

if __name__ == "__main__":
    choice = input("Command (list/date/uptime/who): ").strip()
    print(run_command(choice))
