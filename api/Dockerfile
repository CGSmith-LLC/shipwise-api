FROM yiisoftware/yii2-php:8.0-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/api/web|g' /etc/apache2/sites-available/000-default.conf

RUN echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.idekey=shipwise_api" >> /usr/local/etc/php/conf.d/xdebug.ini
