#!/bin/sh

PHONEBOOK="/var/flash/phonebook"

# Read Netcat Input Line by Line
nc localhost 1012 | while read -r message; do
  TYPE=$( echo "$message" | sed 's/.*;RING/RING/' | sed 's/;.*//' )
  if [ "$TYPE" == "RING" ]; then
    CALLNUMBER=$( echo "$message" | \
                  sed 's/.*;RING;0;//' | \
                  sed 's/.*;RING;1;//' | \
                  sed 's/.*;RING;2;//' | \
                  sed 's/.*;RING;3;//' | \
                  sed 's/.*;RING;4;//' | \
                  sed 's/;.*//g' )

    if [ "$CALLNUMBER" == "" ]; then
      NAME="Unbekannt"
    else
      # Remove all non-numeric characters from the number
      NUMBER=$( echo $CALLNUMBER | tr -cd '0-9' )    

      # Get the first matching line in the phone book for this number
      # Read Phonebook, leave only numeric, grep, get only line number entries, get first entry
      LINE1=$( cat "$PHONEBOOK" | tr -cd '0-9\n' | grep -n "$NUMBER" | sed 's/:.*//' | head -n 1 )

      # Get Name of Calling Person
      # Read Phonebook up to the found $LINE1
      # Remove everything after the number (by replacing it with the number)
      # Get the last <realName> (via grep and then tail)
      # Remove everything before <realName>
      # Remove everytihng after </realName>
      if [ "$LINE1" != "" ]; then
        NAME=$( head -n "$LINE1" "$PHONEBOOK" | \
                sed "s/$NUMBER.*/$NUMBER/" | \
                grep '<realName>' | \
                tail -n 1 | sed 's/.*<realName>//' | \
                sed 's/<\/realName>.*//'
              )
      fi

      if [ "$NAME" == "" ]; then
        NAME="$CALLNUMBER"
      fi

      # Replace HTML Ampersand with %26
      NAME=$( echo "$NAME" | sed 's/&amp;/%26/g' )
      # Replace whitespace with + sign
      NAME=$( echo "$NAME" | sed 's/ /+/g' )
    fi
    
    # Send notifications
    cat /var/flash/iPadNotify/emails.txt | while read -r email; do
      /var/flash/iPadNotify/curl -d "email=$email" \
        -d "&notification[from_screen_name]=Anruf+von" \
        -d "&notification[message]=$NAME" \
        http://boxcar.io/devices/providers/MH0S7xOFSwVLNvNhTpiC/notifications      
    done
  fi
done
