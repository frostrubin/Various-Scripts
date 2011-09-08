#!/usr/bin/env php
<?php
  $lines = file("./urls.txt");
  $artworkfolder = "./artwork/";
  
  require_once('arraytoxml.php'); 
  
  function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
  }
  
  function str_replace_once($needle, $replace, $haystack) { 
    // Looks for the first occurence of $needle in $haystack 
    // and replaces it with $replace. 
    $pos = strpos($haystack, $needle); 
    if ($pos === false) { 
      // Nothing found 
      return $haystack; 
    } 
    return substr_replace($haystack, $replace, $pos, strlen($needle)); 
  }
  
  function download_url($url) {
    $url = str_replace(' ','%20',$url);
    try {
      $error = 'Data could not be retreived';
      $string = @file_get_contents($url);
      return $string;
      
    }
    catch (Exception $e)
    {
    echo 'An Error occured: ',  $e->getMessage(), "\n";
    } 
  }
  
  $appArray = array();
  $counter = 0;
  
  foreach ($lines as &$value) {
    $counter = $counter + 1;
    $url = trim($value);
    $site = download_url($url);
    $site = ereg_replace("\n", " ", $site); //remove line breaks
    $site = ereg_replace("\r", " ", $site); //remove line breaks
    
    $name = get_string_between($site, '<h1>', '</h1>');
    $price = get_string_between($site, '<div class="price">','</div>');
    $imagediv = get_string_between($site, '<div id="left-stack">', '</div>');
    $imagelink = get_string_between($imagediv, 'src="', '"');
    
    ####$image = data_uri($imagelink, $mime);
    $image = download_url($imagelink);
    $localartwork = $artworkfolder.$counter.".jpeg";
    if (empty($name) == false) {
    $file = fopen ($localartwork, "w"); 
    fwrite($file, $image); 
    fclose ($file);
    
    
    //echo $url."\n".$price."\n".$image."\n".$name."\n"; 
    
    $appArray['applist']['app id="'.$name.'"']['name'] = $name;
    $appArray['applist']['app id="'.$name.'"']['url'] = $url;
    $appArray['applist']['app id="'.$name.'"']['image'] = $imagelink;
    $appArray['applist']['app id="'.$name.'"']['price'] = ' ';
    $appArray['applist']['app id="'.$name.'"']['rating'] = ' ';
    $appArray['applist']['app id="'.$name.'"']['boughton'] = ' ';
    $appArray['applist']['app id="'.$name.'"']['inapppurchases'] = ' ';
    $appArray['applist']['app id="'.$name.'"']['nolongeravailable'] = ' ';
    $appArray['applist']['app id="'.$name.'"']['localartwork'] = $localartwork;
    }
  }

  //print_r($appArray);
  
  $xml = new xml(); 
  $xml->setArray($appArray);
  $xmlOutput = $xml->outputXML('return');
  echo $xmlOutput;a
  
  
  
  
  
  
  


?>