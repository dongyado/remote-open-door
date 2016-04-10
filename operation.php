<?php
include 'PhpSerial.php';

// Let's start the class
$serial = new PhpSerial;


if (php_sapi_name() == "cli")
    $action = trim( $argv[1] );
else 
    $action = trim($_GET['action']);

//$action = $argv[1];
// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
$serial->deviceSet("/dev/ttyACM0");

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(2);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen("w+");

sleep(2);
// To write into
$serial->sendMessage($action, 1);


file_put_contents('/tmp/ar.log', time(). " request \n", FILE_APPEND);
// Or to read from
$read = $serial->readPort(9600);
//echo strlen($read) ."\n";
//echo $read."\n===\n";


//for($i = 0; $i < strlen($read); $i++) {
//    echo(ord( $read[$i]  ));
//    echo "\n----\n";
//}

// If you want to change the configuration, the device must be closed
$serial->deviceClose();

// We can change the baud rate
//$serial->confBaudRate(9600);

// etc...
//
//
/* Notes from Jim :
> Also, one last thing that would be good to document, maybe in example.php:
>  The actual device to be opened caused me a lot of confusion, I was
> attempting to open a tty.* device on my system and was having no luck at
> all, until I found that I should actually be opening a cu.* device instead!
>  The following link was very helpful in figuring this out, my USB/Serial
> adapter (as most probably do) lacked DTR, so trying to use the tty.* device
> just caused the code to hang and never return, it took a lot of googling to
> realize what was going wrong and how to fix it.
>
> http://lists.apple.com/archives/darwin-dev/2009/Nov/msg00099.html

Riz comment : I've definately had a device that didn't work well when using cu., but worked fine with tty. Either way, a good thing to note and keep for reference when debugging.
 */
