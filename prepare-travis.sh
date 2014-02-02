#!/bin/bash
#
# @copyright (c) 2013 phpBB Group
# @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
#
VENDOR=$1
NAME=$2
DB=$3

# clone phpBB
git clone "git://github.com/phpbb/phpbb.git" "./../../phpbb"

# make the extension directory
mkdir -p ./../../phpbb/phpBB/ext/$VENDOR/$NAME/travis

# move the current working directory (which contains the root of the main repository) to the extension directory
mv * ./../../phpbb/phpBB/ext/$VENDOR/$NAME

# move the test-framework/travis files to the ext dir (-n means no overwriting files)
mv -n ./../../test-framework/travis/* ./../../phpbb/phpBB/ext/$VENDOR/$NAME/travis

# move the test-framework/tests files to the ext dir (-n means no overwriting files)
mv -n ./../../test-framework/tests/* ./../../phpbb/phpBB/ext/$VENDOR/$NAME/tests

# move ourselves to the root phpBB directory
cd ./../../phpbb/phpBB

# Setup phpBB dependencies
php ./../composer.phar install --dev --no-interaction --prefer-source

# move ourselves to the root directory of the checked out phpbb repository
cd ./../

# Setup the tests/travis
sh -c "if [ '$DB' = 'postgres' ]; then psql -c 'DROP DATABASE IF EXISTS phpbb_tests;' -U postgres; fi"
sh -c "if [ '$DB' = 'postgres' ]; then psql -c 'create database phpbb_tests;' -U postgres; fi"
sh -c "if [ '$DB' = 'mariadb' ]; then ./travis/setup-mariadb.sh; fi"
sh -c "if [ '$DB' = 'mysql' -o '$DB' = 'mariadb' ]; then mysql -e 'create database IF NOT EXISTS phpbb_tests;'; fi"
./travis/install-php-extensions.sh
phpenv rehash
sh -c "if [ `php -r "echo (int) version_compare(PHP_VERSION, '5.3.19', '>=');"` = "1" ]; then ./travis/setup-webserver.sh; fi"
