#!/bin/bash
[[ -e $PWD/composer.phar ]] || { echo >&2 "Please cd into the bundle before running this script."; exit 1; }

php -f main.php
