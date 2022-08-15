#!/bin/bash
# located in /usr/local/bin/deploy.sh
# deploy.sh build-$BITBUCKET_COMMIT build-$BITBUCKET_COMMIT.zip $BUILD_PATH $BITBUCKET_BRANCH'
# $1 commit to unzip
# $2 zip file to unzip
# $3 Path to unzip to
# $4 branch path (used for Yii init command)

YII_ENV_INIT="Unknown"
if [ $4 = "master" ];then
  YII_ENV_INIT="Production"
elif [ $4 = "development" ];then
  YII_ENV_INIT="Development"
fi

echo "Unzipping build $2"
echo "Deploying for $4"
unzip -q -d /var/builds/$3/$1/ /var/builds/$3/$2 || { echo 'unzip failed on build' ; exit 1; }

# Move current www to old folder
mv -v /var/www/$3 /var/www/$3.old || { echo 'mv to old failed' ; exit 1; }
mv -v /var/builds/$3/$1/ /var/www/$3/ || { echo 'mv to backend failed' ; exit 1; }

# Sync over all local files to the new directory
# TODO Cannot get this to work so just moving this
# rsync -rav --include="*-local.php" --exclude="*" /var/www/$3.old/ /var/www/$3/ || { echo 'failed to move local files'; exit 1;}

# Sync over all local uploads into the new directory
# This may fail due to no uploads
# TODO Cannot get this to work so just moving this
# rsync -rav /var/www/$3.old/frontend/web/uploads/* /var/www/$3/frontend/web/uploads/ > /dev/null
mkdir -p /var/www/$3/frontend/web/images || { echo ' mkdir failed on uploads'; exit 1; }
cp -r /var/www/$3.old/frontend/web/images/* /var/www/$3/frontend/web/images/


## Copy main-local and params-local for COMMON configuration
cp /var/www/$3.old/common/config/main-local.php /var/www/$3/common/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/www/$3.old/common/config/params-local.php /var/www/$3/common/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }
## Copy main-local and params-local for CONSOLE configuration
cp /var/www/$3.old/console/config/main-local.php /var/www/$3/console/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/www/$3.old/console/config/params-local.php /var/www/$3/console/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }
## Copy main-local and params-local for API configuration
cp /var/www/$3.old/api/config/main-local.php /var/www/$3/api/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/www/$3.old/api/config/params-local.php /var/www/$3/api/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }
## Copy main-local and params-local for FRONTEND configuration
cp /var/www/$3.old/frontend/config/main-local.php /var/www/$3/frontend/config/main-local.php || { echo 'mv failed for environment settings' ; exit 1; }
cp /var/www/$3.old/frontend/config/params-local.php /var/www/$3/frontend/config/params-local.php || { echo 'mv failed for environment settings' ; exit 1; }
## Copy API index and Frontend index
cp /var/www/$3.old/frontend/web/index.php /var/www/$3/frontend/web/index.php || { echo 'mv failed for index.php'; }
cp /var/www/$3.old/api/web/index.php /var/www/$3/api/web/index.php || { echo 'mv failed for index.php'; }
## Move Yii
cp /var/www/$3.old/yii /var/www/$3/yii || { echo 'mv failed for yii'; }

# Copy local files to new deployment
# not used at this point
#php /var/www/$3/init --env=$YII_ENV_INIT --overwrite=n || { echo 'mv failed for environment settings' ; exit 1; }

# Update SQL
php /var/www/$3/yii migrate --interactive=0 || { echo 'failed to update SQL' ; exit 1; }


function finish {
  rm -rf /var/www/$3.old
}

trap finish EXIT
