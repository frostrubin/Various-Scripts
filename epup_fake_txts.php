#!/usr/bin/env php
<?php

$library_xml_file = '/Users/xyz/Books/1_Library.xml'; 
$mock_folder      = '/Users/xyz/Desktop/mock/';
$book_folder      = '/Users/xyz/Books/';

$library = simplexml_load_file($library_xml_file);
foreach ($library->book as $book) {
	foreach ($book->attributes() as $a => $b) {
	  echo $a,'="',$b,"\"\n";
		switch ($a) {
		    case 'title':
		        $title = $b;       break;
		    case 'author':
		        $author = $b;      break;
		    case 'author_sort':
		        $author_sort = $b; break;
		    case 'genre':
		        $genre = $b;       break;
		    case 'filename':
		        $filename = $b;    break;
		}
	}
  shell_exec('echo '.'"title:'.$title.'"              > '.'"'.$mock_folder.$filename.'"');
  shell_exec('echo '.'"author:'.$author.'"           >> '.'"'.$mock_folder.$filename.'"');
  shell_exec('echo '.'"author_sort:'.$author_sort.'" >> '.'"'.$mock_folder.$filename.'"');
  shell_exec('echo '.'"genre:'.$genre.'"             >> '.'"'.$mock_folder.$filename.'"');
}

?>
