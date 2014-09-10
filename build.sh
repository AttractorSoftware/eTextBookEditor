#!/bin/sh
php ./composer.phar update
npm install
bower install --allow-root
grunt full
git tag > ./web/version.html
php ./app/console cache:clear --env=prod
php ./app/console cache:clear --env=dev
sudo chmod -R 777 ./app/cache