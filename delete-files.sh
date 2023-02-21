#!/usr/bin/env bash

# held files list must contain absolute paths
HELD_FILES_LIST="held_files"

# https://stackoverflow.com/a/246128/
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# get files older than 24 hours
OLD_FILES=$( find $SCRIPT_DIR/files -mindepth 1 -mtime +0 -type f )

for fileName in $OLD_FILES
do
  if [ $( grep -q "$fileName" "$HELD_FILES_LIST" ) ]; then
    echo "$fileName held"
  else
    rm $fileName
  fi
done
