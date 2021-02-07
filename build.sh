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


# copy fonts
cp vendor/redhatofficial/overpass/webfonts/overpass-webfont/overpass-regular.woff2 build/astronauth/resources/fonts/
cp vendor/redhatofficial/overpass/webfonts/overpass-webfont/overpass-italic.woff2 build/astronauth/resources/fonts/
cp vendor/redhatofficial/overpass/webfonts/overpass-webfont/overpass-bold.woff2 build/astronauth/resources/fonts/
