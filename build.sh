#! /bin/bash

# clear build folder
rm -rf build

# create new build folder and subfolders
mkdir build
mkdir build/backend
mkdir build/config
mkdir build/share

# copy
cp -r src/backend/. build/backend
cp -r src/config/. build/config
cp -r src/frontend/. build
cp -r src/share/. build/share
