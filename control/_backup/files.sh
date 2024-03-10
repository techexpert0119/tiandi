#!/bin/bash
# extract and import most recent files from live server

GZ=''
for FILE in `ls /var/www/vhosts/e-tiandi.com/httpdocs/control/_backup/files/*.gz`
do
  GZ=${FILE}
done

if [ "${GZ}" != '' ] ; then
  echo "Extracting files backup ${GZ}"
  cd /var/www/vhosts/e-tiandi.com/httpdocs
  tar -xzf ${GZ} --overwrite
  chown -R tiandi *
fi
