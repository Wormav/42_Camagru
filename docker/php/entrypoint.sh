#!/bin/sh
set -e

for dir in public/uploads/avatars public/uploads/snaps; do
	mkdir -p "$dir"
	chown -R www-data:www-data "$dir" 2>/dev/null || true
done

exec "$@"
