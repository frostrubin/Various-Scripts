#!/usr/bin/env php
<?php

$directory = './Excluded Mobile Applications/';
$artwork = 'Artwork.png';
$plistfile = 'plist.plist';
$website = '';
$mime= 'image/png';
$outfile="Apps.html";
$spalten=3; //Immer eins weniger, als man haben will


  function getDirectoryList ($directory) 
  {
    // create an array to hold directory list
    $results = array();
    // create a handler for the directory
    $handler = opendir($directory);
    // open directory and walk through the filenames
    while ($file = readdir($handler)) {
      // if file isn't this directory or its parent, add it to the results
      if ($file != "." && $file != ".." && end(explode(".", $file)) == 'ipa') {
        $results[] = $file;
      }
    }
    // tidy up: close the handler
    closedir($handler);
    // done!
    return $results;
  }

  function data_uri($file, $mime) 
  {  
    $contents = file_get_contents($file);
    $base64   = base64_encode($contents); 
    return ('data:' . $mime . ';base64,' . $base64);
  }

  function get_string_between($string, $start, $end){
   $string = " ".$string;
   $ini = strpos($string,$start);
   if ($ini == 0) return "";
   $ini += strlen($start);
   $len = strpos($string,$end,$ini) - $ini;
   return substr($string,$ini,$len);
  }

  function get_plist_string($plist, $key){
    $key = get_string_between($plist, $key, '</string');
    $key = str_replace('</key>','',$key);
    $key = str_replace('<string>','',$key);
    $key = trim($key);
    return $key;
  }

  function get_plist_date($plist, $key){
    $key = get_string_between($plist, $key, '</date');
    $key = str_replace('</key>','',$key);
    $key = str_replace('<date>','',$key);
    $key = trim($key);
    return $key;
  }



$filenames = getDirectoryList($directory);

#print_r($filenames);


$website = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	   "http://www.w3.org/TR/html4/strict.dtd"> 
<html> 
<head> 
<title>iTunes Apps</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script> 

<style type="text/css">
* {
margin: 0px;
padding: 0px;
}

body {
background-color: #F9F9F9;
font: 1em helvetica,sans-serif;
}

.header {
background-color: #626D7D;
background-image: -webkit-gradient(
    linear,
    left bottom,
    left top,
    color-stop(0.13, rgb(123,133,150)),
    color-stop(0.95, rgb(51,54,58))
);
background-image: -moz-linear-gradient(
    center bottom,
    rgb(123,133,150) 13%,
    rgb(51,54,58) 95%
);

color: white; /*#F6F6F6;*/
height:3em;
width: 100%;
text-align: center;
}

.header h2 {
padding-top: 0.4em;
}


th, td {
  padding: 3px !important;
}

/* Sortable tables */
table.sortable thead {
    background-color:#DFDFDF !important;
    color:#292929;
    font-weight: bold;
    cursor: default;
}

/* App Cover */
.cover {
width: 64px;
border-radius: 10px;
}
</style>

</head>
<body>
<div class="header"><h2>'.count($filenames).' Apps</h2></div>
<center>
<table class="sortable" border="0">
  <thead>
    <tr>
      <th>Cover</th>
      <th>Name</th>
      <th>Author</th>
      <th>Purchase Date</th>
      <th>Price</th>
    </tr>
  </thead>
  <tbody>';
    $directory = str_replace(' ', '\ ', $directory);
foreach ($filenames as &$filename) {
    $filename = str_replace(' ', '\ ', $filename);
    $filename = str_replace("'", "\'", $filename);
   # echo $filename."\n";
    //Extract Artwork
    shell_exec('unzip -pc '.$directory.$filename.' iTunesArtwork > '.$artwork);
    //Extract Metadata
    shell_exec('unzip -pc '.$directory.$filename.' iTunesMetadata.plist > '.$plistfile);
    //Convert Metadata to XML (some already are xml, some are binary)
    shell_exec('plutil -convert xml1 '.$plistfile);
    //Read Metadata
    $plist = shell_exec('cat '.$plistfile);
    //Get Application Name
    $appName = get_plist_string($plist, 'itemName');
    //Get Application Author
    $appAuthor = get_plist_string($plist, 'artistName');
    //Get Application Price
    $appPrice = get_plist_string($plist, 'priceDisplay');
    if ($appPrice == 'Kostenlos' || $appPrice == 'KOSTENLOS') {
      $appPrice = 'Free';
    }
    //Get Application Purchase Date
    $appPurchaseDate = get_plist_date($plist, 'purchaseDate');
      $lines = explode("\n", $appPurchaseDate);
      if (count($lines) > 1) {
        //Instead of <date>, <string> was used
        $appPurchaseDate = get_plist_string($plist, 'purchaseDate');
      }
      //Only use date, remove time
      $appPurchaseDate = substr($appPurchaseDate,0,10);
    echo $appAuthor;
    echo $appPrice;
    echo $appPurchaseDate;
    echo $appName."\n";

       
    $website = $website.'
  <tr>
    <td><img class="cover" src="'.data_uri($artwork, $mime).'" /></td>
    <td>'.$appName.'</td>
    <td>'.$appAuthor.'</td>
    <td align="center">'.$appPurchaseDate.'</td>
    <td align="right">'.$appPrice.'</td>
  </tr>';



}
$website = $website.'
  </tbody>
</table>
</center>
</body>
</html>';

$file = fopen ($outfile, "w"); 
fwrite($file, $website); 
fclose ($file);

shell_exec('rm '.$plistfile.' '.$artwork);
?>

