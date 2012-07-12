#!/bin/bash

function create_move_files() {
word=$(cat hund.txt)
counter=0
linecounter=1
SAVEIFS=$IFS
IFS=""
while read -r line; do
  echo "$line" >> /tmp/mytempfile"$linecounter".temp.txt
  let counter=counter+1
  if [ $counter -eq 5 ];then
    let linecounter=linecounter+1
    counter=0
  fi
done <<< "$word"
IFS=$SAVEISF
}

ls /tmp/mytempfile*.temp.txt >/dev/null
if [ $? -ne 0 ];then
  create_move_files;
fi


word=$(cat /tmp/mytempfile1.temp.txt)

number_of_files=$(ls /tmp/*temp.txt|wc -l)

number_of_lines=5
longest_line_cols=$(echo "$word"|awk '{ if (length($0) > max) {max = length($0); maxline = $0} } END { print maxline }'|wc -m 2>/dev/null)

cols=$(tput cols)
how_long=$(( $cols - $longest_line_cols ))

echo hello
counter=0
mycounter=1
while [ $counter -lt $how_long ]; do
  word=$(cat /tmp/mytempfile"$mycounter".temp.txt)
  SAVEIFS=$IFS
  IFS=""
  while read -r line; do
    mycounter1=1
    while [ $mycounter1 -lt $counter ]; do
      echo -n " " # Führend Nullen
      let mycounter1=mycounter1+1
    done
    echo "$line" # Zeile Ausgeben
  done <<< "$word"
  IFS=$SAVEISF

  sleep 0.3
  linecounter=0
  while [ $linecounter -lt $number_of_lines ]; do
    tput cuu1 # N Zeilen nach oben gehen
    let linecounter=linecounter+1 
  done
  let counter=counter+1
  let mycounter=mycounter+1
  if [ $mycounter -gt $number_of_files ]; then
    mycounter=1
  fi
done


















exit
# This works to move a cat around!
word="Hello World"
word=$(cat neu.txt)
last_line_length=$(echo "$word"|tail -n 1|wc -m 2>/dev/null)
number_of_lines=$(echo "$word"|wc -l 2>/dev/null)

counter=0
while [ $counter -lt 200 ]; do
  while read -r line; do
    mycounter1=0
    while [ $mycounter1 -lt $counter ]; do
      echo -n " " # Führend Nullen
      let mycounter1=mycounter1+1
    done
    echo "$line" # Zeile Ausgeben
  done <<< "$word"

  sleep 0.1
  linecounter=0
  while [ $linecounter -lt $number_of_lines ]; do
    tput cuu1 # N Zeilen nach oben gehen
    let linecounter=linecounter+1 
  done
  let counter=counter+1
done
