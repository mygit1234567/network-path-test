<?php

function get_the_input( $prompt_text ) {
    $input = readline($prompt_text . ' : ');
    return $input;
}

function printToTheScreen($array = Array(), $array_reversed, $total_latency) {
    $result = '';
    $i = 0;
    $array_size = count( $array ) - 1;

    
    if ( $array_size > 0 ) {
        for ( $i = 0; $i <= $array_size; $i++ ) {
            if ( $i == 0 ) {
                $result = $array[$i][0] . ' => ' . $array[$i][1];
            }

            if ( $i == $array_size ) {
                $result = $result . ' => ' . $array[$i][1];
            }

            if ( ($i>0) && ($i < $array_size) ) {
                $result = $result . ' => ' . $array[$i][1];                
            }
        }

        if ( $array_reversed ) {
            $result_temp = '';
            $values = explode(' => ', $result);
            for ( $i = count($values) - 1; $i >= 0; $i-- ) {
                if ( $result_temp == '' ) {
                    $result_temp = $values[$i];
                } else {
                    $result_temp = $result_temp . ' => ' . $values[$i];
                }

            }

            $result = $result_temp;
        }

    }

    $result = $result . ' => ' . $total_latency;

    echo $result. "\n";
}

function run( $data_file_path, $device_from, $device_to, $latency ) {     
    try {
        $device_from_indexes = [];
        $i = 0;
    
        $lines = file( $data_file_path, FILE_IGNORE_NEW_LINES );

        $array_reversed = false;
        if ( $device_from > $device_to ) {
            $array_reversed = true;

            $temp = $device_from;
            $device_from = $device_to;
            $device_to = $temp;
        }

        foreach ( $lines as $key => $value ) {
            $values = explode(',', $value);

            if ( $device_from == $values[0] ) {
                $device_from_indexes[] = $i;
            }
            $i++;
        }

        $path = Array();
        $i = 0;
        $total_latency = 0;
        $found = false;

        $local_device_to = $device_to;
        $local_latency = $latency;

        foreach ( $device_from_indexes as $key => $device_index ) {
            if ( !$found ) {
                $path = Array();
                $local_device_from = $device_from;
                $total_latency = 0;

                for ( $x = $device_index; $x <= count($lines) -1; $x++ ) {
                    $value = $lines[$x];
                    $values = explode(',', $value);

                    $line_device_from = $values[0];
                    $line_device_to = $values[1];
                    $line_latency = $values[2];
                    
                    if ( ($local_device_from == $line_device_from) && ($local_device_to == $line_device_to) && (($total_latency + $line_latency) <= $local_latency) ) {
                        $found = true;
                    }

                    if ( $local_device_from == $line_device_from ) {
                        $total_latency = $total_latency + $line_latency;
                        $path[] = [$line_device_from, $line_device_to];    
                        $local_device_from = $line_device_to;
                    }

                    if ( $found ) {
                        break;
                    }   
                }
            }
        }      

        if ( !$found ) {
            empty( $path );
            $total_latency = 0;
        }

        printToTheScreen( $path, $array_reversed, $total_latency );
    } catch( Exception $exception ) {
        throw $exception;
    }   
}

function start( $stop = false, $process = true ) { 
    $csv_file_path = get_the_input('Enter .csv File Path');

    if ( strtoupper($csv_file_path) != 'QUIT') {
        do {
            $user_input = get_the_input('Enter [Device From] [Device To] [Latency]');
            $parameters = explode(' ', $user_input);

            if ( strtoupper($parameters[0]) == 'QUIT') {
                $stop = true;
            } else {
                run( $csv_file_path, $parameters[0], $parameters[1], $parameters[2] );
            }
        } while (!$stop);
    }
}