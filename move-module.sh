#!/usr/bin/env bash
echo "Trying to move the module to a subfolder..."
mkdir module
if [ `mv * module/` eq 0 ]; then
    echo 'Module successfully moved to subfolder'
fi
echo 'Failed to move the module to a subfolder'
