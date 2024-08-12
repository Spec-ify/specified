#!/usr/bin/env bash

# https://stackoverflow.com/a/1482133
SCRIPT_DIR=$(dirname -- "$( readlink -f -- "$0"; )")

# held files list must contain absolute paths
HELD_FILES_LIST="$SCRIPT_DIR/held_files"

# get files older than 24 hours
OLD_FILES=$( find $SCRIPT_DIR/files -mindepth 1 -mtime +0 -type f )

for fileName in $OLD_FILES
do
  if grep -Fxq "$fileName" "$HELD_FILES_LIST"; then
    echo "$fileName held"
  else
    rm $fileName
  fi
done
