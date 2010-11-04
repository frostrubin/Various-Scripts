#!/bin/bash
# Convert pdf to png
# Every page as single picture
convert -density 200 'input.pdf' output.png
# Specific page extract
convert -density 200 'input.pdf[4]' output.png