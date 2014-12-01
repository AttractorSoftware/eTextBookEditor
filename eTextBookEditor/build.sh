#!/bin/sh
php ./composer.phar update
npm install
bower install --allow-root
grunt full
git tag > ./web/version.html
php ./app/console cache:clear --env=prod
php ./app/console cache:clear --env=dev
php ./app/console doctrine:schema:update
php ./app/console doctrine:schema:update --force --dump-sql
sudo chmod -R 777 ./app/cache