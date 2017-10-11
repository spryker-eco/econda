#!/usr/bin/env bash
echo "Trying to move the module to a subfolder..."
mkdir module
mv * module/
result=$?
if [ "$result" = 0 ]; then
    echo 'Module successfully moved to subfolder'
fi
echo 'Failed to move the module to a subfolder'
