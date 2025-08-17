# Mon premier blog en Symfony

Projet Symfony 7.3 (PHP >= 8.2) — Work In Progress (WIP)

Ce projet est en cours de développement. Les fonctionnalités, la structure et la documentation peuvent évoluer. N'hésitez pas à ouvrir une issue si vous rencontrez un problème.

---

Sommaire
- Installation rapide
- Prérequis (+ commandes d'installation)
- Installation et exécution SANS Docker (en local)
- Installation et exécution AVEC Docker
- Intégration Tailwind CSS (npm/yarn)
- Commandes utiles (Makefile)
- Variables d'environnement
- Dépannage (FAQ courte)

---

Installation rapide
1. Cloner et entrer dans le dossier
   ```bash
   git clone https://github.com/thomaschagneux/mon-premier-blog-en-symfony
   cd mon-premier-blog-en-symfony
   ```
2. Créer votre fichier .env.local (adapter DATABASE_URL selon votre contexte)
3. Installer les dépendances PHP
   ```bash
   composer install
   ```
4. Préparer la base de données
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
   # Optionnel: données de démo
   php bin/console doctrine:fixtures:load --no-interaction
   ```
5. Compiler les assets Symfony (AssetMapper)
   ```bash
   php bin/console asset-map:compile
   ```
6. Installer et construire le CSS Tailwind
   - Dev (watch):
     ```bash
     npm install
     npm run dev
     ```
   - Prod (minifié):
     ```bash
     npm run build
     ```
   Note: Le fichier généré public/assets/tailwind.css est déjà référencé dans templates/base.html.twig.
7. Lancer le serveur
   ```bash
   symfony server:start -d   # ou php -S 127.0.0.1:8000 -t public
   ```

Pour Docker, utilisez: `make build && make up`, puis `make install`, `make migrations`, `make assets`. Pour Tailwind, exécutez `npm run dev` ou `npm run build` sur votre machine hôte (le conteneur PHP n’embarque pas Node). 

---

Prérequis
- PHP >= 8.2 avec les extensions courantes (intl, pdo_pgsql, zip)
- Composer
- PostgreSQL 16 (pour l'exécution sans Docker)
- Make (utilisé par le Makefile)
- Optionnel: Symfony CLI (recommandé pour le serveur local)
- Docker & Docker Compose (pour l'exécution avec Docker)

Exemples de commandes d'installation (à adapter à votre OS)
- Ubuntu/Debian:
  ```bash
  sudo apt update
  # Outils de base (inclut make)
  sudo apt install -y build-essential curl git unzip
  # PHP 8.2 + extensions
  sudo apt install -y php8.2 php8.2-cli php8.2-intl php8.2-pgsql php8.2-zip
  # PostgreSQL
  sudo apt install -y postgresql postgresql-contrib
  # Composer
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php
  ```
- macOS (Homebrew):
  ```bash
  /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)" # si Homebrew n'est pas installé
  brew update
  brew install php composer make git
  # PostgreSQL
  brew install postgresql@16
  brew services start postgresql@16
  ```
- Windows:
  - Installer Git (inclut Git Bash): https://git-scm.com/downloads
  - Installer Make (Chocolatey): `choco install make` ou utiliser MSYS2/WSL
  - Installer PHP et Composer: https://windows.php.net/ + https://getcomposer.org/ (ou via `choco install php composer`)
  - Installer Docker Desktop: https://www.docker.com/products/docker-desktop/

---

Installation et exécution SANS Docker (en local)
1. Cloner le projet
   ```bash
   git clone https://github.com/thomaschagneux/mon-premier-blog-en-symfony
   cd mon-premier-blog-en-symfony
   ```

2. Installer les dépendances PHP
   ```bash
   composer install
   ```

3. Configurer l'environnement
   Copiez (ou créez) un fichier `.env.local` à la racine et adaptez vos variables:
   ```dotenv
   APP_ENV=dev
   APP_SECRET=change-me
   POSTGRES_DB=blog
   POSTGRES_USER=blog_user
   POSTGRES_PASSWORD=mot_de_passe
   # Base locale (hors Docker) → hôte = 127.0.0.1 et port par défaut 5432
   DATABASE_URL="postgresql://blog_user:mot_de_passe@127.0.0.1:5432/blog?serverVersion=16&charset=utf8"
   ```

4. Créer la base de données et exécuter les migrations
   Via les commandes Symfony (recommandé):
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
   ```
   Optionnel: charger des jeux de données (fixtures)
   ```bash
   php bin/console doctrine:fixtures:load --no-interaction
   ```
   Alternative (PostgreSQL natif): créer l'utilisateur et la base si besoin
   ```bash
   # créer un utilisateur PostgreSQL protégé par mot de passe
   sudo -u postgres createuser -P blog_user
   # créer la base et en donner la propriété à l'utilisateur
   sudo -u postgres createdb -O blog_user blog
   ```

5. Compiler les assets (Asset Mapper)
   ```bash
   php bin/console asset-map:compile
   ```

6. Lancer le serveur de développement
   Avec Symfony CLI (recommandé):
   ```bash
   symfony server:start -d
   # → http://127.0.0.1:8000
   ```
   Ou avec le serveur PHP intégré:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

Note: Si vous utilisez Mailpit/Mailhog pour tester les emails en local hors Docker, adaptez `MAILER_DSN` dans `.env.local`.

---

Installation et exécution AVEC Docker
Cette section s'appuie sur:
- Dockerfile (PHP 8.2 + Apache)
- compose.yaml (+ compose.override.yaml)
- Makefile (raccourcis de commandes)

Option A — via Makefile (le plus simple)
1. Vérifier `.env.local` (un exemple existe déjà dans le dépôt). Il contient notamment:
   - `POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD`
   - `DATABASE_URL="...@database:5432/..."`

2. Construire puis démarrer l'environnement (avant le premier démarrage il faut builder)
   ```bash
   make build
   make up
   # → Application: http://localhost:8080
   ```
   - Base de données exposée localement si configurée dans `compose.override.yaml` (`POSTGRES_PORT`, par défaut 5432)
   - Astuce: si le port 5432 est occupé, modifiez la variable `POSTGRES_PORT` dans votre `.env.local` (par ex. 5433) puis relancez `make down` et `make up`.

3. Installer les dépendances et préparer l'application
   ```bash
   make install
   # Créer explicitement la base (première exécution)
   docker-compose --env-file .env.local exec web php bin/console doctrine:database:create
   # Exécuter les migrations
   make migrations
   # Optionnel, jeux de données
   make fixtures
   # Compiler les assets
   make assets
   ```

4. (Optionnel) Consulter les logs
   ```bash
   make logs
   ```

5. Arrêter / reconstruire
   ```bash
   make down        # stop
   make down-v      # stop + suppression des volumes (réinitialise la DB)
   make build       # rebuild des images
   ```

Option B — via docker compose directement
```bash
# Construire les images (première fois)
docker-compose --env-file .env.local build
# Démarrer
docker-compose --env-file .env.local up -d
# Installer les dépendances (dans le conteneur web)
docker-compose --env-file .env.local exec web composer install
# Créer la base puis migrer
docker-compose --env-file .env.local exec web php bin/console doctrine:database:create
docker-compose --env-file .env.local exec web php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
# Optionnel: fixtures
docker-compose --env-file .env.local exec web php bin/console doctrine:fixtures:load --no-interaction
# Compiler les assets
docker-compose --env-file .env.local exec web php bin/console asset-map:compile
```

Accès
- Application: http://localhost:8080
- Mailpit (si activé via compose.override.yaml):
  - UI: http://localhost:8025
  - SMTP: localhost:1025

---

Commandes utiles (Makefile)
- `make up` / `make down` / `make down-v` / `make restart` / `make build` / `make logs` / `make ps`
- `make install` (composer install)
- `make migrations` (doctrine:migrations:migrate)
- `make fixtures` (doctrine:fixtures:load)
- `make assets` (asset-map:compile)
- `make tests` (phpunit)
- `make reset-db` (drop/create/migrate + fixtures)
- `make clean` (clear cache + cs-fix + phpstan)

Lancez `make` sans argument pour afficher l'aide intégrée.

---

Variables d'environnement
- `APP_ENV`: dev par défaut en local
- `DATABASE_URL`: en Docker, host = `database`; en local, host = `127.0.0.1`
- `POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD`: doivent être cohérents entre `.env.local` et docker compose si vous utilisez Docker
- `MAILER_DSN`: si vous utilisez Mailpit en Docker, utilisez par exemple `smtp://mailer:1025`

---

Dépannage (FAQ courte)
- Le port 5432 est déjà utilisé: changez `POSTGRES_PORT` dans `.env.local` (ex: 5433), puis `make down` et `make up`.
- Erreur de connexion base de données hors Docker: vérifiez que PostgreSQL tourne en local et que `DATABASE_URL` cible `127.0.0.1:5432` avec les bons identifiants.
- Droits d'écriture sur `var/`: en Docker, les permissions sont ajustées dans l'image; hors Docker, donnez accès à votre utilisateur si nécessaire.
- Assets non à jour: relancez `php bin/console asset-map:compile` (ou `make assets` en Docker).

---

Intégration Tailwind CSS (npm/yarn)
Cette application inclut une configuration minimaliste pour utiliser Tailwind CSS via le CLI (sans bundler JS). Vous pouvez rester sur le CDN pendant que vous mettez en place le build local.

Fichiers clés
- tailwind.config.js — chemins de scan configurés pour les templates Twig et assets.
- assets/styles/tailwind.css — point d'entrée CSS avec les directives @tailwind.
- package.json — scripts pour le build et le watch.

Commandes
- Installation des dépendances Node:
  ```bash
  npm install
  ```
- Watch en développement (génère public/assets/tailwind.css en continu):
  ```bash
  npm run dev
  ```
- Build de production (minifié):
  ```bash
  npm run build
  ```

Intégration Twig
- Le layout principal (templates/base.html.twig) charge:
  - <link rel="stylesheet" href="{{ asset('assets/tailwind.css') }}"> (fichier compilé par le CLI)
- Un fallback CDN peut être utilisé si nécessaire. Vous pouvez le retirer une fois le build local en place.

Note Docker
- L'image PHP n'embarque pas Node. Lancez `npm run dev`/`npm run build` sur votre hôte, le résultat dans public/assets est monté dans le conteneur.

---

Licence: aucune
