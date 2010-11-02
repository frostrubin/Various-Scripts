#!/usr/bin/perl

use Chatbot::Eliza;

$bot = new Chatbot::Eliza("Jill", "");

print $bot->{initial}->[0] . "\n";

$true++;

while ($true) {
   print "You: ";
   $message = <STDIN>;
   
   $message = $bot->transform($message);
   print "$message\n";
}

exit;