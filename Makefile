# ==============================================================================
# ? Configuration des exécutables et options communes
# ==============================================================================
PHP ?= php
COMPOSER ?= composer
DEV_MEMORY_LIMIT ?= 512M
SYMFONY ?= symfony
CONSOLE := $(PHP) bin/console
PHP_CS_FIXER := vendor/bin/php-cs-fixer
PHPCS := vendor/bin/phpcs
PHPCBF := vendor/bin/phpcbf
PHPUNIT := $(PHP) bin/phpunit
BDI := vendor/bin/bdi

# * Le scan de style est non modifiant par défaut
DR ?= 1


# ==============================================================================
# * VALIDATION SYMFONY ET PRÉSENTATION
# ==============================================================================
# ? Lance tous les linters Symfony disponibles
lint: lint-container lint-translations lint-twig lint-xliff lint-yaml

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
	@fixer_status=0; phpcs_status=0; \
	$(PHP_CS_FIXER) fix --dry-run --diff --using-cache=no --sequential || fixer_status=$$?; \
	$(PHPCS) --standard=phpcs.xml.dist || phpcs_status=$$?; \
	test $$fixer_status -eq 0 -a $$phpcs_status -eq 0
else ifeq ($(DR),0)
	$(PHP_CS_FIXER) fix --using-cache=no --sequential
	@status=0; $(PHPCBF) --standard=phpcs.xml.dist || status=$$?; test $$status -le 1
	$(PHPCS) --standard=phpcs.xml.dist
else
	$(error DR doit valoir 0 ou 1)
endif
