#!/usr/bin/env php 
<?php
  
  include('arraytoxml.php');
  
  //First Geeklet
  $geeklets['weatherget']['command'] = '/Users/bernhard/.NerdTool/weather.php getttt';
  $geeklets['weatherget']['repeat'] = 400;
  $geeklets['weatherget']['isslow'] = 'X';
  $geeklets['weatherget']['xpos'] = '20px';
  $geeklets['weatherget']['ypos'] = '20px';
  $geeklets['weatherget']['width'] = '10px';
  $geeklets['weatherget']['height'] = '10px';
  $geeklets['weatherget']['txtcolor'] = 'rgba(0,255,255,0.0)';
  $geeklets['weatherget']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['weatherget']['freecss'] = '';
  
  $geeklets['forecast0']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast0';
  $geeklets['forecast0']['repeat'] = 3;
  $geeklets['forecast0']['isslow'] = '';
  $geeklets['forecast0']['xpos'] = '250px';
  $geeklets['forecast0']['ypos'] = '120px';
  $geeklets['forecast0']['width'] = '500px';
  $geeklets['forecast0']['height'] = '500px';
  $geeklets['forecast0']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast0']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast0']['freecss'] = 'font-family: \'Lucida Grande\';font-size: 0.6em;';
  

  $geeklets['forecast0image']['command'] = 'echo ';
  $geeklets['forecast0image']['repeat'] = 3;
  $geeklets['forecast0image']['isslow'] = '';
  $geeklets['forecast0image']['isimage'] = 'X';
  $geeklets['forecast0image']['xpos'] = '150px';
  $geeklets['forecast0image']['ypos'] = '80px';
  $geeklets['forecast0image']['width'] = '250px';
  $geeklets['forecast0image']['height'] = '180px';
  $geeklets['forecast0image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  $geeklets['forecast0image']['background'] = 'url(./files/forecast0.png)';
  $geeklets['forecast0image']['bgtrans'] = '0.5';
  $geeklets['forecast0image']['freecss'] = '';
  
  
  // The very first run of every command is set to
  // a week ago. So at startup, every command is run
  foreach ($geeklets as $id => $array ) {
    $geeklets[$id]['lastcall'] = time() - (7 * 24 * 60 * 60);
    if ($geeklets[$id]['isimage'] == 'X') {
      $geeklets[$id]['background'] = $geeklets[$id]['background'].';background-repeat: no-repeat; background-size: 100% 100%;'.'opacity: '.$geeklets[$id]['bgtrans'].';';
    }
  }
  

  foreach ($geeklets as $id => $array ) {
    // Run the Scripts
    if (time() - $geeklets[$id]['lastcall'] >= $geeklets[$id]['repeat']) {
      //Time to run the script again
      $runinbackground = '';
      if ($geeklets[$id]['isslow'] == 'X') {
        //This command will run pretty slow
        //So we pipe its ouput to /dev/null so that php can go on without waiting
        $runinbackground = ' &'; 
      }
      $geeklets[$id]['output'] = shell_exec($geeklets[$id]['command'].$runinbackground);
      $geeklets[$id]['lastcall'] = time();
    }
  }
  
  foreach ($geeklets as $id => $array ) {
    // Fill the xml data
    foreach ($array as $entity => $value ) {
      $xmlData['geeklets']['geeklet id="'.$id.'"'][$entity] = $value;
    }
  }

    
  
  
  ////exit;
  //while (true) {
  //  echo hallo;
  //}

// Initiate the class 
$xml = new xml(); 
 
// Set the array so the class knows what to create the XML from 
$xml->setArray($xmlData); 
 
// Print the XML to screen 
$fileContent = $xml->outputXML('return'); 
  
  
  $myFile = "geeklets.xml";
  $fh = fopen($myFile, 'w') or die("can't open file");
  $stringData = $fileContent;
  fwrite($fh, $stringData);
  fclose($fh);
?>