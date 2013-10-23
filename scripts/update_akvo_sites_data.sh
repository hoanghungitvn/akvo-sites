#!/bin/bash

RUN_PROJ_UPDATES_SCRIPT="http://akvofoundation.org/updateAkvoSite.php?id_organisation"
LOG_TIME_STAMP=`date +%Y%m%d_%H%M%S`
LOG_FILE_NAME="proj_updates_log_$LOG_TIME_STAMP.html"
LOG_FILE="/var/log/akvo/sites/$LOG_FILE_NAME"
LOG_FILE_URL="http://akvofoundation.org/log/sites/$LOG_FILE_NAME"

for organisation_id in 272 275 539 912 1060 1061 1093
do
    echo "Updating organisation $organisation_id"
    printf "<br />[`date --rfc-3339=seconds`]<br />" >> $LOG_FILE
    curl $RUN_PROJ_UPDATES_SCRIPT=$organisation_id 2>&1 >> $LOG_FILE
    printf "<br />" >> $LOG_FILE
done

echo $LOG_FILE_URL | mail -s '[sites.akvo.org] Akvo Sites project updates refreshed' devops@akvo.org