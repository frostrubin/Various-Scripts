#!/usr/bin/env php
<?php

  $datei = file('tx.txt');
  foreach ($datei as &$value) {
    $value = ereg_replace("\n", "", $value);
    $value = ereg_replace("\r", "", $value);
    $i = 0;
    while ($i < 9) {
      $i = $i + 1;
      echo $value;
      echo 0;
      echo $i;
      echo '.pdf';
      echo "\n";
    }
    
    
    while ($i <= 22) {
      $i = $i + 1;
      echo $value;
      echo $i;
      echo '.pdf';
      echo "\n";
    }
  }

?>