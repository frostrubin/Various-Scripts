#!/usr/bin/env php
<?php

// Edge cases: image? Formatting? https?
$keychain_file = '/Users/username/Library/Keychains/login.keychain'; 
$keychain_dump_file = '/Users/username/Desktop/export/dump.txt';
$known_class_types = array('class: "genp"', 'class: "inet"');

function get_keychain_dump() {
  global $keychain_dump_file;
  $dump = file_get_contents($keychain_dump_file, TRUE);
  return $dump;
}

function get_classes_from_dump($dump) {
	global $keychain_file;
	$separator = 'keychain: "'.$keychain_file.'"';
	$classes = $class_tmp = array();
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $dump) as $line){
	   if ($line == $separator) {
		   	if (!empty($class_tmp)) {
		       array_push($classes, $class_tmp);
		   	}
		    $class_tmp = array();
	   }
	   array_push($class_tmp, $line);
	} 
	array_push($classes, $class_tmp); // To save the very last class
	return $classes;
}

function cleanup_classes($dirty_classes) {
	$clean_classes = array();
	foreach ($dirty_classes as $dirty_class) {
	  $inner_count = 0;
  	$clean_class = array();
  	foreach ($dirty_class as $line) {
	    $ltrim = ltrim($line);
	    // Get Data
	    if ($ltrim === 'data:') {
	      $clean_class['data'] = $dirty_class[$inner_count + 1];
	    }
	    switch (substr($ltrim, 0, 6)) {
        case '"svce"':
          $clean_class['name'] = substr($ltrim, 6);
          break;
        case '"srvr"':
          $clean_class['name'] = substr($ltrim, 6);
          break;
        case '"acct"':
          $clean_class['account'] = substr($ltrim, 6);
          break;
	    }
      
	    $inner_count++;
  	}
	  array_push($clean_classes, $clean_class);	
	}
	return $clean_classes;
}

function extract_data_from_cleaned_classes($cleaned_classes) {
	$extracted_classes = array();
  foreach ($cleaned_classes as $cleaned_class) {
    $extracted_class = array();
    // Extract Name
    $name = null;
  	switch (substr($cleaned_class['name'], 0, 7)) {
      case '<blob>=':
        $name = substr($cleaned_class['name'], 7);
 				switch (substr($name, 0, 1)) {
 					case '"':
 					  // Get Name without the quotes
 					  $name = substr($name, 1, strlen($name) - 2);
 					  break;
 					default:
 					  if (substr($name, 0, 2) == '0x') {
              $name = hex2bin(trim(substr($name, 2)));
						}
 				}
 				break;
    }
    if (!empty($name)) {
			$extracted_class['name'] = $name;
    } else {
    	$extracted_class['name'] = 'Name could not be extracted';
    }

//  Extract Account
    $account = null;
    switch (substr($cleaned_class['account'], 0, 7)) {
      case '<blob>=':
        $account = substr($cleaned_class['account'], 7);
        switch (substr($account, 0, 1)) {
          case '"':
            // Get Account without the quotes
            $account = substr($account, 1, strlen($account) - 2);
            break;
          default:
            if (substr($account, 0, 2) == '0x') {
              $account = hex2bin(trim(substr($account, 2)));
            }
        }
        break;
    }
    if (!empty($account)) {
      $extracted_class['account'] = $account;
    } else {
      $extracted_class['account'] = '';
    }    


    // Extract Data
    $data = null;
    switch (substr($cleaned_class['data'], 0, 1)) {
    	case '"':
    	  // Get data without the quotes
    		$data = substr($cleaned_class['data'], 1, strlen($cleaned_class['data']) - 2);
    	  break;
    	default:
    		if (substr($cleaned_class['data'], 0, 2) == '0x') {
    			$data = hex2bin(trim(substr(strstr($cleaned_class['data'],' ', TRUE), 2)));
    		} 
    }
    if (!empty($data)) {
    	$extracted_class['data'] = $data;
    } else {
    	$extracted_class['data'] = 'Data could not be extracted';
    }

    // Append class
    array_push($extracted_classes, $extracted_class);
  }
  return $extracted_classes;
}

function resolve_note_xml($extracted_classes) {
	$resolved_classes = array();
	foreach ($extracted_classes as $extracted_class) {
		$resolved_class = array();
    if (substr($extracted_class['data'], 0, 5) != '<?xml') {
    	array_push($resolved_classes, $extracted_class);
    	continue;
    }
    $resolved_class['name'] = $extracted_class['name'];

    $xml = simplexml_load_string($extracted_class['data']);
    $note_object = $xml->xpath("/plist/dict/key[.='NOTE']/following-sibling::*[1]")[0];
    $note = (string)$note_object;
    
    // Remove line breaks, translit to ASCII. Then: Check if non-printable characters are found
    if (!ctype_print(iconv("utf-8","ascii//TRANSLIT",preg_replace('/\r|\n/', '', $note)))) {
      $resolved_class['warning'] = 'true'; //something could not be resolved: warn me about it
    } else {
      $resolved_class['warning'] = '';
    }

    //$rtf_object = $xml->xpath("/plist/dict/key[.='RTFD']/following-sibling::*[1]")[0];
    //$rtf = (string)$rtf_object;
    //$resolved_class['rtf'] = $rtf;
    //Todo: handle RTF Input data like images, etc.

    $resolved_class['data'] = $note;
    $resolved_class['account'] = '';
    array_push($resolved_classes, $resolved_class);
	}
	return $resolved_classes;
}


// Get Keychain Dump
$keychain_dump = get_keychain_dump();

// Collect all classes into an array of arrays
$classes = get_classes_from_dump($keychain_dump);

// Get only the data we are interested in
$classes_cleaned = cleanup_classes($classes);
//print_r($classes_cleaned);

// Extract the data
$classes_extracted = extract_data_from_cleaned_classes($classes_cleaned);

// Resolve XML info from secure note items
$classes_resolved = resolve_note_xml($classes_extracted);
foreach ($classes_resolved as $class) {
  if (substr($class['name'], 0, 10) == 'com.apple.') {
    continue;
  }

  $filename = '';
  if ($class['account'] !== '') {
    $filename = $class['name'].'#'.$class['account'];
  } else {
    $filename = $class['name'];
  }

  if (substr($filename,0,4) == 'www.') {
    $filename = substr($filename, 4);
  }

  // remove slash
  $filename = str_replace(' / ', ' ', $filename);
  $filename = str_replace('/', '', $filename);

  $filename = '/Users/username/Desktop/store/'.$filename.'.txt';
  
  $content = $class['data'];
  //$content = 'Super Secret';

  file_put_contents($filename, $content);
  
  if ($class['warning'] != '') {
    echo $class['name'].'#'.$class['account']."\n";
    //echo $class['data']."\n";
  }
}
  
?>