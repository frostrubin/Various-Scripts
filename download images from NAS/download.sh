#!/bin/bash
# This Script finds all files named Preview.jpg in the specified search_dir
# and downloads copies them into a special folder.
# The are renamed according to the folder they were contained in.

search_dir="/Volumes/shared/Images/"  # HAS !! to end with /
image_dir="./new/"  # HAS !! to end with /
find ${search_dir} -name "Preview.jpg" > ~/Desktop/meineeigeneliste.txt
 
 
cat ~/Desktop/meineeigeneliste.txt | while read line; do
  cp "$line" $image_dir
  chmod 777 ${image_dir}Preview.jpg
  #echo filepath, 
  #remove search_dir, 
  #replace first occurence of "/" with nothing, 
  #all others with " - ". 
  #the quote types (" vs ') are important
  mv ${image_dir}Preview.jpg ${image_dir}"$(echo ${line%/*}|sed "s|$search_dir||g"|sed 's|/||' |sed 's|/| - |g')"".jpg"
done
