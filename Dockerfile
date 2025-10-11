FROM php:8.2-apache

# Instalar extensiones necesarias y utilidades
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    zip \
    nano \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Habilitar mod_rewrite (para URLs limpias)
RUN a2enmod rewrite

# Configurar el VirtualHost para usar /var/www/html/public
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copiar los archivos del proyecto al contenedor
COPY . /var/www/html

# Dar permisos adecuados
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto
EXPOSE 80
