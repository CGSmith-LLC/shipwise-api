FROM yiisoftware/yii2-php:8.2-apache

RUN apt-get update && apt-get -y install mariadb-client && apt-get clean && rm -rf /var/lib/apt/lists/*

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/api/web|g' /etc/apache2/sites-available/000-default.conf
