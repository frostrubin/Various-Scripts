#!/bin/bash

function create_gallery {
echo "Reading Images:"
head -n `cat "$zielpfad"|grep -n "<create_gallery>"|cut -f1 -d:` "$zielpfad" > "$galleryfile"
echo -e \
[role=\"gallerytable\"]"\n"\
[format=\"psv\",cols=\"^1,^1,^1,^1\",grid=\"none\"]"\n"\
\|=================================================== >> "$galleryfile"

for i in $( find "$quellordner" \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.gif" -o -name "*.png" \) );do
 i=`echo "$i"|sed 's/^.//'`
 echo "$i"
 echo \ \| image:"$i"[height=120, link=\""$i"\"] >> "$galleryfile"  # Für vim: "
done
echo \|=================================================== >> "$galleryfile"
cat "$galleryfile" > "$zielpfad"                   # Für vim "
rm "$galleryfile"
}

galleryfile="tmp_galleryfile.txt"
quellordner="./media/real_life/wohnideen"
zielpfad="./pages/real_life/wohnideen.asciidoc"

create_gallery



