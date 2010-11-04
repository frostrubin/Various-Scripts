#!/bin/bash
# This tilts an image perspectively
convert source.png -matte -virtual-pixel transparent \
-distort Perspective \
'0,0,0,0 0,262,0,262 240,0,240,37 240,262,240,237' target.png