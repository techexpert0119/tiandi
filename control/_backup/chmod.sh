#!/bin/bash
# set file permissions so can be downloaded

for FILE in `ls /var/www/vhosts/e-tiandi.com/httpdocs/control/_backup/files/*.gz`
do
  chmod 644 ${FILE}
done

for FILE in `ls /var/www/vhosts/e-tiandi.com/httpdocs/control/_backup/mysql/*.gz`
do
  chmod 644 ${FILE}
done
