FROM cgsmith105/yii2-php:8.0-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/frontend/web|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's|memory_limit=128M|memory_limit=512M|g' /usr/local/etc/php/conf.d/base.ini
RUN apt-get update; \
    apt-get install -y --no-install-recommends \
    ghostscript
