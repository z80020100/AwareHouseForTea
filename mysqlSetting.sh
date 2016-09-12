#!/bin/bash

if [ $# -ne 1 ];then
	echo "you need to add a parameter to be the password of database,
it will replace for you in the general.php"
fi

sed -i '/^$db/ s/""/"'${1}'"/g' /var/www/html/AwareHouse/includes/general.php
