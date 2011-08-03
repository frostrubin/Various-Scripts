#!/usr/bin/env php
<?php
$slabletHTML = "";
$essayfolder = '/Users/bernhard/Dropbox/Essay/'; //slash am Ende!
$seconds = @date('U');
$outfile = '/tmp/slablethtml_'.$seconds.'.html';
    shell_exec("ln -s /pub/Scripts/slablet/assets /tmp/assets");
$macwiki = 'https://github.com/frostrubin/Mac-OS-X-Tricks/wiki/_pages';
    
    function get_string_between($string, $start, $end) {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }

    function getDirectoryList ($directory) 
    {
        // create an array to hold directory list
        $results = array();
        // create a handler for the directory
        $handler = opendir($directory);
        // open directory and walk through the filenames
        while ($file = readdir($handler)) {
            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != ".." && $file!= ".DS_Store") {
                $results[] = $file;
            }
        }
        // tidy up: close the handler
        closedir($handler);
        // done!
        return $results;
    }
    
    function writeToFile($file, $text) {
        $outfile = fopen ($file, "w"); 
        fwrite($outfile, $text); 
        fclose ($outfile);
    }
    
    
    function isFileOfFiletype($file,$ext) {
        $path_parts = pathinfo($file);
        $extension = $path_parts['extension'];
        if ($extension  == $ext) {
            return true;
        } else {
            return false;
        }
    }
    
    function isFileHTML ($file) {
        if (isFileOfFiletype($file,"html") == true or 
            isFileOfFiletype($file,"htm") == true ) {
            return true;
        } else {
            return false;
        }
        
    }

    $essayfiles = getDirectoryList($essayfolder);
    $essayhtmlfiles = array_filter($essayfiles, "isFileHTML");
   
    
$slabletHTML='
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Slablet</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/stylesheets/master.css" />
    <style type="text/css">
    .invisible{display:none;}
    </style>
    <script type="text/javascript">
    function writeToContent(text, contentID) {
    document.getElementById(contentID).innerHTML=text;
    }
    function getTextFromContent(contentID) {
    return document.getElementById(contentID).innerHTML;    
    }
    
    function fillMainContentFromElement(elementID) {
    writeToContent(getTextFromContent(elementID), \'main_content_inner\');
    }
    function findFirstDescendant(parent, tagname)
    {
    parent = document.getElementById(parent);
    var descendants = parent.getElementsByTagName(tagname);
    if ( descendants.length )
    return descendants[0];
    return null;
    }
    
  //  var header = findFirstDescendant("header-inner", "h1");
  //  function fillHeadingFromMainContent() {
  //  heading = findFirstDescendant(\'main_content_inner\', \'h1\');
  //  writeToContent(heading.innerText, \'main_page_heading\');
  //  heading.style.display = \'none\';
  //  }
    
    function fillHeadingFromMainContent(elementID) {
      pTag1 = document.getElementById(elementID);
      heading = pTag1.getAttribute(\'heading\')
      writeToContent(heading, \'main_page_heading\');
    }
    
    
    </script>
    <!--[if IE 8]>
    <link rel="stylesheet" href="assets/stylesheets/ie8.css" />
    <![endif]-->
    <!--[if !IE]><!-->
    <script src="assets/javascripts/iscroll.js"></script>
    <!--<![endif]-->
    <script src="assets/javascripts/jquery.js"></script>
    <script src="assets/javascripts/master.js"></script>
    </head>
    <body>
    <div id="main" class="abs">
	<div class="abs header_upper chrome_light">
    <span class="float_left button" id="button_navigation">
    Navigation
    </span>
    <a href="#" class="float_left button">
    Back
    </a>
    <a href="#" class="icon icon_gear2 float_right"></a>
    <div id="main_page_heading">Page Title Here</div>
	</div>
	<div id="main_content" class="abs">
    <div id="main_content_inner">
    <h1>
    Main Content
    </h1>
    
    </div>
	</div>
	
    </div>
    <div id="sidebar" class="abs">
	<span id="nav_arrow"></span>
	<div class="abs header_upper chrome_light">
    ⌘ wiki
	</div>
	<!--
    <form action="" class="abs header_lower chrome_light">
    <input type="search" id="q" name="q" placeholder="Search..." />
	</form>
    -->
	<div id="sidebar_content" class="abs">
    <div id="sidebar_content_inner">
    <ul id="sidebar_menu">
    <li id="sidebar_menu_home" class="active">
    <a href="#"><span class="abs"></span>Home</a>
    </li>
    <li>
    <a href="#">Essay</a>
    <ul>
    
    ';

    foreach ($essayhtmlfiles as &$htmlfile) {
        $heading = str_replace('.html','', $htmlfile);
        $tagname = str_replace(' ','_',preg_replace("/[^A-Za-z0-9]/","",$htmlfile));
        $tagname = 'essay_'.$tagname;
        if ($heading != '') {
            $slabletHTML = $slabletHTML.'
            <li>
            <a href="javascript:fillMainContentFromElement(\''.$tagname.'\');fillHeadingFromMainContent(\''.$tagname.'\');">'.$heading.'</a>
            </li>';
        }
    }
    
$slabletHTML = $slabletHTML.'
    </ul>
    </li> <!-- Essay Files Ende -->';
    
$slabletHTML = $slabletHTML.'
    <li>
    <a href="#">Mac Wiki</a>
    <ul>';

    $macwikipages = @file_get_contents($macwiki);
    $macwikipages = get_string_between($macwikipages, '<div id="template">','</div>');
    $macwikilinks = explode('<li>', $macwikipages);
    
    foreach ($macwikilinks as &$macwikipage) {
        //echo $macwikipage;
        $heading = get_string_between($macwikipage, '">','</a>');
        $tagname = str_replace(' ','_',preg_replace("/[^A-Za-z0-9]/","",$heading));
        $tagname = 'githubmacwiki'.$tagname;
        if ($heading != '' && $heading != 'Home') {
            $slabletHTML = $slabletHTML.'
            <li>
            <a href="javascript:fillMainContentFromElement(\''.$tagname.'\');fillHeadingFromMainContent(\''.$tagname.'\');">'.$heading.'</a>
            </li>';
        }
    }
    
    
$slabletHTML = $slabletHTML.'
    </ul>
    </li> <!-- Mac Wiki Files Ende -->';

$slabletHTML = $slabletHTML.'
    </ul>
    </div>
	</div>';
    
    foreach ($essayhtmlfiles as &$htmlfile) {
        $heading = str_replace('.html','', $htmlfile);
        $htmlfile = str_replace(' ','\ ', $htmlfile);
        $htmlcat = shell_exec('cat '.$essayfolder.$htmlfile);
        
        $htmlcontent = get_string_between($htmlcat, '<body>', '</body>');
        if ($htmlcontent == '') {
            $htmlcontent = get_string_between($htmlcat, '<body class="ipad window">', '</body>');
        }
        $tagname = str_replace(' ','_',preg_replace("/[^A-Za-z0-9]/","",$htmlfile));
        $tagname = 'essay_'.$tagname;
        $slabletHTML = $slabletHTML.'
        <div id="'.$tagname.'" class="invisible" heading="'.$heading.'">'.$htmlcontent.'</div>';
    }
    
    foreach ($macwikilinks as &$macwikipage) {
        $heading = get_string_between($macwikipage, '">','</a>');
        $tagname = str_replace(' ','_',preg_replace("/[^A-Za-z0-9]/","",$heading));
        $tagname = 'githubmacwiki'.$tagname;
        $macwikilink = get_string_between($macwikipage, '<a href="', '">');
        $macwikilink = 'https://github.com'.$macwikilink;
        $wikipage = @file_get_contents($macwikilink);
        //echo $wikipage;
        $wikipage = get_string_between($wikipage, '<div id="wiki-content">', '<div id="gollum-footer">');
        $wikipage = '<div>'.$wikipage;
        if ($heading != '' && $heading != 'Home') {
        $slabletHTML = $slabletHTML.'
        <div id="'.$tagname.'" class="invisible" heading="'.$heading.'">'.$wikipage.'</div>';
        }
    }

$slabletHTML = $slabletHTML.'
    </div>
    </body>
    </html>
';
    writeToFile($outfile, $slabletHTML);
    echo $outputhtml;
    shell_exec('open '.$outfile);
?>