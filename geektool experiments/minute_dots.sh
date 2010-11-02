#!/bin/bash

dotimage="$HOME/.NerdTool/files/minute_dots.png"
minute=`date +%M | awk '{print $1 + 0}'`
if [ "$minute" == "0" ] || [ ! -f $dotimage ]; then
rm -f $dotimage
/usr/local/bin/convert -size 500x500 xc:transparent -fill white -draw 'circle 250,10 250,15' $dotimage
else

/usr/local/bin/convert -rotate 180 \
  <(/usr/local/bin/convert -chop 25x25 \
    <(/usr/local/bin/convert -rotate 180 \
      <(/usr/local/bin/convert -chop 25x25 \
        <(/usr/local/bin/convert -rotate 6 -background none -gravity Center $dotimage \
        png:-) \
      png:-) \
    png:-) \
  png:-) \
$dotimage
#Um nur den Punkt zu haben: einfach das erste $dotimage umbenennen!
/usr/local/bin/convert $dotimage -fill white -draw 'circle 250,10 250,15' $dotimage
fi