#!/bin/bash
#Ersetzt jedes } durch ein }+newline, damit css schön formatiert ist.
cat touch_screen.css | sed "s/}/}\\`echo -e '\r'`/g" > 2.css