<?php
$pfad = "http://www.studentenwerk-mannheim.de/mensa/wo_dh_mm.normal.php"; 

$string = file_get_contents($pfad);

#$string = preg_replace('/<meta name="(.*?)\>/', "", $string);
#$string = preg_replace('/<!DOCTYPE (.*?)\>/', "", $string);
$string = preg_replace("/<table border='0' cellspacing='0' cellpadding='0' class='link_tab' summary='Wochenauswahl für die Menüanzeige'>(.*?)<\/table>/", "", $string);
$string = preg_replace ("/<th class='rechts links fort'(.*?)Buffet(.*?)<\/th>/", "", $string);
$string = str_replace("<font size='4'>Buffet:</font>&nbsp;&nbsp;<font size='2'>Pasta | Gemüse | Salat</font>&nbsp;<font size='1'>je 1000g 7,80 Euro</font>","",$string);
$string = str_replace("<h1>Mo - Fr 11:20 - 14:00 Uhr</h1>","",$string);
$string = str_replace("class='WoDate_mm'>","class='WoDate_mm'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$string);
$string = str_replace("color='white'","color='black'",$string);
$string = str_replace("size='4'","size='3'",$string);
$string = str_replace("class='inh_1a oben rechts'>","class='nodisplay'",$string);
$string = str_replace("cellpadding='4'","cellpadding='0'",$string);
$string = str_replace('</head>','<style type="text/css">
* {font-size: 7pt; font-weight: normal; background-color: #282828; color: white;}
h1 {font-size: 12pt;}
#WoTab1_mm {margin-top:-16px;}

#WoTab2_mm,
#WoAktion_mm,
#LogoWoTab_mm {
  display:none;
} 
.zusatz_mm,
.klausel,
.label,
.Titel_mm,
.HSName_mm,
.bo_re,
.fort,
.dickl,
.zusatz_mas,
.nodisplay {
  display:none;
}
td.rechts.oben {
width: 30px;
margin: 0px;

}

</style>
</head>',$string);
	$string = str_replace("<h1>Freitag</h1>","<h2>&nbsp;&nbsp;&nbsp;Fr.</h2>",$string);
	$string = str_replace("<h1>Donnerstag</h1>","<h2>&nbsp;&nbsp;&nbsp;Do.</h2>",$string);
	$string = str_replace("<h1>Mittwoch</h1>","<h2>&nbsp;&nbsp;&nbsp;Mi.</h2>",$string);
	$string = str_replace("<h1>Dienstag</h1>","<h2>&nbsp;&nbsp;&nbsp;Di.</h2>",$string);
	$string = str_replace("<h1>Montag</h1>","<h2>&nbsp;&nbsp;&nbsp;Mo.</h2>",$string);



if (date('l')=="Friday") {
	$string = str_replace("<h2>&nbsp;&nbsp;&nbsp;Fr.</h2>","<h2><font size='3'>&#9658;&nbsp;</font>Fr.</h2>",$string);
}
elseif (date('l')=="Thursday") {
	$string = str_replace("<h2>&nbsp;&nbsp;&nbsp;Do.</h2>","<h2><font size='3'>&#9658;&nbsp;</font>Do.</h2>",$string);
}
elseif (date('l')=="Wednesday") {
	$string = str_replace("<h2>&nbsp;&nbsp;&nbsp;Mi.</h2>","<h2><font size='1'>&#9658;&nbsp;</font>Mi.</h2>",$string);
}
elseif (date('l')=="Tuesday") {
	$string = str_replace("<h2>&nbsp;&nbsp;&nbsp;Di.</h2>","<h2><font size='3'>&#9658;&nbsp;</font>Di.</h2>",$string);
}
elseif (date('l')=="Monday") {
	$string = str_replace("<h2>&nbsp;&nbsp;&nbsp;Mo.</h2>","<h2><font size='3'>&#9658;&nbsp;</font>Mo.</h2>",$string);
}
else {
	$string = preg_replace('/<body>[\S|\s]*?<\/body>/','<body></body>',$string);
}

echo $string;
?>
