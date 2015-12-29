#!/usr/bin/env php
<?php

$book_bucket         = 'bucketname';
$book_bucket_prefix  = 'books/'; // Slash at the end
$book_types          = array( 'epub' , 'pdf', 'another_file_ending' );
$library_xml_name    = '1_Library.xml';
$library_json_name   = '1_Library.json';
$new_book_folder     = '/Users/username/Desktop/import/'; // Slash at the end
$library_xml_export  = '/Users/username/Desktop/export.xml';
$library_xml_import  = '/Users/username/Desktop/Books.xml';
$book_db             = '/Users/username/Desktop/Books.db';
$ebook_meta_lambda   = 'EbookMetaLambdaFunction';
$ebook_meta_binary   = '/Users/username/Applications/calibre.app/Contents/MacOS/ebook-meta';
$ebook_meta_json_in  = '/tmp/ebook_meta_input.json';
$ebook_meta_json_out = '/tmp/ebook_meta_output.json';

function create_book_db() { // The Book DB is not part of the core functionality of this tool
  global $book_db;
  $STRUCTURE='CREATE TABLE Books (md5 text(32) PRIMARY KEY NOT NULL, title text(300),
  author text(300), author_sort text(300), genre char(128), filename text(300),
  date_added text(10), time_added text(5) );';
  touch($book_db);
  shell_exec('echo "'.$STRUCTURE.'" > /tmp/bookdbstructure.txt');
  shell_exec('sqlite3 '.$book_db.'  < /tmp/bookdbstructure.txt');
}

function open_book_db() { 
  global $book_db;
  create_book_db();
  shell_exec('open -a "SQLPro for SQLite" '.$book_db);
}

function present_basic_options() { // Main selection dialog for the user to choose an option
  echo 'Hello, you have 5 options. Please choose one.'."\n".'  1) Export XML'."\n".'  2) Import XML'."\n"; 
  echo '  3) Import new files'."\n".'  4) Update filenames'."\n".'  5) Open book database'."\n"; 
  echo 'The normal flow is: 3 -> 1 -> 2 -> 1 -> 4 -> 1'."\n";

  $i = readline( );
  switch ($i):
    case 1:  return $i; 
    case 2:  return $i; 
    case 3:  return $i;
    case 4:  return $i;
    case 5:  return $i;
    default: return present_basic_options(); 
  endswitch;
}

function ends_with($haystack, $needle) {
  return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function book_type_relevant($filename) { // Check if a filename ends with an "allowed" book type
  global $book_types;
  foreach ($book_types as $book_type) {
    if (ends_with($filename, $book_type)) {
      return true;
    }
  }
}

function get_list_of_book_files() { // Get list of book files from the library folder
  global $book_bucket;
  global $book_bucket_prefix;
  echo 'Getting list of book files ...'."\n";
  $files = array();
  $json = shell_exec('aws s3api list-objects --bucket '.$book_bucket.' --prefix "'.$book_bucket_prefix.'" --query \'Contents[].{Key: Key}\'');
  $json_array = json_decode($json, TRUE);
  foreach($json_array as $info_array) {
    if(book_type_relevant($info_array['Key'])) {
      array_push($files, basename($info_array['Key']));
    }
  }
  return $files;
}

function get_list_of_new_book_files() { // Get list of book files from the library folder
  global $new_book_folder;
  $files = array();
  if ($handle = opendir($new_book_folder)) {
      while (false !== ($file = readdir($handle))) {
          $i = $i + 1;
          if ($file != "." && $file != ".." && book_type_relevant($file)) {
            $fullFilePath=$new_book_folder.$file;
            array_push($files, $fullFilePath);
          }
      }
      closedir($handle);
  }
  return $files;
}

function get_list_of_book_files_with_md5() { // Get list of book files and their md5 hashes
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
    if (array_key_exists($md5, $files)) {
      echo "\n".'Warning! Duplicate md5 found:'."\n";
      echo '  '.$info_array['Key']."\n".'  '.$files[$md5]."\n";
    }
    if(book_type_relevant($info_array['Key'])) {
      $files[$md5] = basename($info_array['Key']);
    }
  }
  return $files;
}

function create_library_xml() {
  return new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><library></library>');
}

function create_library_xml_file() {
  global $library_xml_name;
  $xml = create_library_xml();
  $xml->asXml('/tmp/'.$library_xml_name);
  return simplexml_load_file('/tmp/'.$library_xml_name);
}

function get_library_xml() {
  global $library_xml_name;
  global $book_bucket;
  global $book_bucket_prefix;
  $output = array();
  $retcode = 0;
  shell_exec('rm -f "/tmp/'.$library_xml_name.'"');
  exec('aws s3 cp "s3://'.$book_bucket.'/'.$book_bucket_prefix.$library_xml_name.'" "/tmp/'.$library_xml_name.'" 2>&1', $output, $retcode);
  if ($rectode != 0 ){
    print_r($output);
    return;
  }

  return simplexml_load_file('/tmp/'.$library_xml_name);
}

function str_last_replace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);
  if($pos !== false) {
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  }
  return $subject;
}

function append_simplexml(&$simplexml_to, &$simplexml_from) {
  foreach ($simplexml_from->children() as $simplexml_child) {
    $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
    foreach ($simplexml_child->attributes() as $attr_key => $attr_value) {
      $simplexml_temp->addAttribute($attr_key, $attr_value);
    }
    append_simplexml($simplexml_temp, $simplexml_child);
  }
} 

function get_ebook_meta_output() {
  global $ebook_meta_json_out;
  $json_array = json_decode(file_get_contents($ebook_meta_json_out), TRUE);
  foreach($json_array['data'] as $key => $value) {
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $value) as $line){
      //Put each line into an array variable
      $parts = explode(":", $line, 2);
      $info[trim($parts[0])] = trim($parts[1]);
      if (trim($parts[0]) == 'Author(s)' || trim($parts[0]) == 'Title') {
        return $value;
      }
    } 
  }
}

function get_ebook_meta_error() {
  global $ebook_meta_json_out;
  $json_array = json_decode(file_get_contents($ebook_meta_json_out), TRUE);
  return $json_array['errorMessage'];
}

function execute_ebook_meta_lambda() {
  global $ebook_meta_lambda;
  global $ebook_meta_json_in;
  global $ebook_meta_json_out;
  $cmd = 'aws lambda invoke --function-name '.$ebook_meta_lambda.' --invocation-type RequestResponse';
  $cmd = $cmd.' --payload file://'.$ebook_meta_json_in.' '.$ebook_meta_json_out;
  return shell_exec($cmd);
}

function get_book_info_from_file_cloud($filename_in) {
  global $ebook_meta_json_in;
  global $book_bucket;
  global $book_bucket_prefix;
  echo 'Getting book info for '.$filename_in."\n";
  $filename = basename($filename_in);
  $info = array();
  if (ends_with($filename, 'pdf')) {
    $parts = explode(' - ', $filename);
    $author = trim($parts[0]);
    $info['Author(s)'] = $author;
    array_shift($parts);
    $info['Title'] = implode(' - ',$parts);
    $info['Title'] = str_replace('.pdf','',$info['Title']);
    $info['Tags'] = 'PDF File'; // Genre
  } else {
    shell_exec('echo "{" > "'.$ebook_meta_json_in.'"; echo \"file_in\": \"'.$filename.'\", >> "'.$ebook_meta_json_in.'"');
    shell_exec('echo \"file_out\": \"'.$filename.'\" >> "'.$ebook_meta_json_in.'"; echo "}" >> "'.$ebook_meta_json_in.'"');
    $json = execute_ebook_meta_lambda();
    $json_array = json_decode($json, TRUE);
    if ($json_array['StatusCode'] == 200) {
      $data = get_ebook_meta_output();
    } else {
      echo 'StatusCode: '.$json_array['StatusCode']."\n";
      $error = get_ebook_meta_error($json);
      echo $error;
    }

    foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line){
      //Put each line into an array variable
      $parts = explode(":", $line, 2);
      $info[trim($parts[0])] = trim($parts[1]);
      if (trim($parts[0]) == 'Author(s)') {
        $author_sort = array();
        preg_match("/\[(.*)\]/", trim($parts[1]), $author_sort);
        $info['SortAuthor'] = $author_sort[1];
        $info[trim($parts[0])] = trim(str_last_replace( $author_sort[0] , '', trim($parts[1])));
      }
    } 
  }  
  return $info;
}

function get_book_info_from_file($filename_in) {
  global $ebook_meta_binary;
  global $book_folder;
  global $book_bucket;
  global $book_bucket_prefix;
  $filename = basename($filename_in);
  echo 'Getting book info for '.$filename."\n";
  $info = array();
  if (ends_with($filename, 'pdf')) {
    $parts = explode(' - ', $filename);
    $author = trim($parts[0]);
    $info['Author(s)'] = $author;
    array_shift($parts);
    $info['Title'] = implode(' - ',$parts);
    $info['Title'] = str_replace('.pdf','',$info['Title']);
    $info['Tags'] = 'PDF File'; // Genre
  } else {
    shell_exec('aws s3 cp "s3://'.$book_bucket.'/'.$book_bucket_prefix.$filename.'" "/tmp/'.$filename.'"');
    $data = shell_exec($ebook_meta_binary.' "/tmp/'.$filename.'"');
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line){
      //Put each line into an array variable
      $parts = explode(":", $line, 2);
      $info[trim($parts[0])] = trim($parts[1]);
      if (trim($parts[0]) == 'Author(s)') {
        $author_sort = array();
        preg_match("/\[(.*)\]/", trim($parts[1]), $author_sort);
        $info['SortAuthor'] = $author_sort[1];
        $info[trim($parts[0])] = trim(str_last_replace( $author_sort[0] , '', trim($parts[1])));
      }
    } 
  }
  return $info;
}

function update_file_with_info($filename_in, $book) {
  global $ebook_meta_binary;
  global $book_bucket;
  global $book_bucket_prefix;
  $filename = basename($filename_in);
  echo 'Setting book info for '.$filename."\n";
  shell_exec('aws s3 cp "s3://'.$book_bucket.'/'.$book_bucket_prefix.$filename.'" "/tmp/'.$filename.'"');
  $cmd = $ebook_meta_binary.' "/tmp/'.$filename.'" -a "'.$book['author'].'"';
  $cmd = $cmd.' --author-sort="'.$book['author_sort'].'" -t "'.$book['title'].'"';
  $cmd = $cmd.' --tags="'.$book['genre'].'" -c "" -r "" -p "" -d "" -l "" --isbn "" -s "" --category ""';
  $cmd = $cmd.' 2>&1 >/dev/null'; //STDOUT to null, STDERR to STDOUT
  $err = shell_exec($cmd);
  return $err;
}

function update_file_with_info_cloud($filename_in, $book) {
  global $ebook_meta_json_in;
  global $ebook_meta_json_out;
  echo 'Setting book info for '.$filename_in."\n";
  $filename = basename($filename_in);
  shell_exec('echo "{" > "'.$ebook_meta_json_in.'"; echo \"file_in\": \"'.$filename.'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"file_out\": \"'.$filename.'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"title\": \"'.str_replace("'","\'", $book['title']).'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"author\": \"'.str_replace("'","\'", $book['author']).'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"author_sort\": \"'.str_replace("'","\'", $book['author_sort']).'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"genre\": \"'.$book['genre'].'\" >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo "}" >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo "" > "'.$ebook_meta_json_out.'"');
  $json = execute_ebook_meta_lambda();
  $json_array = json_decode($json, TRUE);
  $error = '';
  if ($json_array['StatusCode'] == 200) {
    $json_array = json_decode(file_get_contents($ebook_meta_json_out), TRUE);
    if ($json_array['status'] != 0) {
      $error = $json_array['msg'];
    }
  } else {
    echo 'StatusCode: '.$json_array['StatusCode']."\n";
    $error = get_ebook_meta_error($json);
    echo $error;
  }
  return $error;
}

function slug($input) {
  $string = $input;
  $string = ereg_replace("\n", '', $string);
  $string = ereg_replace("\r", '', $string);
  $string = str_replace("'", '', $string);
  $string = str_replace('"', '', $string);
  $string = str_replace(';', '', $string);
  $string = html_entity_decode($input,ENT_COMPAT,"UTF-8");
  setlocale(LC_CTYPE, 'en_US.UTF-8');
  $string = iconv("UTF-8","ASCII//TRANSLIT",$string);
  $string = preg_replace("/[^A-Za-z0-9\s\s+\-]/","",$string);
  $string = str_replace('  ', ' ', $string);
  $string = trim($string);
  return $string;
}

function save_library_xml_to_xml($library_xml) {
  global $library_xml_name;
  global $book_bucket;
  global $book_bucket_prefix;
  $library_xml->asXml('/tmp/'.$library_xml_name);
  shell_exec('aws s3 cp --sse AES256 "/tmp/'.$library_xml_name.'" "s3://'.$book_bucket.'/'.$book_bucket_prefix.$library_xml_name.'"');
}

function save_library_xml_to_json($library_xml) {
  global $library_json_name;
  global $book_bucket;
  global $book_bucket_prefix;
  $string = '['."\n";
  foreach ($library_xml->book as $book) {
    $string = $string.'  {'."\n";
    foreach ($book->attributes() as $a => $b) {
      switch ($a) {
          case 'title':
              $title = $b;       break;
          case 'author':
              $author = $b;      break;
          case 'genre':
              $genre = $b;       break;
          case 'filename':
              $filename = $b;    break;
      }
    }
    $string = $string.'    "author" : "'.$author.'",'."\n";
    $string = $string.'    "title" : "'.$title.'",'."\n";
    $string = $string.'    "genre" : "'.$genre.'",'."\n";
    $string = $string.'    "filename" : "'.$filename.'"'."\n";
    $string = $string.'  },'."\n";  
  }
  $string = $string.'  {'."\n".'  }'."\n".']';
  file_put_contents('/tmp/'.$library_json_name, $string);
  $output = array();
  $retcode = 0;
  $cmd = 'aws s3 cp --sse AES256 "/tmp/'.$library_json_name.'" "s3://'.$book_bucket.'/'.$book_bucket_prefix.$library_json_name.'" 2>&1';
  exec($cmd, $output, $retcode);
  if ($rectode == 0 ){
    unset($output);
  }
  return $output;
}

///////// Begin of Program Logic /////////
// Let the user choose what to do
$option = present_basic_options();

if ($option == 4) { // Update Filenames
  // Get List of file names
  $files = get_list_of_book_files();
  // Remove PDF files
  foreach ($files as $filename) {
    if (ends_with($filename, 'pdf')) {
      $index = array_search($filename, $files);
      if ($index !== false) {
        unset($files[$index]);
      }
    } 
  }

  if (empty($files)) {
    echo 'No books were found in '.$book_bucket.'/'.$book_bucket_prefix.'  -  Renaming canceled.'."\n";
    exit; 
  }

  // Load Library.xml file
  $library = get_library_xml();

  // Loop over filenames, compare metadata
  $unknown = $to_rename = array();
  foreach ($files as $filename) {
    $filename_base = basename($filename);
    $book = array();
    $book = $library->xpath('book[@filename="'.$filename_base.'"]');
    if (empty($book)) {
      array_push($unknown, $filename_base); 
      continue;
    }

    // Begin creating a filename from the metadata
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $new_name = $book[0]['author'].' - '.$book[0]['title'];
    $new_name = slug($new_name);
    $new_name = $new_name.'.'.$ext;

    // Compare it to the real filename
    if ($new_name != $filename_base) {
      $to_rename[$filename] = $new_name;
    }
  }
  
  if (! empty($unknown)) {
    $question = count($unknown).' books are unknown. Do you want to cancel?   1 = Cancel,  9 = Continue'; 
    $i = readline($question."\n");
    if ($i == 1) { print_r($unknown); exit; }; 
  }

  if (empty($to_rename)) {
    echo 'No books need to be renamed. Finished.'."\n";
    exit;
  }

  // Still here? The start renaming!
  echo 'The following books will be renamed: '."\n";
  foreach ($to_rename as $old_name => $new_name) {
    echo 'Old '.basename($old_name)."\n";
    echo 'New '.basename($new_name)."\n";
    if ($files[$new_name] != '' ) {
      echo 'Waring! the new file above already exists!!'."\n";
    }
  }
  $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
  if ($i != 1) { exit; }; 

  foreach ($to_rename as $old_name => $new_name) {
    echo 'Old '.basename($old_name)."\n";
    echo 'New '.basename($new_name)."\n";
    $cmd = 'aws s3 mv --sse AES256 "s3://'.$book_bucket.'/'.$book_bucket_prefix.$old_name.'"';
    $cmd = $cmd.' "s3://'.$book_bucket.'/'.$book_bucket_prefix.$new_name.'"';
    shell_exec($cmd);
  }

  echo 'Finished'."\n";
  exit;
}

if ($option == 3) { // Import new files. 
  //Prepend date and time to filename
  $files = get_list_of_new_book_files();

  $prepend = @date("Y-d-m_H-i");
  foreach($files as $filename) {
    echo basename($filename)."\n";
    shell_exec('aws s3 mv --sse AES256 "'.$filename.'" "s3://'.$book_bucket.'/'.$book_bucket_prefix.$prepend.'_'.basename($filename).'"');
  }
  exit;
}

if ($option == 2) { // Import XML
  // 1. Check if the file exists
  if (! file_exists($library_xml_import)) {
    echo 'Import XML File not found at '.$library_xml_import."\n";
    exit;
  }

  // 2. Get List of files and their md5 hashes
  $files = get_list_of_book_files_with_md5();
  if (empty($files)) {
    echo 'No books were found in '.$book_bucket.'/'.$book_bucket_prefix.'  -  Import canceled.'."\n";
    exit; 
  }

  // 3. Read Import file
  $not_here = $here = $to_update = $update_errors = array();
  $import = simplexml_load_file($library_xml_import);
  foreach ($import->children() as $book) {
    $md5 = (string)$book['md5'];
    if (! array_key_exists($md5, $files)) {
      array_push($not_here, $book);
      continue;
    }
    array_push($here, $book);
  }

  // Ask user if he wants to continue despite missing book files
  if (! empty($not_here)) {
    echo 'The following book files could not be found: '."\n";
    foreach($not_here as $book) {
      echo trim($book['author'].' '.$book['title'])."\n";
      echo '  '.$book['filename'].' '.$book['md5']."\n";
    }
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }

  // Find out which files need to be updated
  //// Load library, compare information
  $library = get_library_xml();
  if (empty($library)) {
    echo 'No library file was found. A new one was created at /tmp/'.$library_xml_name."\n";
    $library = create_library_xml_file();
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }
  foreach ($here as $book) {
    $known_book = $library->xpath('book[@md5="'.(string)$book['md5'].'"]');
    if (empty($known_book)) {
      array_push($to_update, $book);
    } else {
      // Compare data
      if ((string)$known_book[0]['title']       !== (string)$book['title']       or 
          (string)$known_book[0]['author']      !== (string)$book['author']      or 
          (string)$known_book[0]['author_sort'] !== (string)$book['author_sort'] or
          (string)$known_book[0]['genre']       !== (string)$book['genre']) {
        if (! ends_with($book['filename'], 'pdf' )) {
          array_push($to_update, $book);
        }
      }
    }
  }

  if (empty($to_update)) {
    echo 'No need to update anything. Exiting ...'."\n";
    exit;
  }

  // Ask the user if he wants to continue
  echo 'The following files will be updated:'."\n";
  foreach($to_update as $book) {
    //Find the current filename via md5 hash
    $md5 = (string)$book['md5'];
    $filename = $files[$md5];
    echo '  '.basename($filename)."\n";
  }

  $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
  if ($i != 1) { exit; }; 

  // Begin updates
  $count = count($to_update);
  $i = 0;
  foreach($to_update as $book) {
    //Find the current filename via md5 hash
    $md5 = (string)$book['md5'][0];
    $filename = $files[$md5];
    $i = $i + 1;
    echo 'Processing book: '.$i.'/'.$count.': '.basename($filename)."\r";
    $error = update_file_with_info($filename, $book);
    if ($error != '') {
      array_push($update_errors, basename($filename), $error);
      print_r($error);
    } else {
      shell_exec('aws s3 cp --sse AES256 "/tmp/'.basename($filename).'" "s3://'.$book_bucket.'/'.$book_bucket_prefix.basename($filename).'"');
    }
  }
  echo "\n";

  // Did errors occur?
  if (! empty($update_errors)) {
    echo 'Update errors occured for the following books:'."\n";
    foreach ($update_errors as $filename => $error) {
      echo $filename."\n";
      echo $error."\n";
    }
  }
  
  echo 'Import Finished'."\n";
  exit; 
}


if ($option == 1) { // Export XML
  // 1. Get list of files in the folder and their md5 hash
  $files = get_list_of_book_files_with_md5();
  if (empty($files)) {
    echo 'No books were found in '.$book_bucket.'/'.$book_bucket_prefix.'  -  Export canceled.'."\n";
    exit; 
  }

  // 3. Look for which books we already have information in the library
  $library = get_library_xml();
  if (empty($library)) {
    echo 'No library file was found. A new one was created at /tmp/'.$library_xml_name."\n";
    $library = create_library_xml_file();
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }

  $new_library = create_library_xml();
  $known = $new = $no_info = array();
  $i = 0;
  $count = count($files);
  foreach ($files as $md5 => $filename) {
    $i = $i + 1;
    echo 'Processing book: '.$i.'/'.$count.': '.basename($filename)."\r";
    $book = $new_library->addChild('book');
    $book->addAttribute('md5', $md5);
    $known_book = $library->xpath('book[@md5="'.$md5.'"]');
    if (! empty($known_book)) {
      // Output the known information
      array_push($known, $filename);
      $book->addAttribute('title', $known_book[0]['title']);
      $book->addAttribute('author', $known_book[0]['author']);
      $book->addAttribute('author_sort', $known_book[0]['author_sort']);   
      $book->addAttribute('genre', $known_book[0]['genre']);
      $book->addAttribute('filename', basename($filename));
      if ($known_book[0]['date_added'] != '' ) {
        $book->addAttribute('date_added', $known_book[0]['date_added']);
      } else {
        $book->addAttribute('date_added', @date("Y-d-m"));
      }
      if ($known_book[0]['time_added'] != '' ) {
        $book->addAttribute('time_added', $known_book[0]['time_added']);
      } else {
        $book->addAttribute('time_added', @date("H:i"));
      }
    } else {
      // Read information from file
      $info = get_book_info_from_file($filename);
      if (empty($info)) {
        array_push($no_info, $filename);
        continue;
      }
      // Originally, this were AddChildren statements
      // But my SQLite Import required the data as attributes
      $book->addAttribute('title', $info['Title']);
      $book->addAttribute('author', $info['Author(s)']);
      $book->addAttribute('author_sort', $info['SortAuthor']);   
      $book->addAttribute('genre', $info['Tags']);
      $book->addAttribute('filename', basename($filename));
      $book->addAttribute('date_added', @date("Y-d-m"));
      $book->addAttribute('time_added', @date("H:i"));
      array_push($new, $filename);
    }
  }

  // 4. Output protocol, request confirmation to continue
  if (! empty($no_info)) {
    echo 'For the following books, no information could be read:'."\n";
    foreach($no_info as $filename) {
      echo ' '.$filename."\n";
    }
  }
  if (! empty($new)) {
    echo 'The following books are new or changed:'."\n";
    foreach($new as $filename) {
      echo ' '.$filename."\n";
    }
  }

  if (! empty($no_info) || ! empty($new)) {
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }

  // 5 Create export and overwrite old library file
  $new_library->asXml($library_xml_export);
  save_library_xml_to_xml($new_library);
  save_library_xml_to_json($new_library);
  echo 'Export Finished'."\n";

  // Additional function: Ask if the users wants to open the editing db?
  $i = readline('Do you want to open the book database?   1 = Yes,  9 = No'."\n");
  if ($i != 1) { exit; };
  open_book_db();
  exit;
}

if ($option == 5) {
  open_book_db();
  exit;
}

echo 'This option is currently not implemented. Sorry.'."\n";
?>
