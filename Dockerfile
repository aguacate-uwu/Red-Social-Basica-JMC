# Usa la imagen de PHP con Apache como base
FROM php:8.4-apache-bullseye

# Una variable para evitar un warning de render
ENV TERM=xterm

# Instala dependencias adicionales
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    gd \
    intl \
    zip

# Instalación de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer directorio de trabajo
WORKDIR /var/www/html/web

# Copia los archivos del proyecto al contenedor
COPY ./drupal/web/ /var/www/html/web/
COPY ./drupal/vendor/ /var/www/html/vendor/

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite
# Configuración de un index.php para Apache
COPY drupal.conf /etc/apache2/sites-available/drupal.conf
RUN a2ensite drupal.conf
RUN a2dissite 000-default.conf
# Comando para ver si hay error con drupal.conf
RUN apachectl -t
# Intentar inciiar y esperar un poco
RUN service apache2 start && sleep 5 
RUN service apache2 reload

# Establece los permisos correctos para los archivos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto para iniciar el contenedor
CMD ["apache2-foreground"]