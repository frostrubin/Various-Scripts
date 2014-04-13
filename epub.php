#!/usr/bin/env php
<?php

$option = $argv['1'];

if (($option != 'rename' && $option != 'meta_rename' && $option != 'diff' )  || $option == '') {
  echo 'Valid Options are: rename, meta_rename, diff, copy'."\n"; exit;
}

function slug($input){
  $string = html_entity_decode($input,ENT_COMPAT,"UTF-8");
  setlocale(LC_CTYPE, 'en_US.UTF-8');
  $string = iconv("UTF-8","ASCII//TRANSLIT",$string);
  $string = preg_replace("/[^A-Za-z0-9\s\s+\-]/","",$string);
  $string = str_replace('  ', ' ', $string);
  return $string;
}

function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}


if ($option == 'rename' || $option == 'diff' || $option = 'meta_rename') {
  if ($option == 'rename') {
    $directory  = '/Users/bernhard/Desktop/testtt';
  }
  if ($option == 'diff') {
    $directory  = '/Users/bernhard/Books';
  }
  if ($option == 'meta_rename') {
    $directory = '/Users/bernhard/Desktop/testtt'; //exampleallbooks';
  }
	
  $filelist = '/tmp/epubRenamePlistFileList.txt';
  $plistfile = '/tmp/epubRenamePlist';

  shell_exec('rm -f '.$plistfile.'*');
  shell_exec('rm -f '.$filelist);
  
  $filenames = shell_exec('find '.$directory.' -d 1 -name "*.epub" > '.$filelist);
  $lines = file($filelist);
  
  $i = 0;
  foreach ($lines as $epubfile) {
    $i = $i + 1;
    $guid = trim(guid(), '{}');
    $currentPlist = $plistfile.$guid.'.plist'; // GUID instead of $i because plist are cached which can lead to wrong results
  //  $currentPlist = $plistfile.$i.'.plist';  // since GUID-PLISTs are individual, no caching mixup can occur
    $artistName = ''; 
    $filename   = '';
    $itemName   = '';
    $genre      = '';
    $sortArtist = '';
    $epubfile = str_replace(' ', '\ ', $epubfile);
    $epubfile = str_replace("'", "\'", $epubfile);
    $epubfile = str_replace("(", "\(", $epubfile);
    $epubfile = str_replace(")", "\)", $epubfile);
    $epubfile = str_replace("&", "\&", $epubfile);
    $epubfile = ereg_replace("\n", " ", $epubfile); //remove line breaks
    $epubfile = ereg_replace("\r", " ", $epubfile); //remove line breaks
    $filename = shell_exec('basename '.$epubfile);
    $filename = ereg_replace("\n", "", $filename); //remove line breaks
    $filename = ereg_replace("\r", "", $filename); //remove line breaks


    shell_exec('unzip -p '.$epubfile.' iTunesMetadata.plist > '.$currentPlist);
    //Convert Metadata to XML (some already are xml, some are binary)
    shell_exec('plutil -convert xml1 '.$currentPlist);

    sleep(1); 
    //Get Artist Name
    $artistName = shell_exec('defaults read "'.$currentPlist.'" artistName 2> /dev/null');
    $artistName = str_replace('"','',str_replace("'","",str_replace(";","",$artistName)));
    $artistName = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$artistName.'"\'');
    if ($option != 'meta_rename') {    
      $artistName = trim(slug(ereg_replace("\n", " ", ereg_replace("\r", " ", $artistName))));
    } else {
      $artistName = trim(ereg_replace("\n", " ", ereg_replace("\r", " ", $artistName)));
    }
 
    //Get Item Name
    $itemName = shell_exec('defaults read "'.$currentPlist.'" itemName 2> /dev/null');
    $itemName = str_replace('"','',str_replace("'","",str_replace(";","",$itemName)));
    $itemName = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$itemName.'"\'');
    if ($option != 'meta_rename') {    
      $itemName = trim(slug(ereg_replace("\n", " ", ereg_replace("\r", " ", $itemName))));
    } else {      
      $itemName = trim(ereg_replace("\n", " ", ereg_replace("\r", " ", $itemName)));
    }

    //Get Genre
    if ($option == 'meta_rename') {
      $genre = shell_exec('defaults read "'.$currentPlist.'" genre 2> /dev/null');
      $genre = str_replace('"','',str_replace("'","",str_replace(";","",$genre)));
      $genre = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$genre.'"\'');
      $genre = trim(ereg_replace("\n", " ", ereg_replace("\r", " ", $genre)));

      $sortArtist = shell_exec('defaults read "'.$currentPlist.'" sort-artist 2> /dev/null');
      $sortArtist = str_replace('"','',str_replace("'","",str_replace(";","",$sortArtist)));
      $sortArtist = shell_exec('perl -e \'binmode STDOUT => ":utf8"; print "'.$sortArtist.'"\'');
      $sortArtist = trim(ereg_replace("\n", " ", ereg_replace("\r", " ", $sortArtist)));
    }

    $resultname = $artistName.' - '.$itemName.'.epub';

    if ($option == 'rename') {
      if ($artistName == '' || $itemName == '') { continue; }
      echo $epubfile."\n";
      echo $i.'. '.$artistName.' - '.$itemName."\n";
      shell_exec('mv -n '.$epubfile.' "'.$directory.'/'.$resultname.'"');
    } 
    if ($option == 'diff') {
      if ($filename != $resultname) {
        if($artistName != '' && $itemName != '') {		    
	  echo $i.' '.$filename."\n";
	  echo $i.' '.$resultname."\n";
	}
      }
    }
    if ($option == 'meta_rename') {
      if ($artistName == '' || $itemName == '' || $genre == '' || $sortArtist == '' || $epubfile == '') {
        echo 'iTunes Fehler bei '.$filename."\n";
      } else {
        echo $epubfile."\n";
        echo $i.'. '.$artistName.' - '.$itemName."\n";
        $result = shell_exec('/Users/bernhard/Applications/Calibre.app/Contents/MacOS/ebook-meta '.$epubfile.' -a "'.$artistName.'" --author-sort="'.$sortArtist.'" -t "'.$itemName.'" -c "" --tags="'.$genre.'" -r "" -p "" -d "" -l "" --isbn "" -s "" --category "" 2>&1 >/dev/null' ); //STDOUT to null, STDERR to STDOUT
        if ($result != '') {
          echo $result."\n".'Dieser ePub Fehler ist aufgetreten bei:'."\n".$epubfile."\n";
        }
      }  
    }
  }
  shell_exec('rm -f '.$plistfile.'*'); 
}
?>
