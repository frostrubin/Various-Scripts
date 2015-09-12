#!/usr/bin/env php
<?php

$book_folder = '/Users/bernhard/Folder/'; // Slash at the end
$book_bucket = 'bookbucketname';
$book_bucket_prefix = 'folder/';

function get_list_of_book_files() { // Get list of book files from the library folder
  global $book_folder;
  $files = array();
  if ($handle = opendir($book_folder)) {
      while (false !== ($file = readdir($handle))) {
          $i = $i + 1;
          if ($file != "." && $file != "..") {
          	$fullFilePath=$book_folder.$file;
            array_push($files, $fullFilePath);
          }
      }
      closedir($handle);
  }
  return $files;
}

function get_list_of_book_files_with_md5() { // Get list of book files and their md5 hashes
  $files_with_md5 = array();
  $files = get_list_of_book_files();
  foreach($files as $filename) {
    $md5 = trim(shell_exec('md5 -q "'.$filename.'"'));
    $files_with_md5[basename($filename)] = $md5;
  }
  return $files_with_md5;
}

function get_list_of_book_files_with_md5_cloud() { // Get list of book files and their md5 hashes
  global $book_bucket;
  global $book_bucket_prefix;
  echo 'Getting list of book files with md5 ...'."\n";
  $files = array();
  $cmd = 'aws s3api list-objects --bucket '.$book_bucket.' --prefix "'.$book_bucket_prefix.'"';
  $cmd = $cmd.' --query \'Contents[].{Key: Key, MD5: ETag}\'';
  $json = shell_exec($cmd);
  $json_array = json_decode($json, TRUE);
  foreach($json_array as $info_array) {
    $md5 = str_replace('"','', $info_array['MD5']);
    $files[basename($info_array['Key'])] = $md5;
  }
  return $files;
}

$local_md5_array = get_list_of_book_files_with_md5();
$cloud_md5_array = get_list_of_book_files_with_md5_cloud();

foreach ($local_md5_array as $filename => $md5) {
  if (!array_key_exists($filename, $cloud_md5_array)) {
    echo 'Existiert in der Cloud nicht: '.$filename."\n";
  }
  if ($cloud_md5_array[$filename] != $md5) {
  	echo 'MD5 Diff: '.$filename.'Local: '.$md5.'  Cloud: '.$cloud_md5_array[$filename]."\n";
  }
}

?>
