#!/usr/bin/env bash

# Make sure to display this with a fixed width font!
# The part about printing the characters below the horizontal line randomly echoes a blank line...

# Get Ethernet IP Address
function get_eth() {
	# Get the IP Address
	myen0=`ifconfig en0 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`
	# Decide wether ethernet is running or not
	if [ "$myen0" != "" ]; then
  	  address=$myen0
	else
 	   address="$ethreplacement"
	fi	
	eval "$1=$address"
	}
# Get Airport IP Address
function get_air() {
	# Get the IP Address
	myen0=`ifconfig en1 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`
	# Test wether Airport is connected or not
	if [ -n "$myen0" ]; then
 	   address=$myen0
	else
 	   address="$airreplacement"
	fi
	eval "$1=$address"
	}
# Get External IP Address
function get_ext() {
	# Get the IP Address 
	if [ -s ~/.geektoolscripts/externalip ]; then
		address=`cat ~/.geektoolscripts/externalip`
	else
		externalip=`curl -s --connect-timeout 5 http://checkip.dyndns.org/ | sed 's/[a-zA-Z<>/ :]//g'`
		if [ -n "$externalip" ]; then
 	   		address=$externalip
		else
 	   		address="$extreplacement"
		fi
	fi
	eval "$1=$address"
	}
function hvdecide() {
	RANGE=2 # Results can be 0 or 1
	number=$RANDOM
	let "number %= $RANGE"
	eval "$1=$number"
	}
function sort_them() {
	read short medium long < <(cat - | awk '{print length"\t"$0}'|sort -n|cut -f2- | paste -s -d' ' - )
	}
function find_first_match() {
	# Try to find a first match
	i=0
	while [ "$i" -eq "0" ]; do
		# Generate a random number, not higher than the charactercount of horizontal
		RANGE=${#horizontal}
		number=$RANDOM
		let "number %= $RANGE"
		if [[ $vertical1 == *${horizontal:$number:1}* ]]; then
			i=1
		fi
	done
	firstmatch=$number
	firstmatchis=${horizontal:$number:1}
	kkk=$number
	
	# $firstmatch now contains the number of a character of $horizontal, that
	# also exists in $vertical1 | Counting starts with zero

	# We have a number. Now we have to find out how often it occurs in the $vertical1 string,
	# so we can select a point for the vertical string to merge with the horizontal one.
	occur="${vertical1//[^${horizontal:$number:1}]/}"
	# Now we know that the number ${horizontal:$number:1} occurs ${#occur} times in the $vertical1 string
	
	# Select one of these occurences to be the mergepoint
	RANGE=${#occur}     
	number=$RANDOM      
	let "number %= $RANGE"
       
	# $number now contains the number of a character of $vertical1, where $horizontal will merge in.
		i=0
		counter=0
		while [ "$i" -lt "$[${#vertical1}+1]" ]; do
			#nimm=${vertical1:$i:1}
			if [ "${vertical1:$i:1}" == "$firstmatchis" ]; then
			
				if [ "$counter" -eq "$number" ]; then
					vertmatch1=$i
					i=700
				fi
			counter=$[$counter+1]
			fi
			i=$[$i+1]
		done
		# $vertmatch1 now contains the position of $number, so we can say:
		#echo "-------"
		#echo $horizontal
		#echo will be merged with
		#echo "$vertical1"
		#echo at the horizontal position 
		#echo $firstmatch
		#echo and the vertical position 
		#echo $vertmatch1
		#echo the number is 
		#echo $firstmatchis
	}
function find_second_match() {
	# Try to find a second match
	i=0
	while [ "$i" -eq "0" ]; do
		# Generate a random number, not higher than the charactercount of horizontal
		# And not identical to the first generated number $firstmatch
		RANGE=${#horizontal}
		number=$firstmatch
		while [ "$number" = "$firstmatch" ]; do
			number=$RANDOM
			let "number %= $RANGE"
		done
		if [[ $vertical2 == *${horizontal:$number:1}* ]]; then
			i=1
		fi
	done
	secondmatch=$number
	secondmatchis=${horizontal:$number:1}
	qqq=$number
	
	# $firstmatch now contains the number of a character of $horizontal, that
	# also exists in $vertical1 | Counting starts with zero

	# We have a number. Now we have to find out how often it occurs in the $vertical1 string,
	# so we can select a point for the vertical string to merge with the horizontal one.
	occur="${vertical2//[^${horizontal:$number:1}]/}"
	# Now we know that the number ${horizontal:$number:1} occurs ${#occur} times in the $vertical1 string
	
	# Select one of these occurences to be the mergepoint
	RANGE=${#occur}     
	number=$RANDOM      
	let "number %= $RANGE"
       
	# $number now contains the number of a character of $vertical1, where $horizontal will merge in.
		i=0
		counter=0
		while [ "$i" -lt "$[${#vertical2}+1]" ]; do
			#nimm=${vertical1:$i:1}
			if [ "${vertical2:$i:1}" == "$secondmatchis" ]; then
			
				if [ "$counter" -eq "$number" ]; then
					vertmatch2=$i
					i=700
				fi
			counter=$[$counter+1]
			fi
			i=$[$i+1]
		done
		# $vertmatch1 now contains the position of $number, so we can say:
		#echo "-------"
		#echo $horizontal
		#echo will be merged with
		#echo $vertical2
		#echo at the horizontal position 
		#echo $secondmatch
		#echo and the vertical position 
		#echo $vertmatch2
		#echo the number is 
		#echo $secondmatchis
		# Now I can already "imagine" the crossword puzzle.
	}
function calculation() {
		# If you want to debug, and therefore switch $decision to always be 2, 
	# make sure to set these values as well:
	 #ethhv=1
	 #airhv=1
	 #exthv=0
	
	# Determine which of the Addresses is the horizontal one
	if [ "$ethhv" -eq "0" ]; then
		horizontal=$ethip
		vertical1=$airip
		vertical2=$extip
		
		horizontalreplacement1337=$ethreplacement1337
		vertical1replacement1337=$airreplacement1337
		vertical2replacement1337=$extreplacement1337
	elif [ "$airhv" -eq "0" ]; then
		horizontal=$airip
		vertical1=$ethip
		vertical2=$extip
		
		horizontalreplacement1337=$airreplacement1337
		vertical1replacement1337=$ethreplacement1337
		vertical2replacement1337=$extreplacement1337
	elif [ "$exthv" -eq "0" ]; then
		horizontal=$extip
		vertical1=$ethip
		vertical2=$airip
		
		horizontalreplacement1337=$extreplacement1337
		vertical1replacement1337=$ethreplacement1337
		vertical2replacement1337=$airreplacement1337
	fi

	#horizlen=${#horizontal}
	#vertical1len=${#vertical1}
	#vertical2len=${#vertical2}

	if echo "$horizontal" | grep -q "^[A-Za-z]*$"; then
		# It is NOT a number
		horizontalisnumber=0
	else
		# It IS a number
		horizontalisnumber=1
	fi
	
	if echo "$vertical1" | grep -q "^[A-Za-z]*$"; then
		# It is NOT a number
		vertical1isnumber=0
	else
		# It IS a number
		vertical1isnumber=1
	fi

	if  echo "$vertical2" | grep -q "^[A-Za-z]*$"; then
		# It is NOT a number
		vertical2isnumber=0
	else
		# It IS a number
		vertical2isnumber=1
	fi

#----------------------------------------------------------------------------------------------
# Calculate a first match
	test1=$[$horizontalisnumber+$vertical1isnumber]
	if [ "$test1" -eq "2" ] || [ "$test1" -eq "0" ]; then
		# Both are numbers or both are strings
		# The normal match finding algorithm can be used
		find_first_match
	else
		if [ "$horizontalisnumber" -eq "0" ]; then
			# The horizontal line is the string
			hilfs=$horizontal
			horizontal=$horizontalreplacement1337
			# Now a match can be found
			find_first_match
			# But the old state has to be restored
			horizontal=$hilfs
			firstmatchis=${horizontal:$kkk:1}
		else
			# The vertical line is the string
			hilfs=$vertical1
			vertical1=$vertical1replacement1337
			# Now a match can be found
			find_first_match
			# But the old state has to be restored
			vertical1=$hilfs
			firstmatchis=${horizontal:$kkk:1}
		fi
	fi		
#----------------------------------------------------------------------------------------------
# Calculate a second match
	test2=$[$horizontalisnumber+$vertical2isnumber]
	if [ "$test2" -eq "2" ] || [ "$test2" -eq "0" ]; then
		# Both are numbers or both are strings
		# The normal match finding algorithm can be used
		find_second_match
	else
		if [ "$horizontalisnumber" -eq "0" ]; then
			# The horizontal line is the string
			hilfs=$horizontal
			horizontal=$horizontalreplacement1337
			# Now a match can be found
			find_second_match
			# But the old state has to be restored
			horizontal=$hilfs
			secondmatchis=${horizontal:$qqq:1}
		else
			# The vertical line is the string
			hilfs=$vertical2
			vertical2=$vertical2replacement1337
			# Now a match can be found
			find_second_match
			# But the old state has to be restored
			vertical2=$hilfs
			secondmatchis=${horizontal:$qqq:1}
		fi
	fi	
			
	# Which vertical string is the left one, which the right one?
	if [ "$firstmatch" -lt "$secondmatch" ]; then
		leftmatch=$firstmatch
		leftline=$vertical1
		leftmerge=$vertmatch1
		leftmatchis=$firstmatchis
		
		rightmatch=$secondmatch
		rightline=$vertical2
		rightmerge=$vertmatch2
		rightmatchis=$secondmatchis
	else
		rightmatch=$firstmatch
		rightline=$vertical1
		rightmerge=$vertmatch1
		rightmatchis=$firstmatchis
		
		leftmatch=$secondmatch
		leftline=$vertical2
		leftmerge=$vertmatch2
		leftmatchis=$secondmatchis
	fi
	
#	echo "There are 2 mergers on the horizontal line." 
#	echo $horizontal
#	echo ">>>>>>>>>>>>>>>>>>>>>>>>>"
#	echo "The first one is on the horizontal field:" # Counted from zero!!!
#	echo $leftmatch
#	echo "and is:"
#	echo $leftline
#	echo "at the vertical field:" # Counted from zero!!!
#	echo $leftmerge
#	echo "with the character"
#	echo $leftmatchis
#	echo "---------------------"
#	echo "The second one is on the horizontal field:" # Counted from zero!!!
#	echo $rightmatch
#	echo "and is:"
#	echo $rightline
#	echo "at the vertical field:" # Counted from zero!!!
#	echo $rightmerge
#	echo "with the character"
#	echo $rightmatchis
#	echo "------1-------"
#	echo "$decision"

	
	if [ "$leftmerge" -ge "$rightmerge" ]; then
		top=$leftmerge
	else
		top=$rightmerge
	fi

	rightskip=0
	leftskip=0

	leftheight=$[$top-$leftmerge]
	rightheight=$[$top-$rightmerge]

	}
	
function whoisthestring() {
	
	

	if echo "$leftline" | grep -q "^[A-Za-z]*$"; then
		# It is NOT a number
		leftlineisnumber=0
	else
		# It IS a number
		leftlineisnumber=1
	fi

	if  echo "$rightline" | grep -q "^[A-Za-z]*$"; then
		# It is NOT a number
		rightlineisnumber=0
	else
		# It IS a number
		rightlineisnumber=1
	fi

	test3=$[$horizontalisnumber+$leftlineisnumber]
	test4=$[$horizontalisnumber+$rightlineisnumber]	
	
	}
#######################################################################
################### E N D   O F   F U N C T I O N S ###################		
#######################################################################

# Declare the strings to be used, if no IP can be gained
# Also, here we declare the "leet" matching patterns for matching up numbers with strings
# This has the side effekt, that numbers and strings are merged at "suitable" positions.
# And no, I hate 1337 the same way you do. But in this case, it IS nice.
ethreplacement="INACTIVE"; ethreplacement1337="1%4%71%3"
airreplacement="INACTIVE"; airreplacement1337="1%4%71%3"
extreplacement="INACTIVE"; extreplacement1337="1%4%71%3"

# Get the 3 IP Addresses
# Only works on Mac i think
get_eth ethip
get_air airip
get_ext extip

# For Debugging purposes
#extip="79.216.149.38"
#ethip="192.168.0.7"
#airip="INACTIVE"

# Decide how to display the Addresses
hvdecide ethhv
hvdecide airhv
hvdecide exthv
decision=$[$ethhv+$airhv+$exthv]
# Now we know, how to display the Addresses

# Start the Hard Part ...
#decision=2
if [ "$decision" -eq "0" ]; then
	# Every IP will be displayed horizontically
	sort_them < <(echo "$ethip" "$airip" "$extip"| tr ' ' '\n')
	# Start the Display
	echo "$short"
	echo "$medium"
	echo "$long"
elif [ "$decision" -eq "3" ]; then
   	# Every IP will be displayed vertically
   	sort_them < <(echo "$ethip" "$airip" "$extip"| tr ' ' '\n')
	# Start the Display (The if statements are necessary to keep indention right)
	# If the Addresses are aligned in a different way (eg: longest first), this is not necessary.
   	for (( i=0; i<${#long}; i++ )); do
		echo -ne "${short:$i:1} "
		
		if [ "$i" -ge "${#short}" ]; then
   			echo -ne " "
   		fi 
   		
		echo -ne "${medium:$i:1} "
		
		if [ "$i" -ge "${#medium}" ]; then
   			echo -ne " "
   		fi 
		echo -ne "${long:$i:1} "
		echo -ne "\n"
	done
elif [ "$decision" -eq "2" ]; then
	# 1 IP will be horizontically, 2 vertically
	calculation
#----------------------------------------------------------------------------------------------
# Output the characters above the horizontal line  
	for (( h=0; h<$top; h++ )); do	
		for (( m=0; m<${#horizontal}; m++ )); do
			if [ "$m" -lt "$leftmatch" ]; then
				echo -ne " "
			fi
								
			if [ "$m" -eq "$leftmatch" ]; then	
				if [ "$h" -lt "$leftheight" ]; then						
					echo -ne " "
					leftskip=$[$leftskip+1]
				else
					echo -ne "${leftline:$[$h-$leftskip]:1}"
				fi
			fi
								
			if [ "$m" -gt "$leftmatch" ] && [ "$m" -lt "$rightmatch" ]; then
				echo -ne " "
			fi
							
			if [ "$m" -eq "$rightmatch" ]; then		
				if [ "$h" -lt "$rightheight" ]; then
					echo -ne " "
					rightskip=$[$rightskip+1]
				else
					echo -ne "${rightline:$[$h-$rightskip]:1}"
				fi
			fi
								
			if [ "$m" -gt "$rightmatch" ]; then
				echo -ne " "
			fi		
		done
		echo -ne "\n"
	done

#----------------------------------------------------------------------------------------------
# Output the characters of the horizontal line

	whoisthestring


	for (( m=0; m<${#horizontal}; m++ )); do
		if [ "$m" -lt "$leftmatch" ]; then
			echo -ne "${horizontal:$m:1}"
		fi
								
		if [ "$m" -eq "$leftmatch" ]; then
			if [ "$test3" -eq "2" ] || [ "$test3" -eq "0" ]; then
				# Both are numbers or both are strings
				echo -ne "${horizontal:$m:1}"
			else 
				if [ "$horizontalisnumber" -eq "0" ]; then
					# The horizontal line is the string
					echo -ne "${leftline:$leftmerge:1}"
				else
					# The vertical line is the string
					echo -ne "${horizontal:$m:1}"
				fi
			fi
		fi
								
		if [ "$m" -gt "$leftmatch" ] && [ "$m" -lt "$rightmatch" ]; then
			echo -ne "${horizontal:$m:1}"
		fi
							
		if [ "$m" -eq "$rightmatch" ]; then	
			if [ "$test4" -eq "2" ] || [ "$test4" -eq "0" ]; then
				# Both are numbers or both are strings
				echo -ne "${horizontal:$m:1}"
			else
				if [ "$horizontalisnumber" -eq "0" ]; then
					# The horizontal line is the string
					echo -ne "${rightline:$rightmerge:1}"
				else
					# The vertical line is the string
					echo -ne "${horizontal:$m:1}"
				fi
			fi
		fi
								
		if [ "$m" -gt "$rightmatch" ]; then
			echo -ne "${horizontal:$m:1}"
		fi	
	done
	echo -ne "\n"
	#echo -ne "hello"
#----------------------------------------------------------------------------------------------
# Output the characters below the horizontal line
	leftbottom=$[${#leftline}-$leftmerge]
	rightbottom=$[${#rightline}-$rightmerge]
	
	# How "deep" are we below the horizontal line
	if [ "$leftbottom" -ge "$rightbottom" ]; then
		bottom=$leftbottom
	else
		bottom=$rightbottom
	fi

	# I have no clue why i have to subtract two!!!
	# Maybe because I was working with 2 numbers and both began counting at zero ...
	#echo $leftbottom
	#echo $rightbottom
	leftbottom=$[$leftbottom-2]
	rightbottom=$[$rightbottom-2]
	
	for (( h=0; h<$bottom; h++ )); do
		for (( m=0; m<$[${#horizontal}+1]; m++ )); do
			if [ "$m" -lt "$leftmatch" ]; then
				echo -ne " "
			fi
								
			if [ "$m" -eq "$leftmatch" ]; then
				if [ "$h" -gt "$leftbottom" ]; then							
					echo -ne " "
				else	
					echo -ne "${leftline:$[$h+$leftmerge+1]:1}"			
				fi
			fi
								
			if [ "$m" -gt "$leftmatch" ] && [ "$m" -lt "$rightmatch" ]; then
				echo -ne " "
			fi
								
			if [ "$m" -eq "$rightmatch" ]; then		
				if [ "$h" -gt "$rightbottom" ]; then
					echo -ne " "
				else
					echo -ne "${rightline:$[$h+$rightmerge+1]:1}"
				fi
			fi
								
			if [ "$m" -gt "$rightmatch" ]; then
				echo -ne " "
			fi
			
		done
		echo -ne "\n"
	done
	#echo $leftbottom
	#echo $rightbottom
	#echo $leftmatch
	#echo $rightmatch	
elif [ "$decision" -eq "1" ]; then
	# One line vertically, two horizontically
	calculation
	
#	echo "There are 2 mergers on the horizontal line." 
#	echo $horizontal
#	echo ">>>>>>>>>>>>>>>>>>>>>>>>>"
#	echo "The first one is on the horizontal field:" # Counted from zero!!!
#	echo $leftmatch
#	echo "and is:"
#	echo $leftline
#	echo "at the vertical field:" # Counted from zero!!!
#	echo $leftmerge
#	echo "with the character"
#	echo $leftmatchis
#	echo "---------------------"
#	echo "The second one is on the horizontal field:" # Counted from zero!!!
#	echo $rightmatch
#	echo "and is:"
#	echo $rightline
#	echo "at the vertical field:" # Counted from zero!!!
#	echo $rightmerge
#	echo "with the character"
#	echo $rightmatchis
#	echo "------1-------"
#	echo "$decision"
#	echo $top
	
	
	whoisthestring
	
	for (( h=0; h<${#horizontal}; h++ )); do
		if [ "$h" -lt "$leftmatch" ]; then
			for (( m=0; m<$top; m++ )); do
			echo -ne " "
			done
			echo -ne "${horizontal:$h:1}"
		fi

		if [ "$h" -eq "$leftmatch" ]; then
			for (( m=0; m<$[$top - $leftmerge]; m++ )); do
			echo -ne " "
			done
			echo -ne ${leftline:0:$leftmerge}
			
			if [ "$test3" -eq "2" ] || [ "$test3" -eq "0" ]; then
				# Both are numbers or both are strings
				echo -ne "${horizontal:$h:1}"
			else 
				if [ "$horizontalisnumber" -eq "0" ]; then
					# The horizontal line is the string
					echo -ne "${leftline:$leftmerge:1}"
				else
					# The vertical line is the string
					echo -ne "${horizontal:$h:1}"
				fi
			fi
			echo -ne ${leftline:$[$leftmerge + 1]}	
		fi

		if [ "$h" -gt "$leftmatch" ] && [ "$h" -lt "$rightmatch" ]; then
			for (( m=0; m<$top; m++ )); do
			echo -ne " "
			done
			echo -ne "${horizontal:$h:1}"
		fi

		if [ "$h" -eq "$rightmatch" ]; then
			for (( m=0; m<$[$top - $rightmerge]; m++ )); do
			echo -ne " "
			done
			echo -ne ${rightline:0:$rightmerge} #Characters before
			
			if [ "$test4" -eq "2" ] || [ "$test4" -eq "0" ]; then #Horizontal Character
				# Both are numbers or both are strings
				echo -ne "${horizontal:$h:1}"
			else
				if [ "$horizontalisnumber" -eq "0" ]; then
					# The horizontal line is the string
					echo -ne "${rightline:$rightmerge:1}"
				else
					# The vertical line is the string
					echo -ne "${horizontal:$h:1}"
				fi
			fi
			echo -ne ${rightline:$[$rightmerge + 1]} #Characters after
		fi
		
		if [ "$h" -gt "$rightmatch" ]; then
			for (( m=0; m<$top; m++ )); do
			echo -ne " "
			done
			echo -ne "${horizontal:$h:1}"
		fi
		
		echo -ne "\n"
	done
fi
