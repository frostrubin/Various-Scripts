#!/bin/bash

function successFail() {
  if [ $? = 0 ];then
    echo -ne "Success\n"   
  else
    echo -ne "Fail\n"
  fi
}

oldpath=`pwd`;
mkdir ./gitbackups
cd ./gitbackups
# Get a list of all my repos:
reps=$(curl --silent http://github.com/api/v2/yaml/repos/show/frostrubin | grep ":url:" | sed s/"- :url: "//g)
echo "$reps"
for i in $(echo "$reps"); do
  a=`echo "$i"|sed -e 's%https://github.com/frostrubin/%%g'`
  echo -n Cloning $a.git
  git clone $i.git > /dev/null 2>&1
  successFail;
  echo -n Cloning $a.wiki.git
  git clone $i.wiki.git > /dev/null 2>&1
  successFail;
done

cd $oldpath
for i in $(ls ./gitbackups/); do
  cd ./gitbackups/$i
  git pull
  cd $oldpath
done


# grep url, remove github.com part, create folder with rest name, mkdir -p,
# cd in there, git clone