#!/usr/bin/env php
<?php

## This Script creates thumbnail images from the folder "source" and puts them in the "thumbnails" folder.
## This "thumbnails" folder has to exist before the script is run!
## The other output is the "img_infos.txt" file. Its contents must be copied to the JS array of "js/sample.js"

$image_source_folder  = '/Users/bernhard/Desktop/source/'; // Slash at the end
$thumbnail_folder     = '/Users/bernhard/Desktop/thumbnails/'; 
$img_info_file        = '/Users/bernhard/Desktop/img_infos.txt';

function get_list_of_image_files() { 
  global $image_source_folder;
  $files = array();
  if ($handle = opendir($image_source_folder)) {
      while (false !== ($file = readdir($handle))) {
          $i = $i + 1;
          if ($file !== "." && $file !== "..") {
            $fullFilePath = $image_source_folder.basename($file);
            array_push($files, $fullFilePath);
          }
      }
      closedir($handle);
  }
  return $files;
}

$files = get_list_of_image_files();

if (file_exists($img_info_file)) {
  return;
}

shell_exec('echo "[" > "'.$img_info_file.'"');

foreach($files as $filename) {
	$thumbnail_file = $thumbnail_folder.strtoupper(basename($filename));
	if (!file_exists($thumbnail_file)) {
		copy($filename,$thumbnail_file);
    shell_exec('sips --resampleHeight 360 "'.$thumbnail_file.'"');
  }
  echo $thumbnail_file;
  $image_info = getimagesize($thumbnail_file);
  if ($image_info[0] == "" || $image_info[1] == "") {
    continue;
  }
  shell_exec('echo "{image:\"'.basename($filename).'\", thumbnail:\"'.basename($thumbnail_file).'\", width:\"'.$image_info[0].'\", height:\"'.$image_info[1].'\"}," >> "'.$img_info_file.'"');
}

shell_exec('echo "{image: \"dummy\", thumbnail: \"dummy\", width: "\0\", height: \"0\" }" >> "'.$img_info_file.'"');
shell_exec('echo "]" >> "'.$img_info_file.'"');
?>