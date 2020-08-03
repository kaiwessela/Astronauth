#! /bin/bash

# clear build folder
rm -rf build

# create new build folder and subfolders
mkdir build

# copy
cp -r src/. build
