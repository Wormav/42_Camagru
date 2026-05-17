#!/bin/sh
set -e

for dir in uploads/avatars uploads/snaps; do
	mkdir -p "$dir"
	chown -R www-data:www-data "$dir" 2>/dev/null || true
done

exec "$@"
