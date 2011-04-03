#!/bin/bash



pdffile=`ls ./mer/*.pdf`
imagefile=`ls ./mer/*.png`
if [ $? -ne 0 ];then
  imagefile=`ls ./mer/*.jpg`
fi
if [ $? -ne 0 ];then
  imagefile=`ls ./mer/*.jpeg`
fi
if [ $? -ne 0 ];then
  imagefile=`ls ./mer/*.gif`
fi

sips -s format pdf "$imagefile" --out a.pdf

python '/System/Library/Automator/Combine PDF Pages.action/Contents/Resources/join.py' -o \
./"${pdffile##*/}" ./a.pdf "$pdffile"

rm ./a.pdf

