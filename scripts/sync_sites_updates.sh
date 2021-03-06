#!/bin/bash

mkdir -p /var/akvo/akvosites/logs/cron/

SYNC_SITES_UPDATES_SCRIPT="http://akvofoundation.org/wp-content/plugins/akvo-partner-communication/SyncController.php"
LOG_TIME_STAMP=`date +%Y%m%d_%H%M%S`
LOG_FILE_NAME="sync_updates_log_$LOG_TIME_STAMP.html"
LOG_FILE="/var/akvo/akvosites/logs/cron/$LOG_FILE_NAME"
LOG_FILE_URL="http://akvofoundation.org/logs/cron/$LOG_FILE_NAME"

printf "<br />[`date --rfc-3339=seconds`]<br />" >> $LOG_FILE
curl $SYNC_SITES_UPDATES_SCRIPT 2>&1 >> $LOG_FILE
printf "<br />" >> $LOG_FILE

echo $LOG_FILE_URL | mail -s '[sites.akvo.org] Akvo Sites project update deletions synchronised' devops@akvo.org
