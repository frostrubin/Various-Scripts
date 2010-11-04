#!/bin/bash

weatherfile="$HOME/.NerdTool/Files/weatherdata.txt"

if [ ! -s $weatherfile ]; then
   echo " "
	exit
fi
xmldata=`cat $weatherfile`

in2days_dayname=`echo -e $xmldata | grep "day_of_week data"|sed s/"<day_of_week data=\""//g| head -n 3 | tail -n 1`
in2days_dayname=`echo $in2days_dayname | sed 's/Sun/Sunday/g;s/Mon/Monday/g;s/Tue/Tuesday/g;s/Wed/Wednesday/g;s/Thu/Thursday/g;s/Fri/Friday/g;s/Sat/Saturday/g'`
in2days_temperature_low=`echo -e $xmldata | grep "low data"|sed s/"<low data=\""//g| head -n 3 | tail -n 1`
in2days_temperature_low=$((($in2days_temperature_low-32)*5/9))
in2days_temperature_high=`echo -e $xmldata | grep "high data"|sed s/"<high data=\""//g| head -n 3 | tail -n 1`
in2days_temperature_high=$((($in2days_temperature_high-32)*5/9))
in2days_conditions=`echo -e $xmldata | grep "<condition data"|sed s/"<condition data=\""//g| head -n 4 | tail -n 1`


echo $in2days_dayname
echo -ne $in2days_temperature_low$'\xB0'C/$in2days_temperature_high$'\xB0'C '\n\n'
echo $in2days_conditions
