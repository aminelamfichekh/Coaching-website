#!/usr/bin/env bash
# Fetches every URL in /tmp/asset-remote.txt (format: URL|dest-path).
# Uses the Cloudflare-safe User-Agent recipe from
# .claude/skills/wp-to-static-extractor/SKILL.md.
set -u
UA='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
QUEUE="${1:-.asset-queue.tmp}"
FAIL_LOG=/tmp/asset-fetch-fail.log
: > "$FAIL_LOG"

fetch_one() {
  local line="$1"
  local url="${line%%|*}"
  local dst="${line##*|}"
  mkdir -p "$(dirname "$dst")"
  if curl -sSL --max-time 30 --fail \
      -A "$UA" \
      -H "Accept: text/html,application/xhtml+xml,image/*,*/*" \
      -H "Accept-Language: en-US,en;q=0.9" \
      "$url" -o "$dst"; then
    # Size sanity: empty file is a fail.
    if [ ! -s "$dst" ]; then
      echo "EMPTY|$url|$dst" >> "$FAIL_LOG"
      rm -f "$dst"
    fi
  else
    echo "CURL-FAIL|$url|$dst" >> "$FAIL_LOG"
  fi
}

export -f fetch_one
export UA FAIL_LOG

# 8-way parallel
cat "$QUEUE" | xargs -I {} -P 8 bash -c 'fetch_one "$@"' _ {}

echo "--- done ---"
echo "Queued:   $(wc -l < "$QUEUE")"
echo "Failures: $(wc -l < "$FAIL_LOG")"
