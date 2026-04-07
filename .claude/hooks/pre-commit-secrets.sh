#!/usr/bin/env bash
# Hook PreToolUse: detects secrets in files before git commit.
# Activates only on "git commit" commands. Scans staged files
# looking for API key, token, password, and private key patterns.
# If match found: prints {"decision":"block","reason":"..."} and exit 0.
# If clean: prints {"decision":"allow"} and exit 0.

# Do not use set -euo pipefail: the script must ALWAYS exit with 0

# Read JSON input from stdin
INPUT=$(cat 2>/dev/null || true)

# Extract the bash command from JSON input
COMMAND=$(echo "$INPUT" | grep -o '"command"[[:space:]]*:[[:space:]]*"[^"]*"' | head -1 | sed 's/.*"command"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/' 2>/dev/null || true)

# Activate only for git commit commands
if ! echo "$COMMAND" | grep -qE 'git\s+commit'; then
    echo '{"decision":"allow"}'
    exit 0
fi

# Determine the project dir
PROJECT_DIR="${CLAUDE_PROJECT_DIR:-}"
if [[ -z "$PROJECT_DIR" ]]; then
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
fi

# Get staged files
STAGED_FILES=$(cd "$PROJECT_DIR" && git diff --cached --name-only 2>/dev/null || true)

if [[ -z "$STAGED_FILES" ]]; then
    echo '{"decision":"allow"}'
    exit 0
fi

# Secret patterns to look for
SECRET_PATTERNS=(
    'AKIA[0-9A-Z]{16}'                          # AWS Access Key
    'sk-[a-zA-Z0-9]{20,}'                       # OpenAI / Stripe key
    'ghp_[a-zA-Z0-9]{36}'                       # GitHub PAT
    'glpat-[a-zA-Z0-9\-]{20,}'                  # GitLab PAT
    '-----BEGIN (RSA |EC |DSA )?PRIVATE KEY-----' # Private key
    'xox[bprs]-[a-zA-Z0-9\-]{10,}'              # Slack token
)

FOUND_SECRETS=""

while IFS= read -r file; do
    # Skip binary files and lock files
    case "$file" in
        *.lock|*.min.js|*.min.css|*.map|*.woff*|*.ttf|*.eot|*.png|*.jpg|*.gif|*.ico|*.svg)
            continue
            ;;
    esac

    # Check if the file exists
    FULL_PATH="$PROJECT_DIR/$file"
    if [[ ! -f "$FULL_PATH" ]]; then
        continue
    fi

    for pattern in "${SECRET_PATTERNS[@]}"; do
        MATCH=$(grep -nE "$pattern" "$FULL_PATH" 2>/dev/null | head -3 || true)
        if [[ -n "$MATCH" ]]; then
            FOUND_SECRETS="${FOUND_SECRETS}\n- ${file}: pattern '${pattern}'"
        fi
    done
done <<< "$STAGED_FILES"

# Result
if [[ -n "$FOUND_SECRETS" ]]; then
    # Remove newlines for JSON
    CLEAN_SECRETS=$(echo -e "$FOUND_SECRETS" | tr '\n' ' ' | sed 's/"/\\"/g')
    echo "{\"decision\":\"block\",\"reason\":\"Possible secrets found in staged files:${CLEAN_SECRETS}Remove secrets before committing.\"}"
else
    echo '{"decision":"allow"}'
fi

exit 0
