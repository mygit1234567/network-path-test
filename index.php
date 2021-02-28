<?php

 /**
 * Network Path Test Program
 * Checks the network path according to the data file and network path provided by the user
 */

define("MESSAGE_TEXT_HEAD", "\n");
define("MESSAGE_TEXT_TAIL", "\n\n");
define("ERROR_TEXT_HEAD", "\n");
define("ERROR_TEXT_TAIL", "\n\n");
define("ERROR_TEXT_COLOR_HEAD", "ERROR :\033[93m ");
define("ERROR_TEXT_COLOR_TAIL", "\033[0m");
define("QUIT", "QUIT");
define("NOTFOUND", "Not Found!");

require_once 'functions.php';

/* (S) Start the program, get the data file path and check if file exist */

$result_is_file_exist = is_file_exist();

if ( $result_is_file_exist[0] != 'ok' ) {
    if ( $result_is_file_exist[0] == QUIT ) {
        echo bye();  
    }
    
    die( $result_is_file_exist[0] );
}

/* (E) Start the program, get the data file path and check if file exist*/

/* (S) Start the main loop, get the params from user and start network path test */

$file_path = $result_is_file_exist[1];
$continue_looping = true;

do {

    try {
        $result_is_parameters_ok = is_parameters_ok();

        if ( $result_is_parameters_ok[0] != 'ok') {
            if ( $result_is_parameters_ok[0] == QUIT ) {
                echo bye();
                $continue_looping = false;
            } else {
                echo $result_is_parameters_ok[0];
            }
        } else {  
            $device_from = $result_is_parameters_ok[1][0];
            $device_to = $result_is_parameters_ok[1][1];
            $latency = $result_is_parameters_ok[1][2];
    
            search( $file_path, $device_from, $device_to, $latency );
        }    
    } catch( Exception $exception ) {
        throw $exception;
        $continue_looping = false;
    }   


} while ( $continue_looping );

/* (E) Start the main loop, get the params from user and start network path test */
