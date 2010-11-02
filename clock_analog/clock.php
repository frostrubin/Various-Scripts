#!/usr/bin/env php
<?php
/******************************************************************************\
* Analogic clock                               Version 1.0                     *
* Copyright 2000 Frederic TYNDIUK (FTLS)       All Rights Reserved.            *
* E-Mail: tyndiuk@ftls.org                     Script License: GPL             *
* Created  02/28/2000                          Last Modified 02/28/2000        *
* Scripts Archive at:                          http://www.ftls.org/php/        *
*******************************************************************************/

if (! $size > 0) {
	$size = 300;
}
$radius = floor($size / 2);

header("content-Type: image/gif");

$img = ImageCreate($size, $size);
$color_alpha = ImageColorAllocate($img, 254, 254, 254);
$color_white = ImageColorAllocate($img, 255, 255, 255);
$color_black = ImageColorAllocate($img, 0, 0, 0);
$color_gray  = ImageColorAllocate($img, 192, 192, 192);
$color_red   = ImageColorAllocate($img, 255, 0, 0);
$color_blue  = ImageColorAllocate($img, 0, 0, 255);
ImageColorTransparent($img, $color_alpha);

ImageArc($img,$radius, $radius, $size, $size, 0, 360, $color_black);
ImageFill($img, $radius, $radius, $color_white);

$min = 0;
while($min++ < 60) {
	if ($min % 15 == 0)
		$len = $radius / 5;
	elseif ($min % 5 == 0)
		$len = $radius / 10;
	else
		$len = $radius / 25;

	$ang = (2 * M_PI * $min) / 60;
	$x1 = sin($ang) * ($radius - $len) + $radius;
	$y1 = cos($ang) * ($radius - $len) + $radius;
	$x2 = (1 + sin($ang)) * $radius;
	$y2 = (1 + cos($ang)) * $radius;

	ImageLine($img, $x1, $y1, $x2, $y2, $color_black);
}

list($hour, $min, $sec) = preg_split ("/-/", Date("h-i-s", Time()));
$hour = $hour % 12;

$xs = intval(cos($sec * M_PI/30 - M_PI/2) * 0.75 * $radius + $radius);
$ys = intval(sin($sec * M_PI/30 - M_PI/2) * 0.75 * $radius + $radius);
$xm = intval(cos($min * M_PI/30 - M_PI/2) * 0.65 * $radius + $radius);
$ym = intval(sin($min * M_PI/30 - M_PI/2) * 0.65 * $radius + $radius);
$xh = intval(cos($hour*5 * M_PI/30 - M_PI/2) * 0.5 * $radius + $radius);
$yh = intval(sin($hour*5 * M_PI/30 - M_PI/2) * 0.5 * $radius + $radius);

ImageLine($img, $radius, $radius,   $xs, $ys, $color_gray);
ImageLine($img, $radius, $radius-1, $xm, $ym, $color_blue);
ImageLine($img, $radius-1, $radius, $xm, $ym, $color_blue);
ImageLine($img, $radius, $radius-1, $xh, $yh, $color_blue);
ImageLine($img, $radius-1, $radius, $xh, $yh, $color_blue);

ImageArc($img, $radius, $radius, $radius / 8, $radius / 8, 0, 360, $color_red);
ImageFillToBorder($img, $radius, $radius, $color_red, $color_red);

Imagegif($img, './clock.gif');
ImageDestroy($img);

?>