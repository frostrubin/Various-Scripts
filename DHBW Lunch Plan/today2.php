<?php
$today = getdate();
$today = $today["wday"];

   if ($today == 0 || $today== 6) {
      echo '
      <html>
      <body style="background-color: #232323">
      </body>
      </html>';
   } else {
$pfad = "http://www.studentenwerk-mannheim.de/mensa/tg_mm.std.php"; 

$string = file_get_contents($pfad);
$string = ereg_replace("\n", " ", $string);
$string = ereg_replace("\r", " ", $string);  
$string = preg_replace ("/<caption(.*?)<\/caption>/", "", $string);
      #      $string = preg_replace ("/<font(.*?)<\/font>/","",$string);
$string = preg_replace ("/<table(.*?)<h1>(.*?)<\/table>/", "", $string);
$string = preg_replace ("/<table(.*?)<h2>(.*?)<\/table>/", "", $string);
$string = preg_replace ("/<table border='0' cellspacing='0' cellpadding='0' class='tag_std' width='100%'><tr bgcolor='#fff1e5'><td width='23%'><b>GEMÜSETHEKE(.*?)align='right'>&nbsp;<\/td><\/tr><\/table>/", "", $string);

$string = str_replace("<div class='box' style='margin-top:10px'></div>","",$string);
   $string = str_replace('(inkl. Tagessuppe)','',$string);
   $string = str_replace(',',', ',$string);
   $string = str_replace('0, ','0,',$string);
   $string = str_replace('1, ','2,',$string);
   $string = str_replace('2, ','2,',$string);
   $string = str_replace('3, ','3,',$string);
   $string = str_replace('Fruchtkaltschale<br />geschlossen','geschlossen',$string);
   $string = str_replace('Fruchtkaltschale<br />','Fruchtkaltschale, ',$string);
   $string = str_replace('#fff1e5','#232323',$string);
   $string = str_replace('( S)','',$string);
   $string = str_replace('(S)','',$string);
   $string = str_replace('(2)','',$string);
   $string = str_replace('*','',$string);
   $string = str_replace('Aufpreis zum Menü/Hauptgericht','',$string);
   $string = str_replace('" ','"',$string);      
   $string = str_replace(' ,',',',$string);
   $string = str_replace('(vegetarisch)','',$string);
   $string = str_replace('23%','',$string);
   $string = str_replace('65%','',$string);
   $string = str_replace('100%','',$string);
   $string = str_replace('right','left',$string);
   $string = str_replace('Getreide - Reisrisotto','Getreide-Reisrisotto',$string);
   $string = str_replace('(inkl. Tagessuppe )','',$string);

       
      

$string = str_replace('../mensa/css/standard.css','http://www.studentenwerk-mannheim.de/mensa/css/standard.css',$string);
$string = str_replace('</head>','<style type="text/css">
   .label_box,
   .zusatz_std {display:none;}                   
                      body {background-color:#232323;}
                      * {color: white;}
                      .tag_std td {border:none;}
                      .tag_std,
                      .box {width:350px;}
                      .box {border: 1px solid #3DA5DE;}
   #Abst_mm {display:none;}
   </style>
</head>',$string);
   
echo $string;
   }
?>
