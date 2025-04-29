# Usa la imagen de PHP con Apache como base
FROM php:8.4-apache-bullseye

# Instala dependencias adicionales si es necesario
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    intl \
    zip

# Instalación de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de tu proyecto al contenedor
COPY ./modules /var/www/html/modules
# COPY ./themes /var/www/html/themes
# COPY ./profiles /var/www/html/profiles
COPY ./sites /var/www/html/sites

# Establece los permisos correctos para los archivos
RUN chown -R www-data:www-data /var/www/html/sites /var/www/html/modules /var/www/html/themes /var/www/html/profiles

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto para iniciar el contenedor
CMD ["apache2-foreground"]