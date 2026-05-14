# Fake demo dataset

Snapshot used by `make fake` to populate a fresh DB with realistic content
(4 users, 14 snaps, 43 likes, 25 comments) plus the matching avatars and
snap images.

## Contents

```
fake/
├── seed.sql         INSERTs for users / images / likes / comments
├── avatars/         4 avatar files (paths match users.avatar_path in seed.sql)
└── snaps/           14 snap files  (paths match images.image_path in seed.sql)
```

## How `make fake` works

1. Wipes `public/uploads/avatars/*` and `public/uploads/snaps/*` (keeps `.gitkeep`).
2. Copies `fake/avatars/*` → `public/uploads/avatars/`.
3. Copies `fake/snaps/*`   → `public/uploads/snaps/`.
4. `TRUNCATE` of `users` / `images` / `likes` / `comments` (FK checks off
   during the truncate, then back on).
5. Replays `fake/seed.sql` into the running `db` container.

## Demo credentials

The seed keeps the original bcrypt hashes — log in with the same password
you used when creating the accounts.

| Username        | Email               | Password   |
|-----------------|---------------------|------------|
| Joe_Macmillan   | joe@gmail.com       | _(yours)_  |
| Donna_Clark     | donna@gmail.com     | _(yours)_  |
| Cameron_Howe    | cameron@gmail.com   | _(yours)_  |
| Gordon_Clark    | gordon@gmail.com    | _(yours)_  |

> 💡 If you want a single shared password for all four (handy for peer
> evaluation), edit `fake/seed.sql` and replace the `password` column with
> a known bcrypt hash. Example: a hash of `Camagru42!` works with the
> password validator (8 chars, upper, lower, digit, special).

## Refreshing the snapshot

To capture the current DB state again (e.g. after adding more demo data),
run from the project root:

```bash
# Dump the four tables
docker compose exec -T db sh -c \
  'mysqldump --no-tablespaces --skip-add-drop-table --no-create-info \
   --complete-insert -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" \
   users images likes comments' > fake/seed.sql

# Copy current uploads
find public/uploads/avatars -mindepth 1 ! -name '.gitkeep' \
  -exec cp -p {} fake/avatars/ \;
find public/uploads/snaps   -mindepth 1 ! -name '.gitkeep' \
  -exec cp -p {} fake/snaps/ \;
```
