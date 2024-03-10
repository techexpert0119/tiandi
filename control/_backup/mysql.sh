#!/bin/bash
# extract and import most recent mysql live backup to database

# SERVER=db5001016344.hosting-data.io
# DATABASE=dbs879370
# USER=dbu7147
# PASSWORD=mq^50ct?AVg7

SERVER=127.0.0.1
DATABASE=tiandi_en
USER=t14nd1tian
PASSWORD='mf32s$jW_G2'

# get most recent backup
GZ=''
for FILE in `ls /var/www/vhosts/e-tiandi.com/httpdocs/control/_backup/mysql/*.gz`
do
  GZ=${FILE}
done

if [ "${GZ}" != '' ] ; then
  echo "Most recent mySQL backup ${GZ}"
  gzip -dk ${GZ}
  SQL=$(echo ${GZ} | sed "s/.sql.gz/.sql/") 
  echo "Importing ${SQL} to database"
  mysql --host=${SERVER} --user=${USER} --password=${PASSWORD} ${DATABASE} < ${SQL}
  rm -f ${SQL} 
fi 
