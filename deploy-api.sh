#!/bin/bash
# located in /usr/local/bin/deploy-api.sh
echo "Unzipping build $2"
unzip -d /var/builds/api/$1/ /var/builds/api/$2 || { echo 'unzip failed on build' ; exit 1; }

# Move current API to old folder
mv -v /var/www/api /var/www/api.old || { echo 'mv to old failed' ; exit 1; }
mv -v /var/builds/api/$1/ /var/www/api/ || { echo 'mv to backend failed' ; exit 1; }

# Copy local files to new deployment
cp /var/builds/configs/api/api/web/index.php /var/www/api/api/web/index.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/builds/configs/api/common/config/main-local.php /var/www/api/common/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/builds/configs/api/common/config/params-local.php /var/www/api/common/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }

cp /var/builds/configs/api/console/config/main-local.php /var/www/api/console/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/builds/configs/api/console/config/params-local.php /var/www/api/console/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }

cp /var/builds/configs/api/api/config/main-local.php /var/www/api/api/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/builds/configs/api/api/config/params-local.php /var/www/api/api/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }

cp /var/builds/configs/api/yii /var/www/api/yii || { echo 'mv failed for environment settings' ; exit 1; }

# Update SQL
php /var/www/api/yii migrate || { echo 'failed to update SQL' ; exit 1; }

function finish {
  rm -rf /var/www/api.old
}

trap finish EXIT
