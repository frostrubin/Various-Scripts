#!/usr/bin/env php
<?php
  $sites['Serious Sam 3']['uri'] = 'http://store.steampowered.com/app/41070/';
  $sites['Serious Sam 3']['filename'] = '/tmp/ss3bfe.txt';
  $sites['Serious Sam 3']['compare_start'] = '<div class="game_purchase_action">';
  $sites['Serious Sam 3']['compare_end']   = '<!-- game_area_purchase -->';
  $sites['YT Starcraft 2']['uri'] = 'http://www.youtube.com/show?p=I5hsVByfTVM';
  $sites['YT Starcraft 2']['filename'] = '/tmp/sc2yts1casts.txt';
  $sites['YT Starcraft 2']['compare_start'] = '<div id="show-seasons">';
  $sites['YT Starcraft 2']['compare_end'] = '<!-- end content -->';

  
  foreach ($sites as $name => $site) {
    $uri = download_url($site['uri']);
    $newContent = get_string_between($uri, $site['compare_start'], $site['compare_end']);
    $oldContent = shell_exec('cat '.$site['filename']);
    
    if (strlen($newContent) > 0) {
      $file = fopen ($site['filename'], "w"); 
      fwrite($file, $newContent); 
      fclose ($file);
    }
        
    if ($newContent != $oldContent &&
        strlen($oldContent) > 2       ) {
      shell_exec('growlnotify -s -m "Change on '.$name.'"');
    }
  }
  
  ###   F u n c t i o n s   ###
  function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
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
?>