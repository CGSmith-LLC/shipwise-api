FROM cgsmith105/yii2-php:8.0-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/shopify/web|g' /etc/apache2/sites-available/000-default.conf
RUN apt-get update; \
    apt-get install -y --no-install-recommends \
    ghostscript
