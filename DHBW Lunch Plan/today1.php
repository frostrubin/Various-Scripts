<?php
$pfad = "http://www.studentenwerk-mannheim.de/mensa/tg_dh_mm1.fullhd.php"; 

$string = file_get_contents($pfad);
$string = str_replace('(S)','',$string);
$string = str_replace('margin-right: 16px','margin-right: 0px',$string);
$string = str_replace('margin-right: 15px','margin-right: 0px',$string);
$string = str_replace('../mensa/css/1920x1080_inv_reader.css','http://www.studentenwerk-mannheim.de/mensa/css/1920x1080_inv_reader.css',$string);
$string = str_replace('</head>','<style type="text/css">
   body {background-color: #232323;}
   #BoxTV {border:none;}
   h3 {display:none;}
   .rtor {background-color:#232323;}
   .tplan_5mm td {vertical-align: top;}
   .tplan_5box {height:60px; 
                width:350px;
                margin-right:0px;
                background-color: #232323;}
   .un_ws,
   .lefto {height: 15px;vertical-align: middle;}
   .tplan_5mm h1 {font-size: 10pt;color: #3DA5DE;}
   .tplan_5mm h2 {font-size: 8pt;
                  margin:0px;
                  color:white;}
   .tplan_5mm h5 {font-size: 8pt;color:white;}
   .tplan_5mm th {height:13pt;}
                         
   .Titel_mm,
   .WoDate_mm,
   .HSName_mm,
   .zusatz_mm,
   .fuss_5 {display:none;}
                         
   #LogoTgTab_mm,
   #Aktion_mm,
   #Titel_mm {display:none;}
   </style>
</head>',$string);
   
echo $string;
?>
