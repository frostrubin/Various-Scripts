#!/usr/bin/perl

use Chatbot::Eliza;

$bot = new Chatbot::Eliza("Jill", "");

$bot->command_interface();