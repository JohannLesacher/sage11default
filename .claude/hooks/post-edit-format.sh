#!/usr/bin/env bash
# Hook PostToolUse: auto-format file after Write/Edit
# Reads the modified file from JSON input and applies the correct formatter.
# Exit 0 always (non-blocking).

# Do not use set -euo pipefail: the script must ALWAYS exit with 0
# even if variables are empty or commands fail.

# Read JSON input from stdin
INPUT=$(cat 2>/dev/null || true)

# Determine the project dir: CLAUDE_PROJECT_DIR if available, otherwise
# walk up from the script location (.claude/hooks/ → project root)
PROJECT_DIR="${CLAUDE_PROJECT_DIR:-}"
if [[ -z "$PROJECT_DIR" ]]; then
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
fi

# Extract the modified file path from JSON input
FILE_PATH=$(echo "$INPUT" | grep -o '"file_path"[[:space:]]*:[[:space:]]*"[^"]*"' | head -1 | sed 's/.*"file_path"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/' 2>/dev/null || true)

if [[ -z "$FILE_PATH" || ! -f "$FILE_PATH" ]]; then
    exit 0
fi

# Determine the file type and apply the formatter
case "$FILE_PATH" in
    *.blade.php|*.php)
        if [[ -f "$PROJECT_DIR/vendor/bin/pint" ]]; then
            "$PROJECT_DIR/vendor/bin/pint" "$FILE_PATH" --quiet 2>&1 || echo "Warning: pint formatting failed on $FILE_PATH" >&2
        elif [[ -f "$PROJECT_DIR/vendor/bin/phpcbf" ]]; then
            "$PROJECT_DIR/vendor/bin/phpcbf" "$FILE_PATH" --quiet 2>&1 || echo "Warning: phpcbf formatting failed on $FILE_PATH" >&2
        fi
        ;;
    *.css|*.js|*.ts|*.json|*.html)
        if command -v npx &>/dev/null; then
            npx --yes prettier --write "$FILE_PATH" 2>&1 || echo "Warning: prettier formatting failed on $FILE_PATH" >&2
        fi
        ;;
esac

exit 0
