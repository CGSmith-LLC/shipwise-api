FROM yiisoftware/yii2-php:8.0-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/frontend/web|g' /etc/apache2/sites-available/000-default.conf
RUN apt-get update; \
    apt-get install -y --no-install-recommends \
    ghostscript

RUN echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.idekey=shipwise_frontend" >> /usr/local/etc/php/conf.d/xdebug.ini
