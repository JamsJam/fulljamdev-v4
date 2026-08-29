# ==============================================================================
# ? Configuration des exécutables et options communes
# ==============================================================================
PHP ?= php
COMPOSER ?= composer
DEV_MEMORY_LIMIT ?= 512M
PHPSTAN_MEMORY_LIMIT ?= 512M
SYMFONY ?= symfony
CONSOLE := $(PHP) bin/console
PHP_CS_FIXER := vendor/bin/php-cs-fixer
PHPUNIT := $(PHP) bin/phpunit
TEST_OPTIONS ?=
BDI := vendor/bin/bdi
PHPSTAN := vendor/bin/phpstan

.PHONY: quality migrate lint lint-container lint-twig lint-xliff lint-yaml cs-scan phpstan test test-db test-unit test-integration test-application test-page test-reservation test-settings test-shared test-ui

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

# ? Lance uniquement les tests d’intégration Symfony et infrastructure
test-integration: test-db
	@TEST_STATUS=0; \
	$(PHPUNIT) --testsuite Integration $(TEST_OPTIONS) || TEST_STATUS=$$?; \
	DROP_STATUS=0; \
	$(CONSOLE) doctrine:database:drop --env=test --force --if-exists || DROP_STATUS=$$?; \
	if [ $$TEST_STATUS -ne 0 ]; then exit $$TEST_STATUS; fi; \
	exit $$DROP_STATUS

# ? Prépare la base MySQL isolée utilisée par les tests Doctrine
test-db:
	$(CONSOLE) doctrine:database:drop --env=test --force --if-exists
	$(CONSOLE) doctrine:database:create --env=test
	$(CONSOLE) doctrine:migrations:migrate --env=test --no-interaction

# ? Lance uniquement les tests des cas d’usage applicatifs
test-application:
	$(PHPUNIT) --testsuite Application $(TEST_OPTIONS)

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
