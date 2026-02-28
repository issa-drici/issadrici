# Déploiement sur Coolify

Ce guide vous explique comment déployer ce projet Laravel sur un VPS avec Coolify.

## Prérequis

- Un VPS avec Coolify installé
- Un service PostgreSQL disponible (peut être créé via Coolify)
- Un domaine configuré (optionnel mais recommandé)

## Configuration dans Coolify

### 1. Créer une nouvelle application

1. Dans Coolify, créez une nouvelle application
2. Connectez votre repository Git
3. Coolify détectera automatiquement que c'est un projet Laravel

### 2. Configuration de la base de données

#### Option A : Base de données gérée par Coolify

1. Créez un nouveau service PostgreSQL dans Coolify
2. Notez les informations de connexion (host, port, database, username, password)

#### Option B : Base de données externe

Utilisez les informations de votre base de données PostgreSQL existante.

### 3. Variables d'environnement

Configurez les variables d'environnement suivantes dans Coolify :

```env
APP_NAME=CRMForfait
APP_ENV=production
APP_KEY=base64:... (généré automatiquement ou manuellement)
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=pgsql
DB_HOST=votre-host-postgres
DB_PORT=5432
DB_DATABASE=votre-database
DB_USERNAME=votre-username
DB_PASSWORD=votre-password

REDIS_HOST=votre-redis-host
REDIS_PORT=6379
REDIS_PASSWORD=null

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=votre-smtp-host
MAIL_PORT=587
MAIL_USERNAME=votre-email
MAIL_PASSWORD=votre-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Build et déploiement

Coolify utilisera automatiquement le `Dockerfile` présent dans le projet.

**Commandes de build** (si nécessaire de les personnaliser) :
```bash
composer install --optimize-autoloader --no-dev
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Commandes de déploiement** :
```bash
php artisan migrate --force
php artisan filament:install --panels --force
```

### 5. Scripts post-déploiement

Vous pouvez ajouter des scripts dans Coolify pour automatiser certaines tâches :

**Après le déploiement** :
```bash
php artisan optimize
php artisan storage:link
```

## Notes importantes

1. **APP_KEY** : Assurez-vous que `APP_KEY` est défini. Coolify peut le générer automatiquement, ou vous pouvez le générer manuellement avec `php artisan key:generate`.

2. **Permissions** : Coolify gère généralement les permissions automatiquement, mais si vous avez des problèmes avec le storage :
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Filament** : Après le premier déploiement, créez un utilisateur admin :
   ```bash
   php artisan make:filament-user
   ```

4. **SSL** : Coolify peut configurer automatiquement SSL avec Let's Encrypt pour votre domaine.

5. **Backups** : Configurez des backups réguliers de votre base de données PostgreSQL via Coolify.

## Dépannage

### Erreur de connexion à la base de données
- Vérifiez que le service PostgreSQL est accessible depuis votre application
- Vérifiez les variables d'environnement `DB_*`
- Assurez-vous que le firewall autorise les connexions

### Erreur 500
- Vérifiez les logs dans Coolify
- Vérifiez que `APP_KEY` est défini
- Vérifiez les permissions sur `storage/` et `bootstrap/cache/`

### Assets non chargés
- Exécutez `php artisan storage:link` si vous utilisez le storage public
- Vérifiez que les assets sont compilés : `npm run build`

## Mise à jour

Pour mettre à jour l'application :

1. Poussez vos changements sur Git
2. Coolify détectera automatiquement les changements et redéploiera
3. Ou déclenchez manuellement un redéploiement dans Coolify
