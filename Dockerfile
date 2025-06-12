FROM php:8.2-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia tu código al contenedor
COPY src/ /var/www/html/

# Copia configuración personalizada de PHP (opcional)
COPY php.ini /usr/local/etc/php/

# Habilita mod_rewrite si lo necesitas
RUN a2enmod rewrite

#Se crea una imagen  personalizada de PHP con Apache
# con soporte para MySQL y configuraciones de PHP personalizadas.