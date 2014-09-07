#!/usr/bin/env bash

FILES=Rezepte.wiki/*.asciidocd
for f in $FILES; do
  echo -ne "Processing $f \n"
  # take action on each file. $f store current file name
  title=$(cat "$f" | head -n 1)
  name=${f##*/}; name=${name%\.*}
  group=$(echo "$name"|cut -d '-' -f 1)
  text_without_ifdef=$(cat "$f"|sed '/rezepttags/,/rezepttags/d')
  text_without_header=$(echo "$text_without_ifdef"|sed '1d'|sed '1d')
  text_without_image=$(echo "$text_without_header"|sed '/image:/d')
  echo -ne '---'"\n""title: $group $title""\n\n"'layout: page'"\n"'category: rezepte'"\n"'---'"\n" > /tmp/"$name".md
  echo "$text_without_image" >> /tmp/"$name".md
  cat -s /tmp/"$name".md > ./output/2014-01-01-"$name".md 
done