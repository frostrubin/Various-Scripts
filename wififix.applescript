#!/usr/bin/env osascript

do shell script "rm -f /Library/Preferences/SystemConfiguration/com.apple.airport.preferences.plist*; 
                         rm -f /Library/Preferences/SystemConfiguration/com.apple.wifi.message-tracer.plist*;
           		     rm -f /Library/Preferences/SystemConfiguration/NetworkInterfaces.plist*;
		     	     rm -f /Library/Preferences/SystemConfiguration/preferences.plist*;" with administrator privileges

--tell application "Finder" to restart