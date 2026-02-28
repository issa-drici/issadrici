.PHONY: help install up down restart logs shell migrate fresh seed network-traefik

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installe le projet Laravel et les dépendances
	@if [ ! -f composer.json ]; then \
		echo "📦 Création du projet Laravel..."; \
		docker run --rm -v $(PWD):/app -w /app composer create-project laravel/laravel temp_laravel; \
		echo "📋 Fusion des fichiers Laravel..."; \
		cp -r temp_laravel/* . 2>/dev/null || true; \
		cp temp_laravel/.env.example .env.example 2>/dev/null || true; \
		cp temp_laravel/.gitignore .gitignore 2>/dev/null || true; \
		rm -rf temp_laravel; \
		echo "✅ Projet Laravel créé avec succès"; \
	fi
	@if [ ! -f .env ]; then \
		cp env.template .env; \
	fi
	@$(MAKE) network-traefik || true
	@echo "🚀 Démarrage des conteneurs Docker..."
	@if ! docker network inspect traefik-network >/dev/null 2>&1; then \
		echo "⚠️  ATTENTION: Le réseau 'traefik-network' n'existe pas."; \
		echo "📋 Réseaux disponibles contenant 'traefik':"; \
		docker network ls | grep -i traefik || echo "   Aucun réseau Traefik trouvé"; \
		echo ""; \
		echo "💡 Solutions possibles:"; \
		echo "   1. Créer le réseau: docker network create traefik-network"; \
		echo "   2. Ou modifier docker-compose.yml pour utiliser le nom correct du réseau"; \
		echo ""; \
	fi
	docker-compose up -d --build
	@echo "⏳ Attente que les conteneurs soient prêts..."
	@sleep 3
	@for i in 1 2 3 4 5; do \
		if docker-compose exec -T app php --version >/dev/null 2>&1; then \
			break; \
		fi; \
		echo "   Attente... (tentative $$i/5)"; \
		sleep 2; \
	done
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	@echo "✅ Installation terminée !"
	@echo "🌐 Application disponible sur http://crm.localhost"
	@echo "📊 Traefik Dashboard: http://traefik.localhost:8080/dashboard/#/"

up: ## Démarre les conteneurs Docker
	docker-compose up -d

down: ## Arrête les conteneurs Docker
	docker-compose down

restart: ## Redémarre les conteneurs Docker
	docker-compose restart

logs: ## Affiche les logs des conteneurs
	docker-compose logs -f

shell: ## Ouvre un shell dans le conteneur app
	docker-compose exec app bash

migrate: ## Exécute les migrations
	docker-compose exec app php artisan migrate

fresh: ## Réinitialise la base de données
	docker-compose exec app php artisan migrate:fresh

seed: ## Exécute les seeders
	docker-compose exec app php artisan db:seed

filament-install: ## Installe Filament
	docker-compose exec app composer require filament/filament:"^3.3" -W --no-audit
	docker-compose exec app php artisan filament:install --panels

filament-user: ## Crée un utilisateur Filament
	docker-compose exec app php artisan make:filament-user

network-traefik: ## Crée le réseau Docker Traefik
	@if docker network inspect traefik-network >/dev/null 2>&1; then \
		echo "ℹ️  Le réseau Traefik (traefik-network) existe déjà"; \
	elif docker network ls | grep -q traefik; then \
		TRAEFIK_NET=$$(docker network ls | grep traefik | awk '{print $$2}' | head -1); \
		echo "⚠️  Réseau Traefik trouvé avec le nom: $$TRAEFIK_NET"; \
		echo "💡 Assurez-vous que docker-compose.yml utilise le nom: $$TRAEFIK_NET"; \
	else \
		echo "📡 Création du réseau Traefik (traefik-network)..."; \
		docker network create traefik-network 2>/dev/null && echo "✅ Réseau Traefik créé avec succès" || echo "⚠️  Impossible de créer le réseau (peut-être qu'il existe déjà avec un autre nom)"; \
	fi
