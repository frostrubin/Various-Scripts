#!/usr/bin/env php 
<?php
    
  //First Geeklet
  $geeklets['weatherget']['command'] = '/Users/bernhard/.NerdTool/weather.php getttt';
  $geeklets['weatherget']['type'] = 'command';
  $geeklets['weatherget']['repeat'] = 400;
  $geeklets['weatherget']['isslow'] = 'X';
  $geeklets['weatherget']['left'] = '2em';
  $geeklets['weatherget']['top'] = '2em';
  $geeklets['weatherget']['width'] = '20em';
 // $geeklets['weatherget']['height'] = '10px';
  $geeklets['weatherget']['txtcolor'] = 'rgba(0,255,255,0.0)';
  $geeklets['weatherget']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['weatherget']['fontsize'] = '1em';
  $geeklets['weatherget']['freecss'] = '';
  
  $geeklets['gcal_agenda']['command'] = 'tail -n 20 files/agenda.txt';
  $geeklets['gcal_agenda']['type'] = 'command';
  $geeklets['gcal_agenda']['repeat'] = 90;
  $geeklets['gcal_agenda']['isslow'] = 'X';
  $geeklets['gcal_agenda']['preformatted'] = 'X';
  $geeklets['gcal_agenda']['left'] = '5.7em';
  $geeklets['gcal_agenda']['top'] = '16em';
  $geeklets['gcal_agenda']['width'] = 'auto';
  // $geeklets['gcal_agenda']['height'] = '500px';
  $geeklets['gcal_agenda']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['gcal_agenda']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['gcal_agenda']['fontsize'] = '0.25em';
  $geeklets['gcal_agenda']['freecss'] = 'font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;line-height: 0.2em;';
  
  $geeklets['temperatures']['command'] = '/Users/bernhard/.NerdTool/temperatures.sh';
  $geeklets['temperatures']['type'] = 'command';
  $geeklets['temperatures']['repeat'] = 60;
  $geeklets['temperatures']['isslow'] = '';
  $geeklets['temperatures']['left'] = '32em';
  $geeklets['temperatures']['top'] = '16em';
  $geeklets['temperatures']['width'] = 'auto';
  // $geeklets['temperatures']['height'] = '500px';
  $geeklets['temperatures']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['temperatures']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['temperatures']['fontsize'] = '0.3em';
  $geeklets['temperatures']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast0']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast0';
  $geeklets['forecast0']['type'] = 'command';
  $geeklets['forecast0']['repeat'] = 20;
  $geeklets['forecast0']['isslow'] = '';
  $geeklets['forecast0']['left'] = '5.7em';
  $geeklets['forecast0']['top'] = '3.5em';
  $geeklets['forecast0']['width'] = 'auto';
 // $geeklets['forecast0']['height'] = '500px';
  $geeklets['forecast0']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast0']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast0']['fontsize'] = '0.2em';
  $geeklets['forecast0']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast0image']['image'] = './files/forecast0.png';
  $geeklets['forecast0image']['type'] = 'image';
  $geeklets['forecast0image']['repeat'] = 20;
  $geeklets['forecast0image']['isslow'] = '';
  $geeklets['forecast0image']['left'] = '4em';
  $geeklets['forecast0image']['top'] = '2em';
  $geeklets['forecast0image']['width'] = '6.5em';
 // $geeklets['forecast0image']['height'] = '180px';
 // $geeklets['forecast0image']['txtcolor'] = 'rgba(0,255,255,0.0)';
 // $geeklets['forecast0image']['background'] = 'url(./files/forecast0.png)';
  $geeklets['forecast0image']['transparency'] = '0.4';
  $geeklets['forecast0image']['freecss'] = '';
  
  $geeklets['forecast1']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast1';
  $geeklets['forecast1']['type'] = 'command';
  $geeklets['forecast1']['repeat'] = 20;
  $geeklets['forecast1']['isslow'] = '';
  $geeklets['forecast1']['left'] = '9.9em';
  $geeklets['forecast1']['top'] = '4.4em';
  $geeklets['forecast1']['width'] = 'auto';
  // $geeklets['forecast1']['height'] = '500px';
  $geeklets['forecast1']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast1']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast1']['fontsize'] = '0.2em';
  $geeklets['forecast1']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast1image']['image'] = './files/forecast1.png';
  $geeklets['forecast1image']['type'] = 'image';
  $geeklets['forecast1image']['repeat'] = 20;
  $geeklets['forecast1image']['isslow'] = '';
  $geeklets['forecast1image']['left'] = '9em';
  $geeklets['forecast1image']['top'] = '2.7em';
  $geeklets['forecast1image']['width'] = '3.2em';
  // $geeklets['forecast1image']['height'] = '180px';
  // $geeklets['forecast1image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  // $geeklets['forecast1image']['background'] = 'url(./files/forecast1.png)';
  $geeklets['forecast1image']['transparency'] = '0.4';
  $geeklets['forecast1image']['freecss'] = '';
  
  $geeklets['forecast2']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast2';
  $geeklets['forecast2']['type'] = 'command';
  $geeklets['forecast2']['repeat'] = 20;
  $geeklets['forecast2']['isslow'] = '';
  $geeklets['forecast2']['left'] = '13.9em';
  $geeklets['forecast2']['top'] = '4.4em';
  $geeklets['forecast2']['width'] = 'auto';
  // $geeklets['forecast2']['height'] = '500px';
  $geeklets['forecast2']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast2']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast2']['fontsize'] = '0.2em';
  $geeklets['forecast2']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast2image']['image'] = './files/forecast2.png';
  $geeklets['forecast2image']['type'] = 'image';
  $geeklets['forecast2image']['repeat'] = 20;
  $geeklets['forecast2image']['isslow'] = '';
  $geeklets['forecast2image']['left'] = '13em';
  $geeklets['forecast2image']['top'] = '2.7em';
  $geeklets['forecast2image']['width'] = '3.2em';
  // $geeklets['forecast2image']['height'] = '180px';
  // $geeklets['forecast2image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  // $geeklets['forecast2image']['background'] = 'url(./files/forecast2.png)';
  $geeklets['forecast2image']['transparency'] = '0.4';
  $geeklets['forecast2image']['freecss'] = '';
  
  $geeklets['forecast3']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast3';
  $geeklets['forecast3']['type'] = 'command';
  $geeklets['forecast3']['repeat'] = 20;
  $geeklets['forecast3']['isslow'] = '';
  $geeklets['forecast3']['left'] = '17.9em';
  $geeklets['forecast3']['top'] = '4.4em';
  $geeklets['forecast3']['width'] = 'auto';
  // $geeklets['forecast3']['height'] = '500px';
  $geeklets['forecast3']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast3']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast3']['fontsize'] = '0.2em';
  $geeklets['forecast3']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast3image']['image'] = './files/forecast3.png';
  $geeklets['forecast3image']['type'] = 'image';
  $geeklets['forecast3image']['repeat'] = 20;
  $geeklets['forecast3image']['isslow'] = '';
  $geeklets['forecast3image']['left'] = '17em';
  $geeklets['forecast3image']['top'] = '2.7em';
  $geeklets['forecast3image']['width'] = '3.2em';
  // $geeklets['forecast3image']['height'] = '180px';
  // $geeklets['forecast3image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  // $geeklets['forecast3image']['background'] = 'url(./files/forecast3.png)';
  $geeklets['forecast3image']['transparency'] = '0.4';
  $geeklets['forecast3image']['freecss'] = '';
  
  $geeklets['forecast4']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast4';
  $geeklets['forecast4']['type'] = 'command';
  $geeklets['forecast4']['repeat'] = 20;
  $geeklets['forecast4']['isslow'] = '';
  $geeklets['forecast4']['left'] = '21.9em';
  $geeklets['forecast4']['top'] = '4.4em';
  $geeklets['forecast4']['width'] = 'auto';
  // $geeklets['forecast4']['height'] = '500px';
  $geeklets['forecast4']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast4']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast4']['fontsize'] = '0.2em';
  $geeklets['forecast4']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast4image']['image'] = './files/forecast4.png';
  $geeklets['forecast4image']['type'] = 'image';
  $geeklets['forecast4image']['repeat'] = 20;
  $geeklets['forecast4image']['isslow'] = '';
  $geeklets['forecast4image']['left'] = '21em';
  $geeklets['forecast4image']['top'] = '2.7em';
  $geeklets['forecast4image']['width'] = '3.2em';
  // $geeklets['forecast4image']['height'] = '180px';
  // $geeklets['forecast4image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  // $geeklets['forecast4image']['background'] = 'url(./files/forecast4.png)';
  $geeklets['forecast4image']['transparency'] = '0.4';
  $geeklets['forecast4image']['freecss'] = '';
  
  $geeklets['forecast5']['command'] = '/Users/bernhard/.NerdTool/weather.php forecast5';
  $geeklets['forecast5']['type'] = 'command';
  $geeklets['forecast5']['repeat'] = 20;
  $geeklets['forecast5']['isslow'] = '';
  $geeklets['forecast5']['left'] = '25.9em';
  $geeklets['forecast5']['top'] = '4.4em';
  $geeklets['forecast5']['width'] = 'auto';
  // $geeklets['forecast5']['height'] = '500px';
  $geeklets['forecast5']['txtcolor'] = 'rgba(255,255,255,0.5)';
  $geeklets['forecast5']['background'] = 'rgba(0,0,255,0.0)';
  $geeklets['forecast5']['fontsize'] = '0.2em';
  $geeklets['forecast5']['freecss'] = 'font-family: \'Lucida Grande\';';
  
  $geeklets['forecast5image']['image'] = './files/forecast5.png';
  $geeklets['forecast5image']['type'] = 'image';
  $geeklets['forecast5image']['repeat'] = 20;
  $geeklets['forecast5image']['isslow'] = '';
  $geeklets['forecast5image']['left'] = '25em';
  $geeklets['forecast5image']['top'] = '2.7em';
  $geeklets['forecast5image']['width'] = '3.2em';
  // $geeklets['forecast5image']['height'] = '180px';
  // $geeklets['forecast5image']['txtcolor'] = 'rgba(0,255,255,0.0)';
  // $geeklets['forecast5image']['background'] = 'url(./files/forecast5.png)';
  $geeklets['forecast5image']['transparency'] = '0.4';
  $geeklets['forecast5image']['freecss'] = '';
  
  
  
  
  $html = '
  <html> 
    <head> 
      <style type="text/css"> 
        * {border: 0; 
           padding: 0; 
           margin: 0; 
          } 
        body {background-image: url(./bg.png);}
      </style> 
      <script type="text/javascript"> 
        var font_percent = .018; 
        function total(){ 
          font_percent = document.f.font_percent.value; 
        } 
      </script>
    </head> 
    <body id="txt">';
  
  
  // The very first run of every command is set to
  // a week ago. So at startup, every command is run
  foreach ($geeklets as $id => $array ) {
    $geeklets[$id]['lastcall'] = time() - (7 * 24 * 60 * 60);
  //  if ($geeklets[$id]['type'] == 'image') {
  //    $geeklets[$id]['background'] = $geeklets[$id]['background'].';background-repeat: no-repeat; background-size: 100% 100%;'.'opacity: '.$geeklets[$id]['bgtrans'].';';
  //  }
  }
  

  foreach ($geeklets as $id => $array ) {
    // Run the Scripts
    if (time() - $geeklets[$id]['lastcall'] >= $geeklets[$id]['repeat']) {
      //Time to run the script again
      if ($geeklets[$id]['type'] == 'command') {
        $runinbackground = '';
        if ($geeklets[$id]['isslow'] == 'X') {
          //This command will run pretty slow
          //So we run it in the background an neglegt its output
          $runinbackground = ' &'; 
        }
        $geeklets[$id]['output'] = nl2br(utf8_decode(shell_exec($geeklets[$id]['command'].$runinbackground)));
      } elseif ($geeklets[$id]['type'] == 'image') {
        //data uri scheme for embedding image directly in html
        $geeklets[$id]['output'] = 'data:image/png;base64,'.base64_encode(file_get_contents($geeklets[$id]['image']));
      }
      $geeklets[$id]['lastcall'] = time();
    }
  }
  
  
  // Build the html page
  foreach ($geeklets as $id => $array ) {
    if ($geeklets[$id]['type'] == 'command') {
      echo 'command';
      $html = $html.
      '<div style="'.
        'position: fixed;'.
        'width: '.$geeklets[$id]['width'].';'.
        'background: '.$geeklets[$id]['background'].';'.
        'top: '.$geeklets[$id]['top'].';'.
        'left: '.$geeklets[$id]['left'].';'.
        'color: '.$geeklets[$id]['txtcolor'].';'.

        $geeklets[$id]['freecss'].
      '">';
      if ($geeklets[$id]['preformatted'] == 'X') {
        $html = $html.'<pre ';
      } else {
        $html = $html.'<div ';
      }
      //$html = $html.$geeklets[$id]['output'];
      $html = $html.'style="font-size: '.$geeklets[$id]['fontsize'].';">'.$geeklets[$id]['output'];
      if ($geeklets[$id]['preformatted'] == 'X') {
        $html = $html.'</pre>';
      } else {
        $html = $html.'</div>';
      }
      $html = $html.'</div>';
    } elseif ($geeklets[$id]['type'] == 'image') {
      echo 'image';
      $html = $html.
      '<img style="'.
        'position: fixed;'.
        'width: '.$geeklets[$id]['width'].';'.
        'top: '.$geeklets[$id]['top'].';'.
        'left: '.$geeklets[$id]['left'].';'.
        'opacity: '.$geeklets[$id]['transparency'].';'.
        $geeklets[$id]['freecss'].
      '" src="'.$geeklets[$id]['output'].'" />';
    }
  }
  
  
  
  $html = $html.
  '<script type="text/javascript" src="./resize.js"></script>
   </body>
   </html>';
  
  
  $myFile = "myoutput.html";
  $fh = fopen($myFile, 'w') or die("can't open file");
  $stringData = $html;
  fwrite($fh, $stringData);
  fclose($fh);
?>