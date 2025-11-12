# Mon premier blog en Symfony

Projet Symfony 7.3 (PHP >= 8.2) — Work In Progress (WIP)

Ce projet est en cours de développement. Les fonctionnalités, la structure et la documentation peuvent évoluer. N'hésitez pas à ouvrir une issue si vous rencontrez un problème.

---

Sommaire
- Installation rapide
- Prérequis (+ commandes d'installation)
- Installation et exécution SANS Docker (en local)
- Installation et exécution AVEC Docker
- CSS frontend — Bootstrap via AssetMapper (sans CDN)
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
6. Lancer le serveur
```bash
symfony server:start -d   # ou php -S 127.0.0.1:8000 -t public
```

Pour Docker, utilisez: `make build && make up`, puis `make install`, `make migrations`, `make assets`. Le conteneur PHP n’embarque pas Node — exécutez les commandes npm sur votre machine hôte si nécessaire.

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

CSS frontend — Bootstrap via AssetMapper (sans CDN)
Ce projet utilise Bootstrap 5 servi localement via Symfony AssetMapper (aucun CDN en runtime).

Fichiers clés
- assets/styles/bootstrap.min.css — feuille de style Bootstrap (locale)
- importmap.php — références à `bootstrap` et `@popperjs/core` pour le JS
- assets/bootstrap.js — entrypoint qui importe Bootstrap et l’expose en `window.bootstrap`
- templates/base*.html.twig — chargent `{{ asset('styles/bootstrap.min.css') }}` et `{{ importmap(['app','datatables','bootstrap-init']) }}`

Commandes utiles
- Récupérer la CSS Bootstrap localement:
  ```bash
  make bootstrap-css        # télécharge la CSS dans assets/styles/
  # ou si vous avez npm et ayez installé 'bootstrap':
  make bootstrap-css-node   # copie depuis node_modules/bootstrap
  ```
- Recompiler les assets (AssetMapper):
  ```bash
  make assets
  ```
- Installer/mettre à jour les vendors ImportMap si besoin:
  ```bash
  make importmap
  ```

Vérifications rapides
- `debug:asset-map` doit lister `styles/bootstrap.min.css` → `/public/assets/<hash>/bootstrap.min.css`.
- Dans le navigateur: la CSS est chargée depuis `/assets/<hash>/bootstrap.min.css` (200), et `window.bootstrap.Modal` est disponible.

Note
- Tailwind a été retiré du projet (fichiers, dépendances et templates).

---

Licence: aucune


---

DataTables — intégration et thème Bootstrap 5 (optionnel)
Ce projet inclut DataTables. Par défaut, le thème "DT" (standard) peut être utilisé. Pour une intégration visuelle cohérente avec Bootstrap 5, vous pouvez passer à l’intégration BS5 sans CDN en runtime.

Étapes recommandées
1. Modules JS (dans le conteneur):
   ```bash
   docker-compose --env-file .env.local exec web php bin/console importmap:require datatables.net-bs5
   # (optionnel si vous utilisez le mode responsive)
   docker-compose --env-file .env.local exec web php bin/console importmap:require datatables.net-responsive-bs5 || true
   ```
2. CSS locale (sur votre machine):
   - via npm (recommandé)
     ```bash
     npm i datatables.net-bs5 datatables.net-responsive-bs5 --save
     cp node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css assets/styles/
     # si responsive:
     cp node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css assets/styles/
     ```
   - ou via téléchargement au build (toujours local en runtime)
     ```bash
     curl -fsSL https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css -o assets/styles/dataTables.bootstrap5.min.css
     curl -fsSL https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css -o assets/styles/responsive.bootstrap5.min.css
     ```
3. Entrypoint `assets/datatables.js` (exemple minimal):
   ```js
   import $ from 'jquery';
   import 'datatables.net-bs5';
   // import 'datatables.net-responsive-bs5'; // si utilisé
   import './styles/dataTables.bootstrap5.min.css';
   // import './styles/responsive.bootstrap5.min.css'; // si utilisé

   window.$ = window.jQuery = $;

   document.addEventListener('DOMContentLoaded', () => {
     const $tables = $('.datatables');
     if ($tables.length) {
       $tables.each(function () {
         this.classList.add('table', 'table-striped', 'table-bordered');
       });
       $tables.DataTable({
         responsive: true // si extension importée
       });
     }
   });
   ```
4. Recompiler les assets et recharger:
   ```bash
   make assets
   # puis Ctrl/Cmd + F5 dans le navigateur
   ```

Notes
- Si vous migrez vers BS5, vous pouvez retirer les variantes "DT" du projet:
  ```bash
  npm uninstall datatables.net-dt datatables.net-responsive-dt
  ```
- Vérifiez que les tables ont les classes Bootstrap souhaitées (ex.: `.table`, `.table-striped`).
