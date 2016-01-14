#!/usr/bin/env php
<?php

$book_bucket         = 'bucketname';
$book_bucket_prefix  = 'books/'; // Slash at the end
$upload_save_bucket  = 'backup_bucket';
$upload_save_prefix  = 'Books_Uploaded/'; //Slash at the end
$book_types          = array( 'epub' , 'pdf', 'another_file_ending' );
$library_xml_name    = '1_Library.xml';
$library_json_name   = '1_Library.json';
$new_book_folder     = '/Users/username/Desktop/import/'; // Slash at the end
$library_xml_export  = '/Users/username/Desktop/export.xml';
$library_xml_import  = '/Users/username/Desktop/Books.xml';
$book_db             = '/Users/username/Desktop/Books.db';
$ebook_meta_lambda   = 'EbookMetaLambdaFunction';
$ebook_meta_json_in  = '/tmp/ebook_meta_input.json';
$ebook_meta_json_out = '/tmp/ebook_meta_output.json';

function create_book_db() { // The Book DB is not part of the core functionality of this tool
  global $book_db;
  $STRUCTURE='CREATE TABLE Books (filename text(300) PRIMARY KEY NOT NULL, title text(300),
  author text(300), author_sort text(300), genre char(128) );';
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
  echo '  3) Import new files'."\n".'  5) Open book database'."\n"; 
  echo 'The normal flow is: 3 -> 1 -> 2 -> 1'."\n";

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
    if (ends_with(strtolower($filename), strtolower($book_type))) {
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
    if (book_type_relevant($info_array['Key'])) {
      array_push($files, $info_array['Key']);
    }
  }
  return $files;
}

function get_list_of_book_files_with_mtime() { // Get list of book files from the library folder with corresponding last modified date
  global $book_bucket;
  global $book_bucket_prefix;
  echo 'Getting list of book files with mtime ...'."\n";
  $files = array();
  $cmd = 'aws s3api list-objects --bucket '.$book_bucket.' --prefix "'.$book_bucket_prefix.'"';
  $cmd = $cmd.' --query \'Contents[].{Key: Key, Mtime: LastModified}\'';
  $json = shell_exec($cmd);
  $json_array = json_decode($json, TRUE);
  foreach($json_array as $info_array) {
    if (book_type_relevant($info_array['Key'])) {
      $files[$info_array['Key']] = $info_array['Mtime'];
    }
  }
  return $files; 
}

function get_list_of_new_book_files() { // Get list of new files from the specific folder
  global $new_book_folder;
  $files = array();
  if ($handle = opendir($new_book_folder)) {
      while (false !== ($file = readdir($handle))) {
          $i = $i + 1;
          if ($file !== "." && $file !== ".." && book_type_relevant($file)) {
            $fullFilePath = $new_book_folder.basename($file);
            array_push($files, $fullFilePath);
          }
      }
      closedir($handle);
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
  if ($retcode !== 0 ){
    print_r($output);
    return;
  }

  return simplexml_load_file('/tmp/'.$library_xml_name);
}

function str_last_replace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);
  if ($pos !== false) {
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  }
  return $subject;
}

function read_ebook_meta_lambda_result_data() { // Return the lines from the "data" segment that contain calibre output
  global $ebook_meta_json_out;
  $json_array = json_decode(file_get_contents($ebook_meta_json_out), TRUE);
  return $json_array['data'];
}

function execute_ebook_meta_lambda() { //Execute the lambda function. Returns only the terminal output.
  global $ebook_meta_lambda;
  global $ebook_meta_json_in;
  global $ebook_meta_json_out;
  shell_exec('rm -f "'.$ebook_meta_json_out.'"');
  $cmd = 'aws lambda invoke --function-name '.$ebook_meta_lambda.' --invocation-type RequestResponse';
  $cmd = $cmd.' --payload fileb://'.$ebook_meta_json_in.' '.$ebook_meta_json_out.' 2>&1'; //STDERR to STDOUT
  return shell_exec($cmd);
  //return shell_exec('/Users/bernhard/Desktop/metafake/metafake.sh "'.$ebook_meta_json_out.'"');
}

function check_lambda_status_retcode() { // Return the status code of a lambda execution result
  // This function can only work correctly, if check_for_lambda_tech_error did not find an error!
  global $ebook_meta_json_out;
  $json_array = json_decode(file_get_contents($ebook_meta_json_out), TRUE);
  $status = $json_array['status'];
  if ($status !== 0) {
    echo 'Lambda function encountered a handeled error:'."\n";
    if (array_key_exists('msg', $json_array)) {
      echo $json_array['msg']."\n";
    } else {
      echo 'No error message was set!'."\n";
    }
  }
  return $status;
}

function check_for_lambda_tech_error($terminal_result) {
  global $ebook_meta_json_out;
  $error = '';
  $output = '';
  if (file_exists($ebook_meta_json_out)) {
    $output = file_get_contents($ebook_meta_json_out);
  }
  $output_array = json_decode($output, TRUE);
  $json_array = json_decode($terminal_result, TRUE);
  if (empty($json_array)) {
    $error = $terminal_result; // Output was not valid JSON
  } else {
    if ($json_array['StatusCode'] !== 200) { // Only Status Code 200 is good
      $error = $terminal_result;
    }
    $error = $output_array['errorMessage'];
  }
  
  if (! file_exists($ebook_meta_json_out)) {
    $error = $error."\n".'No lamabda output file was created at '.$ebook_meta_json_out;
  }

  if (empty($output_array)) {
    $error = $error."\n".'The output file did not contain valid JSON: '.$ebook_meta_json_out;
  } elseif (! array_key_exists('status', $output_array)) {
    $error = $error."\n".'The output file did not have a JSON field named status: '.$ebook_meta_json_out;
  }

  if (! empty($error)) {
    echo 'Technical Error when calling the lambda function:'."\n";
    echo $error."\n";
    if (! empty($output)) {
      echo 'Lambda function result:'."\n";
      echo $output."\n";
    }
  }
  return $error;
}

function get_book_info_via_lambda($filename_in) {
  global $ebook_meta_json_in;
  global $book_bucket;
  global $book_bucket_prefix;
  echo 'Getting book info for '.$filename_in."\n";
  $info = array();
  if (ends_with(strtolower($filename_in), 'pdf')) {
    $parts = explode(' - ', basename($filename_in));
    $author = trim($parts[0]);
    $info['Author(s)'] = $author;
    array_shift($parts);
    $info['Title'] = implode(' - ',$parts);
    $info['Title'] = str_ireplace('.pdf','',$info['Title']);
    $info['Tags'] = 'PDF File'; // Genre
    return $info;
  } 

  shell_exec('echo "{" > "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"bucket\": \"'.$book_bucket.'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"file_in\": \""'.$filename_in.'\"" >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo "}" >> "'.$ebook_meta_json_in.'"');
  $terminal_result = execute_ebook_meta_lambda();
  $tech_error = check_for_lambda_tech_error($terminal_result);
  if (! empty($tech_error)) {
    return $info;
  }

  $retcode = check_lambda_status_retcode();
  if ($retcode !== 0) {
    return $info;
  }
  
  $data = read_ebook_meta_lambda_result_data();
  if (empty(data)) {
    return $info;
  }

  foreach($data as $key => $value) {
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $value) as $line){
      //Put each line into an array variable
      $parts = explode(":", $line, 2);
      if (array_key_exists(1, $parts)) {
        $info[trim($parts[0])] = trim($parts[1]);
        if (trim($parts[0]) === 'Author(s)') {
          $author_sort = array();
          preg_match("/\[(.*)\]/", trim($parts[1]), $author_sort);
          $info['SortAuthor'] = $author_sort[1];
          $info[trim($parts[0])] = trim(str_last_replace( $author_sort[0] , '', trim($parts[1])));
        }
      }
    } 
  }
  return $info;
}

function set_book_info_via_lambda($filename_in, $book) {
  global $book_bucket;
  global $book_bucket_prefix;
  global $ebook_meta_json_in;
  global $ebook_meta_json_out;
  $filename_out = generate_new_file_name($book);
  $title        = (string)$book['title'];
  $author       = (string)$book['author'];
  $author_sort  = (string)$book['author_sort'];
  $genre        = (string)$book['genre'];
  echo 'Setting book info for '.$filename_in."\n";
  shell_exec('echo "{" > "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"bucket\": \"'.$book_bucket.'\", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"file_in\": \""'.$filename_in.'\"", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"file_out\": \""'.$filename_out.'\"", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"title\": \""'.$title.'\"", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"author\": \""'.$author.'\"", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"author_sort\": \""'.$author_sort.'\"", >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo \"genre\": \""'.$genre.'\"" >> "'.$ebook_meta_json_in.'"');
  shell_exec('echo "}" >> "'.$ebook_meta_json_in.'"');
  $terminal_result = execute_ebook_meta_lambda();
  $tech_error = check_for_lambda_tech_error($terminal_result);
  if (! empty($tech_error)) {
    return $tech_error;
  }
  
  $retcode = check_lambda_status_retcode();
  if ($retcode !== 0) {
    return 'Update Error: Retcode <> 0';
  }
}

function slug($input) {
  $string = $input;
  $string = str_replace("\n", '', $string);
  $string = str_replace("\r", '', $string);
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

function generate_new_file_name($book) {
  global $book_bucket_prefix;
  $filename     = (string)$book['filename'];
  $title        = (string)$book['title'];
  $author       = (string)$book['author'];
  $new_name = $author.' - '.$title;
  $new_name = slug($new_name);
  $new_name = $book_bucket_prefix.$new_name.'.'.strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  return $new_name;
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
    $string = $string.'    "author": "'.$author.'",'."\n";
    $string = $string.'    "title": "'.$title.'",'."\n";
    $string = $string.'    "genre": "'.$genre.'",'."\n";
    $string = $string.'    "filename": "'.$filename.'"'."\n";
    $string = $string.'  },'."\n";  
  }
  $string = $string.'  {'."\n".'  }'."\n".']';
  $string = json_encode(json_decode($string)); //Nicer JSON via Standard functions
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

if ($option == 3) { // Import new files. 
  //Prepend date and time to filename
  $files = get_list_of_new_book_files();

  $prepend = @date("Y-d-m_H-i");
  foreach($files as $filename) {
    echo basename($filename)."\n";
    $uploadname = $prepend.'_'.slug(pathinfo($filename, PATHINFO_FILENAME)).'.'.strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $uploadname = trim($uploadname);
    shell_exec('aws s3 mv --sse AES256 "'.$filename.'" "s3://'.$book_bucket.'/'.$book_bucket_prefix.$uploadname.'"');
    $cmd = 'aws s3 cp --sse AES256 "s3://'.$book_bucket.'/'.$book_bucket_prefix.$uploadname.'"';
    $cmd = $cmd.' "s3://'.$upload_save_bucket.'/'.$upload_save_prefix.$uploadname.'"';
    shell_exec($cmd);
  }
  exit;
}

if ($option == 2) { // Import XML
  // Check if the import file exists
  if (! file_exists($library_xml_import)) {
    echo 'Import XML File not found at '.$library_xml_import."\n";
    exit;
  }

  // Get List of files book files
  $files = get_list_of_book_files();
  if (empty($files)) {
    echo 'No books were found  -  Import canceled.'."\n";
    exit; 
  }

  // Read Import file
  $not_here = $here = $to_update = $update_errors = array();
  $import = simplexml_load_file($library_xml_import);
  foreach ($import->children() as $book) {
    $import_filename = (string)$book['filename'];
    $index = array_search($import_filename, $files);
    if ($index !== false) {
      array_push($here, $book);
    } else {
      array_push($not_here, $book);
    }
  }

  // Ask user if he wants to continue despite missing book files
  if (! empty($not_here)) {
    echo 'The following book files could not be found in S3: '."\n";
    foreach($not_here as $book) {
      echo trim($book['author'].' '.$book['title'])."\n";
      echo '  '.$book['filename']."\n";
    }
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }

  // Find out which files need to be updated: Load library, compare information
  $library = get_library_xml();
  if (empty($library)) {
    echo 'No library file was found. A new one was created at /tmp/'.$library_xml_name."\n";
    $library = create_library_xml_file();
    $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
    if ($i != 1) { exit; }; 
  }

  echo 'Comparing Info ...'."\n";
  foreach ($here as $book) {
    $known_book = $library->xpath('book[@filename="'.(string)$book['filename'].'"]');
    if (empty($known_book)) {
      array_push($to_update, $book); // It is a new file!
    } else {
      // Compare data
      if ((string)$known_book[0]['title']       !== (string)$book['title']       or 
          (string)$known_book[0]['author']      !== (string)$book['author']      or 
          (string)$known_book[0]['author_sort'] !== (string)$book['author_sort'] or
          (string)$known_book[0]['genre']       !== (string)$book['genre']       or
          (string)$known_book[0]['filename']    !== generate_new_file_name($book)
         ) {
        if (! ends_with(strtolower((string)$book['filename']), 'pdf' )) {
          array_push($to_update, $book);
        }
      }
    }
  }

  if (empty($to_update)) {
    echo 'No need to update anything. Finished.'."\n";
    exit;
  }

  // Collect known names and new names into one array 
  $total_names = array(); 
  foreach ($files as $filename) { 
    array_push($total_names, $filename); 
  } 
  foreach ($to_update as $book) { 
    array_push($total_names, generate_new_file_name($book)); 
  } 
  // Now look if we were giving out one name multiple times 
  $cnt = array_count_values($total_names); 
  foreach ($to_update as $book) { 
    $new_name = generate_new_file_name($book); 
    if ($cnt[$new_name] > 1 ) {
      echo 'Duplicate filenames woule be produced:'."\n";
      echo $new_name."\n"; 
      exit;
    } 
  }

  // Ask the user if he wants to continue
  echo 'The following files will be updated:'."\n";
  foreach ($to_update as $book) {
    $filename = (string)$book['filename'];
    echo '  '.$filename."\n";
  }

  $i = readline('Do you want to continue?   1 = Yes,  9 = No'."\n");
  if ($i != 1) { exit; }; 

  // Begin updates
  $count = count($to_update);
  $i = 0;
  foreach($to_update as $book) {
    $i = $i + 1;
    $filename = (string)$book['filename'];
    echo 'Processing book: '.$i.'/'.$count.': '.$filename."\r";
    $error = set_book_info_via_lambda($filename, $book);

    if (! empty($error)) {
      $update_errors[$filename] = $error;
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
  // Get list of files in the folder
  $files = get_list_of_book_files_with_mtime();
  if (empty($files)) {
    echo 'No books were found  -  Export canceled.'."\n";
    exit; 
  }

  // Look for which books we already have information in the library
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
  foreach ($files as $filename => $mtime) {
    $call_lambda = FALSE;
    $info = '';
    $i = $i + 1;
    echo 'Processing book: '.$i.'/'.$count.': '.$filename."\r";
    $book = $new_library->addChild('book');
    $known_book = $library->xpath('book[@filename="'.$filename.'"]');
    $book->addAttribute('filename', $filename);
    $book->addAttribute('mtime', $mtime);
    if (! empty($known_book)) {
      $mtime_old = $known_book[0]['mtime'];
      if ($mtime != $mtime_old) {
        $call_lambda = TRUE;
      }
    } else {
      $call_lambda = TRUE;
    }

    if ($call_lambda === TRUE) {
      // Read information from file
      $info = get_book_info_via_lambda($filename);
      if (empty($info)) {
        array_push($no_info, $filename);
        continue;
      }   
    }

    // Still here? Then we have info either in KNOWN_BOOK or in INFO
    if (! empty($info)) {
      array_push($new, $filename);
      $known_book[0]['title']       = $info['Title'];
      $known_book[0]['author']      = $info['Author(s)'];
      $known_book[0]['author_sort'] = $info['SortAuthor'];
      $known_book[0]['genre']       = $info['Tags'];
    } else {
      array_push($known, $filename);
    }

    // Output the known information
    // Originally, this were AddChildren statements
    // But my SQLite Import required the data as attributes
    $book->addAttribute('title',       $known_book[0]['title']);
    $book->addAttribute('author',      $known_book[0]['author']);
    $book->addAttribute('author_sort', $known_book[0]['author_sort']);   
    $book->addAttribute('genre',       $known_book[0]['genre']);
  }

  // Output protocol, request confirmation to continue
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

  // Create export and overwrite old library file
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
