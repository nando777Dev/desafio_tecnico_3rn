FROM php:8.3-apache

# Instalar dependências e extensões
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite e definir o diretório público do Laravel
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/propostas_app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Definir o diretório de trabalho
WORKDIR /var/www/html

# 🧩 Ajustar o usuário do container para o mesmo UID/GID do host (WSL)
ARG UID=1000
ARG GID=1000
RUN usermod -u $UID www-data && groupmod -g $GID www-data

# Trocar para o usuário ajustado (para evitar problemas de permissão)
USER www-data

# Expor a porta padrão do Apache
EXPOSE 80

# Comando padrão (Apache)
CMD ["apache2-foreground"]
