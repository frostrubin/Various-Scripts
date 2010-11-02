#!/usr/bin/env php
<?php
   $per = shell_exec('pmset -g ps');
   $per = substr($per,strpos($per,'%')-3,3);
   echo $per;
   $watermark = imagecreatefrompng('/Users/bernhard/.NerdTool/surfboard/src1.png');  
   $watermark_width = imagesx($watermark);  
   $watermark_height = imagesy($watermark);  
   #$image = imagecreatetruecolor($watermark_width, $watermark_height);  
   #$image = imagecreatefromjpeg('src2.jpg');
   $image = imagecreatetruecolor($watermark_width, $watermark_height);  
   imagealphablending( $image, false );
   imagesavealpha( $image, true );
   $transparentColor = imagecolorallocatealpha($image, 200, 200, 200, 127);
   imagefill($image, 0, 0, $transparentColor);
   $image = imagecreatefrompng('/Users/bernhard/.NerdTool/surfboard/src2.png');
   imagecopymerge($image, $watermark, 0, 0, 0, 0, 171,279, $per);  
   imagepng($image,'/Users/bernhard/.NerdTool/files/Surfboard.png');  
   imagedestroy($image);  
   imagedestroy($watermark);  
   ?>

