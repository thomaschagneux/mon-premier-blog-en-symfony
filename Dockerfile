# Utilisez une image officielle de PHP et Apache
FROM php:8.2-apache

# Installez les dépendances nécessaires (versions épinglées)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libzip-dev="$(apt-cache show -a libzip-dev | grep -m1 '^Version:' | awk '{print $2}')" \
        libpq-dev="$(apt-cache show -a libpq-dev | grep -m1 '^Version:' | awk '{print $2}')" \
        libicu-dev="$(apt-cache show -a libicu-dev | grep -m1 '^Version:' | awk '{print $2}')" \
        zip="$(apt-cache show -a zip | grep -m1 '^Version:' | awk '{print $2}')" \
        unzip="$(apt-cache show -a unzip | grep -m1 '^Version:' | awk '{print $2}')"; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install pdo pdo_pgsql zip opcache intl; \
    pecl install xdebug; \
    docker-php-ext-enable xdebug

# Activez le module Apache rewrite
RUN a2enmod rewrite

# Configurez le DocumentRoot d'Apache pour pointer vers le répertoire public de Symfony
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiez le projet Symfony dans le conteneur
COPY . /var/www/html

# Copiez la configuration PHP personnalisée
COPY php.ini /usr/local/etc/php/conf.d/symfony-custom.ini

# Définissez les permissions
RUN chown -R www-data:www-data /var/www/html/var
