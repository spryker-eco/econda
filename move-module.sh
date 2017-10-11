#!/usr/bin/env bash
echo "Trying to move the module to a subfolder..."
mkdir $BUILD_MODULE_FOLDER && ls -1 | grep -v ^$BUILD_MODULE_FOLDER | xargs -I{} mv {} $BUILD_MODULE_FOLDER
if [ "$?" = 0 ]; then
    echo 'Module successfully moved to subfolder'
fi
echo 'Failed to move the module to a subfolder'
