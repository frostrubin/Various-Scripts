#!/bin/bash

# Title........: block
# Description..: shell based binary clock
# Author.......: Mitchell Johnston - uid 0 
# Contact....: http://www.bdragon.net
# Last Modified: Mon Sep 07 2009 
#----------------------------------

#--------------------------- 
# This script was something I made just messing around. It's a binary clock, and
# a very simple one at that. Only one option '-12', to use 12 hour format.

# variables
#----------------------------------
NORM=$(tput sgr0)                          # Turn of all attributes
LIGHT_REDF=$(tput setaf 1 ; tput bold )    # Light Red foreground
LIGHT_GREENF=$(tput setaf 2 ; tput bold )  # Light Green foreground
LIGHT_YELLOWF=$(tput setaf 3 ; tput bold ) # Light Yellow foreground
LIGHT_BLUEB=$(tput setab 4 ; tput bold )   # Light Blue background
ROWS=$(tput lines)                         # Number of row in terminal
COLS=$(tput cols)                          # Number of columns in the terminal
typeset -R $BHOUR

# functions
#----------------------------------
xtitle(){ ## display string in window title
        printf "\033]0;[$*] \007"
}

# main
#--------------------------- 

while :
do
    ## hour
    HOUR=$(date +%I)
    TM=$(date +%p)
    BHOUR=$(echo "obase=2; $HOUR"|bc)

    ## minute
    MINUTE=$(date +%M)
    BMINUTE=$(echo "obase=2; $MINUTE"|bc)

    ## seconds
    SECOND=$(date +%S)
    BSECOND=$(echo "obase=2; $SECOND"|bc)

    ## display
    clear
    xtitle "$HOUR:$MINUTE:$SECOND $TM"
    echo "${LIGHT_REDF}$BHOUR:$BMINUTE:$BSECOND${NORM}"
    sleep 1
done
