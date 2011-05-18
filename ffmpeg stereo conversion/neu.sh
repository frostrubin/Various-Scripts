#!/bin/bash

if [ $# -gt 1 ]; then
  echo "Only one parameter is allowed."
  exit;
elif [ $# -eq 1 ]; then
  




movieInfo=`/pub/Applications/Air\ Video\ Server.app/Contents/Resources/ffmpeg -i "$1" > tempfile.txt 2>&1`

#echo "$movieInfo" > tempfile.txt
aspectRatio=`cat tempfile.txt|grep "DAR"|grep "PAR"|grep "kb/s"|sed 's/.*\[\(.*\)\].*/\1/'|cut -d ' ' -f 4`
rm tempfile.txt
echo "${1##*/}" has aspect Ratio of "$aspectRatio" and is being converted in 5 seconds.
echo If this seems wrong, please press CTRL + C


sleep 7

# hier habe ich das -i rausgenommen !!

#/pub/Applications/Air\ Video\ Server.app/Contents/Resources/ffmpeg -i \
#"$1" -vcodec copy -sameq \
#-aspect "$aspectRatio" \
#-acodec aac -ab 160k -ac 2 /Users/bernhard/Desktop/output_"${1##*/}" -acodec aac -ab 160k -ac 2 -newaudio

/pub/Applications/Air\ Video\ Server.app/Contents/Resources/ffmpeg -i \
"$1" -vcodec copy -sameq \
-aspect "$aspectRatio" \
-acodec aac -ab 160k -ac 2 \
-acodec aac -ab 160k -ac 2 \
-map 0:0 -map 0:1 -map 0:2 \
/Volumes/GoFlex/stereoimport/"${1##*/}" -newaudio



say -v Pipe Finished
else
  echo "Sorry, but you have to supply at least one parameter."
  exit
fi
