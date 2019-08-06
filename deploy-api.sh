#!/bin/bash
# located in /usr/local/bin/deploy-api.sh
echo "Unzipping build $2"
unzip -d /var/builds/api/$1/ /var/builds/api/$2 || { echo 'unzip failed on build' ; exit 1; }

mv -v /var/www/api /var/www/api.old || { echo 'mv to old failed' ; exit 1; }
mv -v /var/builds/api/$1/ /var/www/api/ || { echo 'mv to backend failed' ; exit 1; }
function finish {
  rm -rf /var/www/api.old
}

trap finish EXIT
