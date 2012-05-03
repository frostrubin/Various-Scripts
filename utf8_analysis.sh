#!/bin/bash
searchpath=`pwd`
searchpathlength=`echo ${#searchpath}`

echo "PDF Metadaten sind falsch:"
SAVEIFS=$IFS
IFS=$(echo -en "\n\b")
for i in $( find "$searchpath" -name "*.pdf" ); do
  #echo "$i"
  FILENAME=`echo ${i##*/}`; 
  #echo "$FILENAME"


  FILEPATH=`echo ${i%/*}`;
  PARENTFOLDER=`echo ${FILEPATH:$searchpathlength:1000}`
  PARENTFOLDER=`echo ${PARENTFOLDER:1:1000}`

  #echo "$PARENTFOLDER"


  AUTHOR=`mdls -name kMDItemAuthors "$i"`
  AUTHOR=`echo "$AUTHOR"|sed -n '2p'`           # Zweite Zeile ausgeben
  AUTHOR=`echo ${AUTHOR:5:1000}`                # Erst ab Character 6 ausgeben
  AUTHOR=`echo "$AUTHOR"|sed 's/\(.*\)"/\1/'`   # Letztes Vorkommen von " löschen
  AUTHOR="${AUTHOR#"${AUTHOR%%[![:space:]]*}"}" # Remove leading whitespaces
  AUTHOR="${AUTHOR%"${AUTHOR##*[![:space:]]}"}" # Remove trailing whitespaces
  
  if [ "$PARENTFOLDER" != "$AUTHOR" ] && 
     [ "$PARENTFOLDER" != "BXfH" ]    &&
     [ "$AUTHOR"       != "Antoine de Saint-Exupe\U0301ry" ] &&
     [ "$AUTHOR"       != "Terry Pratchett" ] &&
     [ "$PARENTFOLDER" != "Galileo Press" ]   &&
     [ "$AUTHOR"       != "Patrick Su\U0308skind" ] &&
     [ "$AUTHOR"       != "Joachim Ko\U0308rber" ]  &&
     [ "$PARENTFOLDER" != "Terry Pratchett/Terry Pratchett English/Discworld" ]; then
  	  echo "$i"
  	  echo Author "$AUTHOR"
  	  echo Folder "$PARENTFOLDER"
  fi
  
done
IFS=$SAVEIFS


echo " A C H T U N G  -  S C H W E R E   F E H L E R"
#echo "Name enthält *"
find "$searchpath" -name "*\**"
#echo "Name enthält >"
find "$searchpath" -name "*\>*"
#echo "Name enthält <"
find "$searchpath" -name "*\<*"
#echo "Name enthält :"
find "$searchpath" -name "*:*"
#echo "Name enthält /"
find "$searchpath" -name "*/*"
#echo "Name enthält \\"
find "$searchpath" -name "*\\\\*"
#echo "Name enthält \""
find "$searchpath" -name "*\"*"
#echo "Name enthält |"
find "$searchpath" -name "*|*"
#echo "Name enthält ?"
find "$searchpath" -name "*\?*"
#echo "Name endet in Space"
find "$searchpath" -name "* "
#echo "Order endet in ."
find "$searchpath" -type d -name '*.'


echo  " "
echo "N O N - A S C I I"

#echo "Name ist Non-Ascii"
SAVEIFS=$IFS
IFS=$(echo -en "\n\b")
for i in $( find "$searchpath" ); do
  FILEPATH=`echo ${i:$searchpathlength:1000}`
  echo "$FILEPATH" | perl -ne 'print if /[^[:ascii:]-]/'
done
IFS=$SAVEIFS









