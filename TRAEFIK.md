# Configuration Traefik

Ce projet utilise Traefik comme reverse proxy pour le routage automatique.

## Prérequis

Traefik doit être installé et configuré avec :
- Un réseau Docker nommé `traefik`
- Un point d'entrée `web` sur le port 80

## Configuration

Le projet est configuré pour être accessible sur `crm.localhost` par défaut.

### Changer le domaine

Pour utiliser un autre domaine, modifiez la variable `APP_DOMAIN` dans votre `.env` :

```env
APP_DOMAIN=mon-crm.localhost
APP_URL=http://mon-crm.localhost
```

Puis redémarrez les conteneurs :

```bash
docker-compose down
docker-compose up -d
```

## Labels Traefik

Le service `app` utilise les labels suivants :

- `traefik.enable=true` : Active Traefik pour ce service
- `traefik.docker.network=traefik` : Utilise le réseau Traefik
- `traefik.http.routers.crm.rule=Host(...)` : Règle de routage basée sur le hostname
- `traefik.http.routers.crm.entrypoints=web` : Point d'entrée (port 80)
- `traefik.http.services.crm.loadbalancer.server.port=8000` : Port interne du service
- Middlewares pour les headers `X-Forwarded-Proto` et `X-Forwarded-Host`

## Vérification

1. Vérifiez que Traefik est démarré :
   ```bash
   docker ps | grep traefik
   ```

2. Vérifiez que le réseau `traefik` existe :
   ```bash
   docker network ls | grep traefik
   ```

3. Si le réseau n'existe pas, créez-le :
   ```bash
   docker network create traefik
   ```

4. Vérifiez dans le dashboard Traefik (http://traefik.localhost:8080/dashboard/#/) que le service `crm` apparaît dans les routers.

## Dépannage

### L'application n'est pas accessible

1. Vérifiez que Traefik est démarré
2. Vérifiez que le réseau `traefik` existe
3. Vérifiez les logs du conteneur app :
   ```bash
   docker-compose logs app
   ```
4. Vérifiez dans le dashboard Traefik que le router est bien configuré

### Erreur de connexion au réseau Traefik

Si vous obtenez une erreur indiquant que le réseau `traefik` n'existe pas :

```bash
docker network create traefik
```

Puis redémarrez les conteneurs :

```bash
docker-compose down
docker-compose up -d
```

### Changer le point d'entrée

Si votre Traefik utilise un autre point d'entrée que `web`, modifiez le label dans `docker-compose.yml` :

```yaml
- "traefik.http.routers.crm.entrypoints=websecure"  # Pour HTTPS
```
