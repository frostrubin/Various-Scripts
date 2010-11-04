#!/bin/bash
mkdir -p ./OutPutPNG
 
SAVEIFS=$IFS
IFS=$(echo -en "\n\b")
for i in $(ls ./test/);do
   echo "$i"
   sips -s format png ./test/"$i" \
        --out OutPutPNG/"${i%\.*}".png
done
IFS=$SAVEIFS