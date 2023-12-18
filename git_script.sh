#!/bin/bash
export PATH=/bin:/usr/bin:/usr/local/bin


cd /var/www/html/

git add design/*
git -c user.name="autoCommit" -c user.email="alagiyanirav@gmail.com" commit design/* -m "Design page changes commit"
git push git@bitbucket.org:photoadking/photoadking.git master
