#!/usr/bin/perl

#

use Mac::iTunes::Library::XML;

my $library = Mac::iTunes::Library::XML->parse( 'Lib.xml' );
print "This library has " . $library->num() . " items\n";
my $size = $library->size()/1024/1024/1024;
my $b = sprintf("%.2f", $size);
print "It has a size of " . $b . "GB\n";
my $sec = $library->time()/1000;
print "That results in a total playtime of " . int($sec/(24*60*60)) . " days and " . ($sec/(60*60))%24 ." hours\n";

