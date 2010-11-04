#!/bin/bash

weatherfile="$HOME/.NerdTool/Files/weatherdata.txt"

if [ ! -s $weatherfile ]; then
   echo " "
	exit
fi
xmldata=`cat $weatherfile`

# Get City
city_data=`echo -e $xmldata | grep "city data"|sed s/"<city data=\""//`
city_data=`echo $city_data | cut -d , -f 1,1`
# Get Time of Measurement
current_date_time=`echo -e $xmldata | grep "current_date_time data"|sed s/"<current_date_time data=\""// |awk '{print $2}'|rev`
current_date_time=`echo ${current_date_time:3}|rev`
# Get current temperature
current_temperature=`echo -e $xmldata | grep "temp_c data"|sed s/"<temp_c data=\""//`
# Get wind direction
current_wind_direction=`echo -e $xmldata | grep "wind_condition data"|sed s/"<wind_condition data=\"Wind: "// | sed s/[^A-Z]//g`
# Get wind speed
current_wind_speed=`echo -e $xmldata | grep "wind_condition data"|sed s/"<wind_condition data=\"Wind: "// | sed s/[^0-9]//g`
current_wind_speed=`echo $current_wind_speed \* 1.6 | bc`
# Get humidity
current_humidity=`echo -e $xmldata | grep "humidity data"|sed s/"<humidity data=\"Humidity: "//`
# Get current Conditions
current_conditions=`echo -e $xmldata | grep "<condition data"|sed s/"<condition data=\""//g | head -n 1`


# Output
echo $city_data", "$current_date_time
echo -ne $current_humidity H, $current_temperature$'\xB0'C '\n'
echo Wind: $current_wind_direction $current_wind_speed km/h
echo $current_conditions

