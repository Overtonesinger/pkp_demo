#!/bin/bash
[[ -e $PWD/composer.phar ]] || { echo >&2 "Please cd into the bundle before running this script."; exit 1; }

$PWD/composer.phar install

echo -e "\\nHotovo! :-)\\n    - skript EET:PKP Demo spustite prikazem:  php -f main.php"
