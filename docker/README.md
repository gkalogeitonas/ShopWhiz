# ShopWhiz Docker Setup

This repository contains two Docker Compose configurations:

- `docker-compose.yml`: For production environment with all services containerized
- `docker-compose.dev.yml`: For development environment with only Meilisearch and Qdrant containerized

## Development Environment Setup

For development, you'll run Laravel with the native `artisan serve` command and use containerized versions of Meilisearch and Qdrant:

### 1. Setup the Development Environment

```bash
# Copy the example environment file
cp .env.example .env

# Generate an application key
php artisan key:generate

# Install Composer dependencies
composer install

# Install NPM dependencies (if needed)
npm install

# Run migrations
php artisan migrate

# Start the required containerized services (Meilisearch and Qdrant)
docker-compose -f docker-compose.dev.yml up -d

# Run Laravel locally using the artisan command
php artisan serve
```

You can now access:
- Laravel application at http://localhost:8000
- Meilisearch at http://localhost:7700
- Qdrant at http://localhost:6333

### Development Environment Configuration

Update your `.env` file for development:

```
DB_HOST=127.0.0.1
DB_PORT=3306
# ... other MySQL settings for your local installation

MEILISEARCH_HOST=http://localhost
MEILISEARCH_PORT=7700
MEILISEARCH_KEY=masterKey

QDRANT_HOST=http://localhost
QDRANT_PORT=6333

OPENAI_API_KEY=your_openai_api_key_here
```

## Production Environment Setup

For production, use the main Docker Compose file which includes all services with Traefik integration:

```bash
# Copy the example environment file and update it for production
cp .env.example .env
# Edit the .env file with your production values

# Start all production services
docker-compose up -d

# If this is the first time you're deploying, you'll need to run these commands inside the app container:
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
```

### Production Environment Configuration

For production, update your `.env` file with:

```
APP_ENV=production
APP_DEBUG=false

DB_HOST=db
DB_PORT=3306
# ... other MySQL settings

MEILISEARCH_HOST=http://meilisearch
MEILISEARCH_PORT=7700
MEILI_MASTER_KEY=your_secure_key_here

QDRANT_HOST=http://qdrant
QDRANT_PORT=6333

OPENAI_API_KEY=your_openai_api_key_here
```

Make sure to generate secure keys and passwords for production. You can generate a secure key with:

```bash
openssl rand -base64 32
```

## Container Structure

- **app**: Laravel application container (PHP 8.2-FPM)
- **web**: Nginx web server container (Alpine version)
- **db**: MySQL 8 database container
- **meilisearch**: MeiliSearch search engine container
- **qdrant**: Qdrant vector database container

## Traefik Integration

The production environment is configured to work with Traefik as a reverse proxy. Ensure you have a Traefik container running with a network named `proxy`.

### Setting up Traefik (if not already set up)

If you don't have Traefik set up yet, create a `traefik` directory and add the following files:

1. Create `docker-compose.yml` for Traefik:

```yml
version: '3'

services:
  traefik:
    image: traefik:v2.10
    container_name: traefik
    restart: unless-stopped
    command:
      - "--api.dashboard=true"
      - "--providers.docker=true"
      - "--providers.docker.exposedbydefault=false"
      - "--entrypoints.web.address=:80"
      - "--entrypoints.web.http.redirections.entrypoint.to=websecure"
      - "--entrypoints.web.http.redirections.entrypoint.scheme=https"
      - "--entrypoints.websecure.address=:443"
      - "--certificatesresolvers.letsencrypt.acme.httpchallenge=true"
      - "--certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web"
      - "--certificatesresolvers.letsencrypt.acme.email=your-email@example.com"
      - "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - "/var/run/docker.sock:/var/run/docker.sock:ro"
      - "./letsencrypt:/letsencrypt"
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.traefik.rule=Host(`traefik.yourdomain.com`)"
      - "traefik.http.routers.traefik.entrypoints=websecure"
      - "traefik.http.routers.traefik.tls.certresolver=letsencrypt"
      - "traefik.http.routers.traefik.service=api@internal"
      - "traefik.http.routers.traefik.middlewares=traefik-auth"
      - "traefik.http.middlewares.traefik-auth.basicauth.users=admin:$$apr1$$ruca84Hq$$mbjdMZBAG.KWn7vfN/SNK/" # admin/password (use htpasswd to generate your own)
    networks:
      - proxy

networks:
  proxy:
    external: true
```

2. Create the external network:

```bash
docker network create proxy
```

3. Start Traefik:

```bash
docker-compose up -d
```

## Maintenance Commands

### Viewing Logs

```bash
# View logs from all containers
docker-compose logs

# View logs from a specific container
docker-compose logs app
docker-compose logs meilisearch
docker-compose logs qdrant
```

### Running Laravel Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Create a new controller
docker-compose exec app php artisan make:controller ProductController

# Clear cache
docker-compose exec app php artisan cache:clear
```
