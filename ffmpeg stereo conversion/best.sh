#!/bin/bash

SAVEIFS=$IFS
IFS=$(echo -en "\n\b")
for i in $(ls /Volumes/GoFlex/Filme/Mit\ Kapitelnamen/*);do
echo "$i"
./neu.sh "$i"
done
IFS=$SAVEIFS