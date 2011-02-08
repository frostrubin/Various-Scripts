#!/bin/bash

# Get a list of all my repos:
reps=$(curl --silent http://github.com/api/v2/yaml/repos/show/frostrubin | grep ":url:" | sed s/"  :url: "//g)
#echo "$reps"
for i in $(echo "$reps"); do 
  echo Cloning $i.git
  git clone $i.git
done

# grep url, remove github.com part, create folder with rest name, mkdir -p,
# cd in there, git clone