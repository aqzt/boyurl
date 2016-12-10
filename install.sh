#!/bin/bash
## Robert yu 2016-12-09
## http://www.aqzt.com
echo "*/5 * * * * curl -fsSL http://$1/$2/boyurl_cron.txt  | sed  's/\r//g' | sh" >> /var/spool/cron/root
echo "ok" > boyurl_cron.txt
echo "ok" > boyurl_ip.txt
chmod 777 boyurl_cron.txt
chmod 777 boyurl_ip.txt
crontab -l
