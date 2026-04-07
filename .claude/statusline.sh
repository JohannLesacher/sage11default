#!/bin/bash
input=$(cat)
MODEL=$(echo "$input" | jq -r '.model.display_name // "unknown"')
PCT=$(echo "$input" | jq -r '.context_window.used_percentage // 0' | cut -d. -f1)
printf "model: %s | context: %s%%" "$MODEL" "$PCT"