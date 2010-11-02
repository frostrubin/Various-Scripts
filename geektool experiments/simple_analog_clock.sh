#!/bin/bash

###   Variables   ###
## Files ##
clockimage="$HOME/.NerdTool/files/clock.png"
## Colors ##
#RGBA
min_stroke="#FFFFFFFF"
min_fill="#FFFFFFFF"
hour_stroke="#FFFFFFFF"
hour_fill="#FFFFFFFF"

## Sizes ##
#Hand Vectors
#Each Vector starts in the middel. 
#First number is X-Axis, Second number is Y-Axis.
#+5,+10 means: from the middle, go five down and ten to the right 
hour_head="l 0,0  +20,+20  -20,-20  +20,-20 +90,+20  -90,+20"
minute_head="l 0,0 +15,+15 -15,-15 +15,-15 +130,+15 -130,+15"


###   Calculation   ###
hour=`date +%I| awk '{print $1 + 0}'`
minute=`date +%M| awk '{print $1 + 0}'`

min_position=$((($minute*6)-90))
hour_position_basic=$(($hour*30))
#Refinement of the hour hand position.
#For every ten minutes, the hour hand moves 5 additional degrees forward
#Why 5 degrees? Well....
#Every 10 minutes means, an hour is split in 6 parts.
#6*12 = 72
#360/72 = 5
#So if you want to move the hour hand every 5 minutes, instead of every 10,
#calculate: 12*12 = 144    360/144 = 2.5
hour_position_refinement=$((($minute/10)*5))
hour_position=$((($hour_position_refinement+$hour_position_basic)-90))

###   Image Creation   ###
/usr/local/bin/convert \
  <(/usr/local/bin/convert -rotate $hour_position -background none <(/usr/local/bin/convert -size 500x500 xc:transparent -draw "stroke $hour_stroke fill $hour_fill path 'M 240,250 $hour_head' " png:-) png:-) \
  <(/usr/local/bin/convert -rotate $min_position -background none <(/usr/local/bin/convert -size 500x500 xc:transparent -draw "stroke $min_stroke fill $min_fill path 'M 240,250 $minute_head' " png:-) png:-) \
  -gravity Center -composite $clockimage


