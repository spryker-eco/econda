#!/usr/bin/env bash

latestTag=`git tag | tail -n 1`
git checkout "$latestTag"
