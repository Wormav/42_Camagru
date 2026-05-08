# Camagru

A web-based photobooth application built as a 42 school project (subject v4.1).

Users can capture photos via webcam (or upload an image), overlay them with selectable PNG stickers (alpha channel), and the server merges both images into a final composition. All creations are displayed in a public gallery where users can like and comment.

## Tech Stack

| Component | Choice |
|-----------|--------|
| Server | PHP (standard library only, no framework) |
| Architecture | MVC from scratch |
| CSS | Tailwind CSS (CLI build, static CSS output) |
| Database | MySQL via PDO (prepared statements) |
| Image processing | GD (PHP extension) |
| Email | Native `mail()` |
| Web server | Nginx |
| Containerization | Docker + docker-compose |
| JavaScript | Vanilla JS (browser native APIs only) |

## Features

### Mandatory

- **User management** — Registration with email confirmation, login, logout, password reset, profile editing
- **Photo editor** — Webcam capture or image upload, overlay selection, server-side image merging (GD)
- **Public gallery** — Paginated display of all creations, likes and comments (authenticated users only)
- **Email notifications** — Author notified on new comments (opt-out in preferences)
- **Security** — bcrypt passwords, prepared statements, CSRF tokens, XSS protection, upload validation
- **Docker** — Single `docker-compose up` deploys the full stack

### Bonus

- AJAX interactions (native `fetch()`)
- Live overlay preview on webcam stream (canvas + `requestAnimationFrame`)
- Infinite scroll pagination (`IntersectionObserver`)
- Social media sharing (URL-based, no SDK)
- Animated GIF rendering (multi-frame capture + server encoding)

## Getting Started

```bash
git clone git@github.com:Wormav/42_Camagru.git
cp .env.example .env
# Edit .env with your credentials
docker-compose up
```

The application will be available at `http://localhost:8080`.

## Project Constraints

This project follows strict rules imposed by the 42 subject:

- **No PHP frameworks** (Symfony, Laravel, etc.)
- **No JS libraries** (jQuery, React, etc.)
- **No ORM** — raw PDO only
- **CSS frameworks allowed** only if they don't include JavaScript
- All server-side functions must have an equivalent in the PHP standard library
- Compatible with Firefox >= 41 and Chrome >= 46

## License

This project is a school assignment and is not intended for production use.
