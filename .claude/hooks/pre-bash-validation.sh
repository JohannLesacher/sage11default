#!/usr/bin/env bash
# Hook PreToolUse: blocks dangerous bash commands before execution.
# Receives JSON input from stdin, extracts the command and validates it.
# If dangerous: prints {"decision":"block","reason":"..."} and exit 0.
# If safe: prints {"decision":"allow"} and exit 0.

# Do not use set -euo pipefail: the script must ALWAYS exit with 0
# even if variables are empty or commands fail.

# Read JSON input from stdin
INPUT=$(cat 2>/dev/null || true)

# Extract the bash command from JSON input
COMMAND=$(echo "$INPUT" | grep -o '"command"[[:space:]]*:[[:space:]]*"[^"]*"' | head -1 | sed 's/.*"command"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/' 2>/dev/null || true)

if [[ -z "$COMMAND" ]]; then
    echo '{"decision":"allow"}'
    exit 0
fi

# Dangerous patterns to block
BLOCKED=""

# Destructive deletion
if echo "$COMMAND" | grep -qE 'rm\s+(-[a-zA-Z]*f[a-zA-Z]*\s+)?(-[a-zA-Z]*r[a-zA-Z]*\s+)?(\/|~|\.)($|\s)'; then
    BLOCKED="rm -rf on critical path (/, ~, .)"
fi
if echo "$COMMAND" | grep -qE 'rm\s+(-[a-zA-Z]*r[a-zA-Z]*\s+)?(-[a-zA-Z]*f[a-zA-Z]*\s+)?(\/|~|\.)($|\s)'; then
    BLOCKED="rm -rf on critical path (/, ~, .)"
fi

# Git push force on main/master
if echo "$COMMAND" | grep -qE 'git\s+push\s+.*--force.*\s+(main|master)'; then
    BLOCKED="git push --force on main/master"
fi
if echo "$COMMAND" | grep -qE 'git\s+push\s+.*-f\s+.*\s+(main|master)'; then
    BLOCKED="git push -f on main/master"
fi

# Git reset hard
if echo "$COMMAND" | grep -qE 'git\s+reset\s+--hard'; then
    BLOCKED="git reset --hard can cause data loss"
fi

# Chmod 777
if echo "$COMMAND" | grep -qE 'chmod\s+(-R\s+)?777'; then
    BLOCKED="chmod 777 makes files accessible to everyone"
fi

# Disk destruction
if echo "$COMMAND" | grep -qE '>\s*/dev/sd[a-z]'; then
    BLOCKED="direct write to disk device"
fi
if echo "$COMMAND" | grep -qE 'mkfs\s'; then
    BLOCKED="filesystem formatting"
fi
if echo "$COMMAND" | grep -qE 'dd\s+if='; then
    BLOCKED="dd can overwrite data"
fi

# Pipe to shell (curl/wget | sh/bash)
if echo "$COMMAND" | grep -qE '(curl|wget)\s.*\|\s*(sh|bash|zsh)'; then
    BLOCKED="download and direct execution of remote script"
fi

# Fork bomb
if echo "$COMMAND" | grep -qF ':(){ :|:&};:'; then
    BLOCKED="fork bomb"
fi

# Result
if [[ -n "$BLOCKED" ]]; then
    # Escape for JSON
    REASON="Command blocked: ${BLOCKED}. Run manually if needed."
    echo "{\"decision\":\"block\",\"reason\":\"${REASON}\"}"
else
    echo '{"decision":"allow"}'
fi

exit 0
