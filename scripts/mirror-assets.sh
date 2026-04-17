#!/usr/bin/env bash
# Mirrors assets discovered in /tmp/asset-urls.txt into static-site/assets/.
# For theme/ and revslider/ paths, copies from local source if present.
# Everything else goes on the remote-fetch queue.
# Run from project root.

set -u

URL_FILE=/tmp/asset-urls.txt
REMOTE_QUEUE=/tmp/asset-remote.txt
LOCAL_LOG=/tmp/asset-local.log
MISSING_LOG=/tmp/asset-missing.log

: > "$REMOTE_QUEUE"
: > "$LOCAL_LOG"
: > "$MISSING_LOG"

HOST_PREFIX='https://grit.ancorathemes.com'

while IFS= read -r url; do
  [ -z "$url" ] && continue
  rel="${url#$HOST_PREFIX/}"

  case "$rel" in
    wp-content/themes/grit/*)
      sub="${rel#wp-content/themes/grit/}"
      src="grit/grit/$sub"
      dst="static-site/assets/theme/$sub"
      if [ -f "$src" ]; then
        mkdir -p "$(dirname "$dst")"
        cp "$src" "$dst"
        echo "theme-local: $sub" >> "$LOCAL_LOG"
      else
        echo "$url|static-site/assets/theme/$sub" >> "$REMOTE_QUEUE"
        echo "theme-missing-local: $sub" >> "$MISSING_LOG"
      fi
      ;;
    wp-content/plugins/booked/*)
      sub="${rel#wp-content/plugins/booked/}"
      src="_plugin-source/booked/$sub"
      dst="static-site/assets/plugins/booked/$sub"
      if [ -f "$src" ]; then
        mkdir -p "$(dirname "$dst")"
        cp "$src" "$dst"
        echo "booked-local: $sub" >> "$LOCAL_LOG"
      else
        echo "$url|static-site/assets/plugins/booked/$sub" >> "$REMOTE_QUEUE"
        echo "booked-missing-local: $sub" >> "$MISSING_LOG"
      fi
      ;;
    wp-content/plugins/revslider/*)
      sub="${rel#wp-content/plugins/revslider/}"
      src="_plugin-source/revslider/$sub"
      dst="static-site/assets/plugins/revslider/$sub"
      if [ -f "$src" ]; then
        mkdir -p "$(dirname "$dst")"
        cp "$src" "$dst"
        echo "revslider-local: $sub" >> "$LOCAL_LOG"
      else
        echo "$url|static-site/assets/plugins/revslider/$sub" >> "$REMOTE_QUEUE"
        echo "revslider-missing-local: $sub" >> "$MISSING_LOG"
      fi
      ;;
    wp-content/plugins/*)
      # other plugins — not in local bundle
      sub="${rel#wp-content/plugins/}"
      echo "$url|static-site/assets/plugins/$sub" >> "$REMOTE_QUEUE"
      ;;
    wp-content/uploads/*)
      sub="${rel#wp-content/uploads/}"
      echo "$url|static-site/assets/images/uploads/$sub" >> "$REMOTE_QUEUE"
      ;;
    wp-includes/*)
      sub="${rel#wp-includes/}"
      echo "$url|static-site/assets/vendor/wp-includes/$sub" >> "$REMOTE_QUEUE"
      ;;
    *)
      echo "unclassified: $url" >> "$MISSING_LOG"
      ;;
  esac
done < "$URL_FILE"

echo "Local copies:  $(wc -l < "$LOCAL_LOG")"
echo "Remote queued: $(wc -l < "$REMOTE_QUEUE")"
echo "Missing/odd:   $(wc -l < "$MISSING_LOG")"
