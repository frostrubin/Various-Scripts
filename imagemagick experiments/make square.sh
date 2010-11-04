#!/bin/bash
# This creates a 512x512 image. Ideal for Icon creation
size=512
convert \
  <(convert -resize ${size}x${size} xc:transparent png:-) \
  <(convert -resize ${size}x${size} source.jpg png:-) \
  -gravity Center -composite target.png
