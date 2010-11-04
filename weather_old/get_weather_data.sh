#!/bin/bash

### Variables
# The Weather Icon
weatherimage="$HOME/.NerdTool/Files/weather.png"
weatherfile="$HOME/.NerdTool/Files/weatherdata.txt"
# The SSIDs for WLAN Based Location finding
SSIDs=(
"WUENSCH" "Reinheim, Germany" "http://ca.weather.yahoo.com/germany/hesse/reinheim-687697/"
"Lecker Pizza" "Darmstadt, Germany" "http://ca.weather.yahoo.com/germany/hesse/darmstadt-643787/"
"BaWebAuth" "Mannheim, Germany" "http://ca.weather.yahoo.com/germany/baden-wurttemberg/mannheim-673711/"
"unser netz" "Reinheim, Germany" "http://ca.weather.yahoo.com/germany/hesse/reinheim-687697/"
)

# Initialize main variables
locationstring=""
yahoocode=""
### Get location by WLAN
# Get the current SSID
network_name=`system_profiler SPAirPortDataType`

# Check if AirPort is Connected
if [[ $network_name == *Status:\ Connected* ]];then
   ssid=`echo $network_name | awk '{ split($0,a,"Current")
   split(a[2],b,"PHY")
   print b[1] }' | sed s/"Network Information:"//g |  sed 's/^[ \t]*//;s/[ \t]*$//'`

   ssid=$(echo ${ssid%:})
   
   for (( i = 0 ; i < ${#SSIDs[@]} ; i++ )); do
      #echo ${SSIDs[$i]}
      if [ "${SSIDs[$i]}" == "$ssid" ]; then
         locationstring=${SSIDs[$i+1]}
         yahoocode=${SSIDs[$i+2]} 
      fi
   done   
fi

# Location was not found via WLAN. Try to find it via external IP
if [ "$locationstring" == "" ]; then

# Get current Location with hostip.info
   geo=`curl -s --connect-timeout 5 "http://api.hostip.info/get_html.php?position=true"`
   country=`echo "$geo"| grep "Country"|sed s/"Country: "//g | sed s/..\(*.\)//g`
   city=`echo "$geo"| grep "City"|sed s/"City: "//g`

# If no city could be found, try geobytes IP Service
   if [[ $city == *Unknown* ]] || [ "$city" == "" ] || [ $city == *Private* ];then
      alternate_geo=`curl -s --connect-timeout 10 "http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt"`
      country=`echo -e $alternate_geo | awk 'BEGIN{FS="<meta name=\"country\"";RS=">"}/</{print $2}' | sed '/^$/d' | sed 's/content=//g' | sed 's/\"//g' | sed -e 's/^[ \t]*//'`
      city=`echo -e $alternate_geo | awk 'BEGIN{FS="<meta name=\"city\"";RS=">"}/</{print $2}' | sed '/^$/d' | sed 's/content=//g' | sed 's/\"//g' | sed -e 's/^[ \t]*//'`
   fi

# If still no city could be found: clean up!
   if [[ $city == *Unknown* ]] || [ "$city" == "" ] || [ $city == *Private* ];then
      rm -f $weatherimage
      rm -f $weatherfile
      exit
   fi
   locationstring=$city", "$country
fi

### Get Weather Data
location="http://www.google.com/ig/api?weather="$locationstring"&hl=en&encoding=utf-8"
weatherdata=`curl -s --connect-timeout 10 $location`


# Check if data was received
if [ "${#weatherdata}" -gt "600" ];then
   echo $weatherdata | sed 's/>/\\\n/g' | sed s/"\"\/"//g > $weatherfile
else
   rm -f $weatherimage
   rm -f $weatherfile
   exit
fi

if [ $yahoocode != "" ]; then
# Get yahoo weather image
picture=$(curl --silent $yahoocode | grep "forecast-icon" | sed "s/.*background\:url(\'\(.*\)\')\;\ _background.*/\1/")
curl --silent $picture -o $weatherimage
else
   rm -f $weatherimage
fi
