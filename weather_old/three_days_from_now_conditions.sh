#!/bin/bash

weatherfile="$HOME/.NerdTool/Files/weatherdata.txt"

if [ ! -s $weatherfile ]; then
   echo " "
	exit
fi
xmldata=`cat $weatherfile`

# Get data
in3days_dayname=`echo -e $xmldata | grep "day_of_week data"|sed s/"<day_of_week data=\""//g| head -n 4 | tail -n 1`
in3days_dayname=`echo $in3days_dayname | sed 's/Sun/Sunday/g;s/Mon/Monday/g;s/Tue/Tuesday/g;s/Wed/Wednesday/g;s/Thu/Thursday/g;s/Fri/Friday/g;s/Sat/Saturday/g'`
in3days_temperature_low=`echo -e $xmldata | grep "low data"|sed s/"<low data=\""//g| head -n 4 | tail -n 1`
in3days_temperature_low=$((($in3days_temperature_low-32)*5/9))
in3days_temperature_high=`echo -e $xmldata | grep "high data"|sed s/"<high data=\""//g| head -n 4 | tail -n 1`
in3days_temperature_high=$((($in3days_temperature_high-32)*5/9))
in3days_conditions=`echo -e $xmldata | grep "<condition data"|sed s/"<condition data=\""//g| head -n 5 | tail -n 1`

echo $in3days_dayname
echo -ne $in3days_temperature_low$'\xB0'C/$in3days_temperature_high$'\xB0'C '\n\n'
echo $in3days_conditions