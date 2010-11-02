#!/usr/bin/perl

use Chatbot::Eliza;

$jill = new Chatbot::Eliza("Jill", "");
$bob = new Chatbot::Eliza("Jill", "");


print "Jill: " . $jill->{initial}->[0] . "\n";
$message = $jill->{initial}->[0];

$true++;

while ($true) {
   $message = $bob->transform($message);
   print "Bob: " . "$message\n";
   
   $message = $jill->transform($message);
   print "Jill: " . "$message\n";
}

exit;