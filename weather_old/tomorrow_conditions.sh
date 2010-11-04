#!/bin/bash

weatherfile="$HOME/.NerdTool/Files/weatherdata.txt"

if [ ! -s $weatherfile ]; then
   echo " "
	exit
fi
xmldata=`cat $weatherfile`

# Get data
tomorrow_dayname=`echo -e $xmldata | grep "day_of_week data"|sed s/"<day_of_week data=\""//g| head -n 2 | tail -n 1`
tomorrow_dayname=`echo $tomorrow_dayname | sed 's/Sun/Sunday/g;s/Mon/Monday/g;s/Tue/Tuesday/g;s/Wed/Wednesday/g;s/Thu/Thursday/g;s/Fri/Friday/g;s/Sat/Saturday/g'`
tomorrow_temperature_low=`echo -e $xmldata | grep "low data"|sed s/"<low data=\""//g| head -n 2 | tail -n 1`
tomorrow_temperature_low=$((($tomorrow_temperature_low-32)*5/9))
tomorrow_temperature_high=`echo -e $xmldata | grep "high data"|sed s/"<high data=\""//g| head -n 2 | tail -n 1`
tomorrow_temperature_high=$((($tomorrow_temperature_high-32)*5/9))
tomorrow_conditions=`echo -e $xmldata | grep "<condition data"|sed s/"<condition data=\""//g| head -n 3 | tail -n 1`

echo $tomorrow_dayname
echo -ne $tomorrow_temperature_low$'\xB0'C/$tomorrow_temperature_high$'\xB0'C '\n\n'
echo $tomorrow_conditions

