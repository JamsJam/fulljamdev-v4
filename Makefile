# ==============================================================================
# ? Configuration des exécutables et options communes
# ==============================================================================
PHP ?= php
COMPOSER ?= composer
DEV_MEMORY_LIMIT ?= 512M
PHPSTAN_MEMORY_LIMIT ?= 512M
SYMFONY ?= symfony
DOCKER_COMPOSE ?= docker compose
TEST_DATABASE_PORT ?= 3307
TEST_DATABASE_HOST ?= 127.0.0.1
TEST_DATABASE_USER ?= root
TEST_DATABASE_PASSWORD ?= root
TEST_DATABASE_URL ?= mysql://$(TEST_DATABASE_USER):$(TEST_DATABASE_PASSWORD)@$(TEST_DATABASE_HOST):$(TEST_DATABASE_PORT)/fulljamdev4?serverVersion=8.0.32&charset=utf8mb4
CONSOLE := $(PHP) bin/console
PHP_CS_FIXER := vendor/bin/php-cs-fixer
PHPUNIT := $(PHP) bin/phpunit
TEST_OPTIONS ?=
BDI := vendor/bin/bdi
PHPSTAN := vendor/bin/phpstan

.PHONY: quality migrate lint lint-container lint-twig lint-xliff lint-yaml cs-scan phpstan test test-assets test-db test-unit test-integration test-application test-page test-reservation test-settings test-shared test-ui

# * Le scan de style est non modifiant par défaut
DR ?= 1
# ==============================================================================
# * Commandes combinées 
# ==============================================================================
quality: lint cs-scan phpstan

# ? Vérifie le projet et simule les migrations avant de les exécuter
migrate:
	$(MAKE) lint
	$(SYMFONY) console doctrine:migrations:migrate --dry-run --no-interaction && \
	$(SYMFONY) console doctrine:migrations:migrate --no-interaction


# ==============================================================================
# * VALIDATION SYMFONY ET PRÉSENTATION
# ==============================================================================
# ? Lance tous les linters Symfony disponibles
lint: lint-container lint-twig lint-xliff lint-yaml

# ? Vérifie la compilation du conteneur de services
lint-container:
	$(CONSOLE) lint:container

# ? Vérifie les templates Twig
lint-twig:
	$(CONSOLE) lint:twig templates

# ? Vérifie les fichiers de traduction XLIFF
lint-xliff:
	$(CONSOLE) lint:xliff translations

# ? Vérifie la configuration YAML Symfony
lint-yaml:
	$(CONSOLE) lint:yaml config --parse-tags

# ==============================================================================
# * QUALITÉ ET FORMATAGE DU CODE PHP
# ==============================================================================
# ? make cs-scan ou make cs-scan DR=1 analyse sans modifier les fichiers
# ! make cs-scan DR=0 applique automatiquement les corrections disponibles
cs-scan:
ifeq ($(DR),1)
	$(PHP_CS_FIXER) fix --dry-run --diff --using-cache=no --sequential
else ifeq ($(DR),0)
	$(PHP_CS_FIXER) fix --using-cache=no --sequential
else
	$(error DR doit valoir 0 ou 1)
endif

# ? Analyse statique PHP
phpstan:
	$(PHPSTAN) analyse --memory-limit=$(PHPSTAN_MEMORY_LIMIT)

# ==============================================================================
# * TESTS AUTOMATISÉS
# ==============================================================================
# ? Lance les linters puis les suites de tests et s’arrête dès qu’une étape échoue
test:
	$(MAKE) quality && \
	$(MAKE) test-unit && \
	$(MAKE) test-integration && \
	$(MAKE) test-application

# ? Lance uniquement les tests unitaires isolés
test-unit:
	$(PHPUNIT) --testsuite Unit $(TEST_OPTIONS)

# ? Compile les feuilles Sass requises par les rendus Twig et AssetMapper
test-assets:
	$(CONSOLE) sass:build --env=test

# ? Lance les tests d’intégration puis nettoie la base et le conteneur Docker
test-integration: test-assets
	@TEST_STATUS=0; DATABASE_READY=0; \
	$(MAKE) test-db && DATABASE_READY=1 || TEST_STATUS=$$?; \
	if [ $$TEST_STATUS -eq 0 ]; then \
		DATABASE_URL='$(TEST_DATABASE_URL)' $(PHPUNIT) --testsuite Integration $(TEST_OPTIONS) || TEST_STATUS=$$?; \
	fi; \
	if [ $$DATABASE_READY -eq 1 ]; then DATABASE_URL='$(TEST_DATABASE_URL)' $(CONSOLE) doctrine:database:drop --env=test --force --if-exists; fi || { \
		CLEANUP_STATUS=$$?; \
		if [ $$TEST_STATUS -eq 0 ]; then TEST_STATUS=$$CLEANUP_STATUS; fi; \
	}; \
	MYSQL_PORT=$(TEST_DATABASE_PORT) $(DOCKER_COMPOSE) rm --stop --force database || { \
		CLEANUP_STATUS=$$?; \
		if [ $$TEST_STATUS -eq 0 ]; then TEST_STATUS=$$CLEANUP_STATUS; fi; \
	}; \
	exit $$TEST_STATUS

# ? Démarre MySQL avec Docker et prépare la base isolée utilisée par Doctrine
test-db:
	MYSQL_PORT=$(TEST_DATABASE_PORT) $(DOCKER_COMPOSE) up -d --wait database
	@ATTEMPT=0; \
	until $(PHP) -r 'try { new PDO("mysql:host=$(TEST_DATABASE_HOST);port=$(TEST_DATABASE_PORT)", "$(TEST_DATABASE_USER)", "$(TEST_DATABASE_PASSWORD)"); } catch (Throwable) { exit(1); }'; do \
		ATTEMPT=$$((ATTEMPT + 1)); \
		if [ $$ATTEMPT -ge 30 ]; then echo "MySQL Docker n’est pas accessible depuis PHP après 60 secondes." >&2; exit 1; fi; \
		sleep 2; \
	done
	DATABASE_URL='$(TEST_DATABASE_URL)' $(CONSOLE) doctrine:database:drop --env=test --force --if-exists
	DATABASE_URL='$(TEST_DATABASE_URL)' $(CONSOLE) doctrine:database:create --env=test
	DATABASE_URL='$(TEST_DATABASE_URL)' $(CONSOLE) doctrine:migrations:migrate --env=test --no-interaction

# ? Lance les tests applicatifs puis nettoie la base et le conteneur Docker
test-application: test-assets
	@TEST_STATUS=0; DATABASE_READY=0; \
	$(MAKE) test-db && DATABASE_READY=1 || TEST_STATUS=$$?; \
	if [ $$TEST_STATUS -eq 0 ]; then \
		DATABASE_URL='$(TEST_DATABASE_URL)' $(PHPUNIT) --testsuite Application $(TEST_OPTIONS) || TEST_STATUS=$$?; \
	fi; \
	if [ $$DATABASE_READY -eq 1 ]; then DATABASE_URL='$(TEST_DATABASE_URL)' $(CONSOLE) doctrine:database:drop --env=test --force --if-exists; fi || { \
		CLEANUP_STATUS=$$?; \
		if [ $$TEST_STATUS -eq 0 ]; then TEST_STATUS=$$CLEANUP_STATUS; fi; \
	}; \
	MYSQL_PORT=$(TEST_DATABASE_PORT) $(DOCKER_COMPOSE) rm --stop --force database || { \
		CLEANUP_STATUS=$$?; \
		if [ $$TEST_STATUS -eq 0 ]; then TEST_STATUS=$$CLEANUP_STATUS; fi; \
	}; \
	exit $$TEST_STATUS

# ? Lance tous les tests du domaine Page
test-page:
	$(PHPUNIT) tests/Page $(TEST_OPTIONS)

# ? Lance tous les tests du domaine Reservation
test-reservation:
	$(PHPUNIT) tests/Reservation $(TEST_OPTIONS)

# ? Lance tous les tests du domaine Settings
test-settings:
	$(PHPUNIT) tests/Settings $(TEST_OPTIONS)

# ? Lance tous les tests des services partagés
test-shared:
	$(PHPUNIT) tests/Shared $(TEST_OPTIONS)

# ? Lance tous les tests des composants UI
test-ui:
	$(PHPUNIT) tests/UI $(TEST_OPTIONS)
