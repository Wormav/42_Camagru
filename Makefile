NAME    := camagru
COMPOSE := docker compose
APP     := app
WEB     := web
DB      := db

# Colors
GREEN   := \033[1;32m
YELLOW  := \033[1;33m
CYAN    := \033[1;36m
MAGENTA := \033[1;35m
RED     := \033[1;31m
DIM     := \033[2m
BOLD    := \033[1m
RESET   := \033[0m

.DEFAULT_GOAL := all

define BANNER

  ██████╗ █████╗ ███╗   ███╗ █████╗  ██████╗ ██████╗ ██╗   ██╗
 ██╔════╝██╔══██╗████╗ ████║██╔══██╗██╔════╝ ██╔══██╗██║   ██║
 ██║     ███████║██╔████╔██║███████║██║  ███╗██████╔╝██║   ██║
 ██║     ██╔══██║██║╚██╔╝██║██╔══██║██║   ██║██╔══██╗██║   ██║
 ╚██████╗██║  ██║██║ ╚═╝ ██║██║  ██║╚██████╔╝██║  ██║╚██████╔╝
  ╚═════╝╚═╝  ╚═╝╚═╝     ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝ ╚═════╝
              📸 photo booth — server-side merge

endef
export BANNER

SEP := "$(DIM)─────────────────────────────────────────────────────────────$(RESET)"

banner:
	@printf "$(CYAN)"
	@echo "$$BANNER"
	@printf "$(RESET)"

all: banner tailwind
	@printf $(SEP)"\n"
	@printf "$(MAGENTA)[1/3]$(RESET) $(BOLD)Building images (if needed)$(RESET)\n"
	@printf "      $(DIM)→ $(COMPOSE) build$(RESET)\n"
	@$(COMPOSE) build >/dev/null
	@printf "      $(GREEN)✓$(RESET) images ready\n"
	@printf "$(MAGENTA)[2/3]$(RESET) $(BOLD)Starting db / app / web$(RESET)\n"
	@printf "      $(DIM)→ $(COMPOSE) up -d$(RESET)\n"
	@$(COMPOSE) up -d >/dev/null
	@printf "      $(GREEN)✓$(RESET) containers started\n"
	@printf "$(MAGENTA)[3/3]$(RESET) $(BOLD)Waiting for MySQL to accept connections$(RESET)\n      "
	@i=0; until $(COMPOSE) exec -T $(DB) sh -c 'mysqladmin -u root -p"$$MYSQL_ROOT_PASSWORD" ping --silent' >/dev/null 2>&1; do \
		if [ $$i -ge 30 ]; then \
			printf " $(RED)✗ timeout (30s)$(RESET)\n"; exit 1; \
		fi; \
		printf "$(CYAN).$(RESET)"; sleep 1; i=$$((i+1)); \
	done; \
	printf " $(GREEN)✓ ready$(RESET) $(DIM)($${i}s)$(RESET)\n"
	@printf $(SEP)"\n"
	@printf "$(GREEN)✓ $(NAME) is up$(RESET)  $(DIM)open$(RESET) $(CYAN)http://localhost:8080$(RESET) $(DIM)·$(RESET) $(CYAN)make logs$(RESET) $(DIM)·$(RESET) $(CYAN)make shell$(RESET)\n\n"

build:
	@printf "$(MAGENTA)▼ Building images$(RESET) $(DIM)($(COMPOSE) build)$(RESET)\n"
	@$(COMPOSE) build
	@printf "$(GREEN)✓ Images built$(RESET)\n"

clean:
	@printf "$(YELLOW)▼ Stopping containers$(RESET) $(DIM)(data preserved)$(RESET)\n"
	@$(COMPOSE) down
	@printf "$(GREEN)✓ Stopped$(RESET)\n"

fclean:
	@printf "$(YELLOW)▼ Stopping containers, dropping volumes & local images$(RESET)\n"
	@printf "  $(DIM)→ this WIPES the database, removes custom images and user uploads$(RESET)\n"
	@$(COMPOSE) down -v --rmi local
	@printf "$(YELLOW)▼ Wiping user uploads$(RESET) $(DIM)(keeps .gitkeep)$(RESET)\n"
	@find public/uploads/avatars public/uploads/snaps -mindepth 1 ! -name '.gitkeep' -delete 2>/dev/null || true
	@printf "$(GREEN)✓ Clean slate$(RESET)\n"

re: fclean build all

# Dev helpers — Docker
logs:
	@$(COMPOSE) logs -f

ps:
	@$(COMPOSE) ps

shell:
	@printf "$(CYAN)→ Shell in $(APP) container$(RESET) $(DIM)(exit to leave)$(RESET)\n"
	@$(COMPOSE) exec $(APP) sh

db:
	@printf "$(CYAN)→ Opening MySQL shell as $$($(COMPOSE) exec -T $(DB) printenv MYSQL_USER)$(RESET)\n"
	@$(COMPOSE) exec $(DB) sh -c 'mysql -u "$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"'

# Fake data
fake:
	@if [ ! -f fake/seed.sql ]; then \
		printf "$(RED)✗ fake/seed.sql not found — run \`make fake-snapshot\` first$(RESET)\n"; \
		exit 1; \
	fi
	@printf "$(MAGENTA)▼ Restoring fake data$(RESET)\n"
	@printf "  $(DIM)→ wiping current uploads (keeps .gitkeep)$(RESET)\n"
	@find public/uploads/avatars public/uploads/snaps -mindepth 1 ! -name '.gitkeep' -delete 2>/dev/null || true
	@printf "  $(DIM)→ copying fake/avatars + fake/snaps → public/uploads/$(RESET)\n"
	@find fake/avatars -mindepth 1 -type f -exec cp -p {} public/uploads/avatars/ \;
	@find fake/snaps   -mindepth 1 -type f -exec cp -p {} public/uploads/snaps/   \;
	@printf "  $(DIM)→ truncating tables and replaying fake/seed.sql$(RESET)\n"
	@$(COMPOSE) cp fake/seed.sql $(DB):/tmp/fake-seed.sql >/dev/null
	@$(COMPOSE) exec -T $(DB) sh -c 'mysql -u"$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE" -e "\
		SET FOREIGN_KEY_CHECKS=0; \
		TRUNCATE TABLE comments; TRUNCATE TABLE likes; TRUNCATE TABLE images; TRUNCATE TABLE users; \
		SET FOREIGN_KEY_CHECKS=1;" && \
		mysql -u"$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE" < /tmp/fake-seed.sql' 2>&1 | grep -v "Warning" || true
	@printf "$(GREEN)✓ Fake data restored$(RESET)  $(DIM)(see fake/README.md for credentials)$(RESET)\n"

# Dev helpers
install:
	@printf "$(MAGENTA)▼ Installing PHP dependencies$(RESET) $(DIM)(composer)$(RESET)\n"
	@composer install
	@printf "$(MAGENTA)▼ Installing Node dependencies$(RESET) $(DIM)(npm)$(RESET)\n"
	@npm install
	@printf "$(GREEN)✓ Dependencies installed$(RESET)\n"

serve:
	@printf "$(CYAN)→ PHP dev server on $(BOLD)http://localhost:8000$(RESET)$(DIM) (Ctrl+C to stop)$(RESET)\n"
	@php -S localhost:8000 -t public/ public/index.php

tailwind:
	@printf "$(CYAN)→ Building CSS$(RESET) $(DIM)(minified)$(RESET)\n"
	@npx tailwindcss -i assets/input.css -o public/style.css --minify >/dev/null 2>&1
	@printf "$(GREEN)✓ public/style.css built$(RESET)\n"

tailwind-watch:
	@printf "$(CYAN)→ Watching CSS$(RESET) $(DIM)(Ctrl+C to stop)$(RESET)\n"
	@npx tailwindcss -i assets/input.css -o public/style.css --watch

fmt:
	@printf "$(MAGENTA)▼ Formatting PHP$(RESET) $(DIM)(php-cs-fixer, PSR-12)$(RESET)\n"
	@vendor/bin/php-cs-fixer fix
	@printf "$(GREEN)✓ Formatted$(RESET)\n"

lint:
	@printf "$(MAGENTA)▼ Static analysis$(RESET) $(DIM)(phpstan)$(RESET)\n"
	@vendor/bin/phpstan analyse

help: banner
	@printf "$(BOLD)Available targets$(RESET)\n\n"
	@printf "  $(BOLD)Stack$(RESET)\n"
	@printf "    $(GREEN)all$(RESET)             $(DIM)Build CSS, build images, start db/app/web (default)$(RESET)\n"
	@printf "    $(GREEN)build$(RESET)           $(DIM)Rebuild custom images$(RESET)\n"
	@printf "    $(GREEN)clean$(RESET)           $(DIM)Stop containers, keep volumes$(RESET)\n"
	@printf "    $(GREEN)fclean$(RESET)          $(DIM)Stop, drop volumes AND remove local images$(RESET)\n"
	@printf "    $(GREEN)re$(RESET)              $(DIM)fclean + build + all$(RESET)\n\n"
	@printf "  $(BOLD)Docker$(RESET)\n"
	@printf "    $(GREEN)logs$(RESET)            $(DIM)Tail container logs (db + app + web)$(RESET)\n"
	@printf "    $(GREEN)ps$(RESET)              $(DIM)Show container status$(RESET)\n"
	@printf "    $(GREEN)shell$(RESET)           $(DIM)Open a shell inside the PHP (app) container$(RESET)\n"
	@printf "    $(GREEN)db$(RESET)              $(DIM)Open a MySQL shell inside the db container$(RESET)\n"
	@printf "    $(GREEN)fake$(RESET)            $(DIM)Restore the demo dataset shipped in fake/$(RESET)\n\n"
	@printf "  $(BOLD)App$(RESET)\n"
	@printf "    $(GREEN)install$(RESET)         $(DIM)Install PHP and Node dependencies (host)$(RESET)\n"
	@printf "    $(GREEN)serve$(RESET)           $(DIM)Run PHP built-in server on :8000 (non-docker dev)$(RESET)\n"
	@printf "    $(GREEN)tailwind$(RESET)        $(DIM)Build minified public/style.css$(RESET)\n"
	@printf "    $(GREEN)tailwind-watch$(RESET)  $(DIM)Watch and rebuild CSS on change$(RESET)\n"
	@printf "    $(GREEN)fmt$(RESET)             $(DIM)Format PHP (PSR-12)$(RESET)\n"
	@printf "    $(GREEN)lint$(RESET)            $(DIM)Run PHPStan static analysis$(RESET)\n\n"
	@printf "    $(GREEN)help$(RESET)            $(DIM)Show this help$(RESET)\n\n"

.PHONY: all banner build clean fclean re logs ps shell db fake install serve tailwind tailwind-watch fmt lint help
