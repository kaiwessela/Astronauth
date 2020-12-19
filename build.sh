#! /bin/bash

# clear build folder
rm -rf build

# create new build folder and subfolders
mkdir build
mkdir build/astronauth
mkdir build/libs
mkdir build/libs/Astronauth

# copy
cp -r frontend/. build/astronauth
cp -r core/. build/libs/Astronauth
