#!/bin/bash

DeRez -e -only icns `printf ./folder/Icon'\r'` > ./dieter.txt

linecount=`cat ./dieter.txt |wc -l`
echo $linecount
lesslines=$(($linecount-2))
echo $lesslines

cat dieter.txt |sed -n '2,'$lesslines'p' > dieter2.txt


#cat dieter2.txt| head -n 8
# remove last occ. of */
cat dieter2.txt | cut -b 59-74 | sed 's/\(.*\)\*\//\1/' > dieter3.txt



cat dieter3.txt | sed -n -e ":a" -e "$ s/\n//gp;N;b a" > dieter4.txt

awk 'length > 128 { while ( length($0) > 128 ) {
    printf "%s\n", substr($0,1,128)
    $0 = substr($0,129)
  }
  if (length) print
  next
}
{print}' "dieter4.txt" > dieter6.txt

cat dieter6.txt | sed 's/\./ /g' > dieter7.txt



exit
cat dieter4.txt | sed 's/\./ /g' | sed 's/[ \t]*$//'



exit
#................ 
#................

#cat dieter2.txt | cut -b 4-7,9-12,14-17,19-22,24-27,29-32,34-37,39-42 | sed 's/"//g' > olaf3.txt
cat dieter2.txt | cut -b 4-42 | sed 's/"//g' > olaf4.txt

format1='" $@"' 
format2='"%04X "' 
format3='"@\n"' 


cat olaf4.txt
#hexdump -v -e " $format1 8/2 $format2 $format3" | tr '@' '"'