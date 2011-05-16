#!/bin/bash

echo "# Willkommen #" > ./pages/Home.md
echo >> ./pages/Home.md
SAVEIFS=$IFS
IFS=$(echo -en "\n\b")
for i in $( find . \( -name "*.md" ! -name "Home.md" ! -name "_Sidebar*" -o -name "*.asciidoc" \) );do
#echo "$i"
#voice=`echo ${i%\.*}`
heading=`head -n 1 "$i"`
heading=`echo "$heading"|sed 's/# //'` #remove leading #
heading=`echo "$heading"|sed -e "s/#*$//"`
heading=`echo "$heading"|sed -e "s/ *$//"`
#echo `basename "$i"`
#echo "$heading"
echo - \[\["$heading"\|`basename "$i"|sed 's/.md//;s/.asciidoc//'`\]\] >> ./pages/Home.md
#echo >> ./pages/Home.md /no P elements in li!!
done
IFS=$SAVEIFS

