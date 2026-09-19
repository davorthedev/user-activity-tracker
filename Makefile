DC := docker compose
UID := $(shell id -u)
GID := $(shell id -g)
EXEC := $(DC) exec -u $(UID):$(GID) php

# can be overide on command line (make seed EVENTS=10000)
EVENTS ?= 1000000
DAYS ?= 90
USERS ?= 50

.PHONY: up down fresh install seed test logs

up: # build
	$(DC) up -d --build --wait

down: # stop without deleting
	$(DC) down

fresh: # delete db volume and restart
	$(DC) down -v
	$(DC) up -d --build --wait
	$(MAKE) install

install: # Install and create two demo accounts
	$(EXEC) composer install
	@$(EXEC) php bin/console user:create admin@mejl.com ponchek2024 admin || echo "  (admin@mejl.com exists - ignore)"
	@$(EXEC) php bin/console user:create user@mejl.com ponchek2024 || echo "  (user@mejl.com exists - ignore)"
	@echo ""
	@echo "  Go here and login: http://localhost:8080"
	@echo "  with admin@mejl.com / ponchek2024"
	@echo "  or"
	@echo "  with user@mejl.com  / ponchek2024"

seed: # fill the events table 
	$(EXEC) php bin/console events:seed $(EVENTS) $(DAYS) $(USERS)

test:
	$(EXEC) vendor/bin/phpunit

logs:
	$(DC) logs -f php