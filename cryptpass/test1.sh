#!/bin/bash

message="Please enter the Name of the Service: "
read -e -p "$message" service
servicepin=`echo -n "$service"|shasum -pa 512|cut -d" " -f1`

message="Please enter your secret Pin: "
counter=1
until [  $counter -gt 3 ]; do
  read -e -s -p "$message" secretpin
  echo -ne "\n"
  secretpin=`echo -n "$secretpin"|shasum -pa 512|cut -d" " -f1`
  storedpin=`cat pw.txt`

  if [ "$storedpin" == "$secretpin" ];then
    counter=4
  else
    if [ "$counter" -lt 3 ];then
      message="Sorry, that was wrong. Please try again: "
      let counter+=1
    else
      echo "Sorry, you were wrong too many times. Come back later."
      exit 1
    fi
  fi
done

# Extract only the Numbers from Pin and Service SHA
servicenum=`echo "$servicepin"|sed 's/[^0-9]*//g'`
secretnum=`echo "$secretpin"|sed 's/[^0-9]*//g'`

# Sum those two numbers up
export BC_LINE_LENGTH=999
summednum=`echo $servicenum + $secretnum|bc`

# Convert that summed number to base64
summednum64=`echo -n "$summednum"|openssl base64| tr '\n' ' '|sed 's/ //g'`

# Get the SHA 512 for that string
summedpin64=`echo -n "$summednum64"|shasum -pa 512|cut -d" " -f1`

# Finally get an md5 of that string
password=`echo -n "$summedpin64"|md5`

# Store the name of the service in a file
echo "$service" >> services.txt
servicelist=`sort services.txt | uniq`
echo "$servicelist" > services.txt

# Output the final password
echo "The Password for this service is: $password"
