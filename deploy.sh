#! /bin/bash

./build.sh

rm -rf /var/www/astro.local/*
cp -r ./build/. /var/www/astro.local
