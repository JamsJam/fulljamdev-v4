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
BDI := vendor/bin/bdi
PHPSTAN := vendor/bin/phpstan

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
lint: lint-container lint-twig lint-yaml

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
# ? Lance la suite de tests unitaires PHPUnit
test:
	$(PHPUNIT)
