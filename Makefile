NAME    := camagru
COMPOSE := docker compose
SERVICE := db

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

# -----------------------------------------------------------------------------
# Banner
# -----------------------------------------------------------------------------
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

all: banner
	@printf $(SEP)"\n"
	@printf "$(MAGENTA)[1/2]$(RESET) $(BOLD)Starting MySQL container$(RESET)\n"
	@printf "      $(DIM)→ $(COMPOSE) up -d $(SERVICE)$(RESET)\n"
	@$(COMPOSE) up -d $(SERVICE) >/dev/null 2>&1
	@printf "      $(GREEN)✓$(RESET) container started\n"
	@printf "$(MAGENTA)[2/2]$(RESET) $(BOLD)Waiting for MySQL to accept connections$(RESET)\n      "
	@i=0; until $(COMPOSE) exec -T $(SERVICE) sh -c 'mysqladmin -u root -p"$$MYSQL_ROOT_PASSWORD" ping --silent' >/dev/null 2>&1; do \
		if [ $$i -ge 30 ]; then \
			printf " $(RED)✗ timeout (30s)$(RESET)\n"; exit 1; \
		fi; \
		printf "$(CYAN).$(RESET)"; sleep 1; i=$$((i+1)); \
	done; \
	printf " $(GREEN)✓ ready$(RESET) $(DIM)($${i}s)$(RESET)\n"
	@printf $(SEP)"\n"
	@printf "$(GREEN)✓ $(NAME) is up$(RESET)  $(DIM)try$(RESET) $(CYAN)make mysql$(RESET) $(DIM)or$(RESET) $(CYAN)make logs$(RESET)\n\n"

clean:
	@printf "$(YELLOW)▼ Stopping containers$(RESET) $(DIM)(data preserved)$(RESET)\n"
	@$(COMPOSE) down
	@printf "$(GREEN)✓ Stopped$(RESET)\n"

fclean:
	@printf "$(YELLOW)▼ Stopping containers and dropping volumes$(RESET)\n"
	@printf "  $(DIM)→ this WIPES the database (volumes will be deleted)$(RESET)\n"
	@$(COMPOSE) down -v
	@printf "$(GREEN)✓ Clean slate$(RESET)\n"

re: fclean all

# -----------------------------------------------------------------------------
# Dev helpers — Docker
# -----------------------------------------------------------------------------
logs:
	@$(COMPOSE) logs -f

ps:
	@$(COMPOSE) ps

mysql:
	@printf "$(CYAN)→ Opening MySQL shell as $$($(COMPOSE) exec -T $(SERVICE) printenv MYSQL_USER)$(RESET)\n"
	@$(COMPOSE) exec $(SERVICE) sh -c 'mysql -u "$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"'

# -----------------------------------------------------------------------------
# Dev helpers — App
# -----------------------------------------------------------------------------
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
	@npx tailwindcss -i assets/input.css -o public/style.css --minify
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
	@printf "    $(GREEN)all$(RESET)             $(DIM)Start the stack (default)$(RESET)\n"
	@printf "    $(GREEN)clean$(RESET)           $(DIM)Stop containers, keep data$(RESET)\n"
	@printf "    $(GREEN)fclean$(RESET)          $(DIM)Stop containers AND drop volumes (DB reset)$(RESET)\n"
	@printf "    $(GREEN)re$(RESET)              $(DIM)Full reset and restart$(RESET)\n\n"
	@printf "  $(BOLD)Docker$(RESET)\n"
	@printf "    $(GREEN)logs$(RESET)            $(DIM)Tail container logs$(RESET)\n"
	@printf "    $(GREEN)ps$(RESET)              $(DIM)Show container status$(RESET)\n"
	@printf "    $(GREEN)mysql$(RESET)           $(DIM)Open MySQL shell inside the container$(RESET)\n\n"
	@printf "  $(BOLD)App$(RESET)\n"
	@printf "    $(GREEN)install$(RESET)         $(DIM)Install PHP and Node dependencies$(RESET)\n"
	@printf "    $(GREEN)serve$(RESET)           $(DIM)Run PHP dev server on :8000$(RESET)\n"
	@printf "    $(GREEN)tailwind$(RESET)        $(DIM)Build minified public/style.css$(RESET)\n"
	@printf "    $(GREEN)tailwind-watch$(RESET)  $(DIM)Watch and rebuild CSS on change$(RESET)\n"
	@printf "    $(GREEN)fmt$(RESET)             $(DIM)Format PHP (PSR-12)$(RESET)\n"
	@printf "    $(GREEN)lint$(RESET)            $(DIM)Run PHPStan static analysis$(RESET)\n\n"
	@printf "    $(GREEN)help$(RESET)            $(DIM)Show this help$(RESET)\n\n"

.PHONY: all banner clean fclean re logs ps mysql install serve tailwind tailwind-watch fmt lint help
