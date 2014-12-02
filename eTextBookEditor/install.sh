#!/bin/sh
mkdir app/cache
chmod -R 777 app/cache
mkdir app/logs
chmod -R 777 app/logs
mkdir web/books
chmod -R 777 web/books
mkdir web/publicBooks
chmod -R 777 web/publicBooks
mkdir web/tmp
chmod -R 777 web/tmp
php app/console doctrine:database:create
php app/console doctrine:schema:update --force