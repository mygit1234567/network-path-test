<?php

 /**
 * A Function to check if data file exist
 * @param string $file_full_path file path to check
 * @return string retuns $result, if $file_full_path exists $result is set to 'ok' otherwise $result is set to an error message
 */
function is_file_exist() {
    echo MESSAGE_TEXT_HEAD . "Network Path Test Progam\n(Type '" . QUIT . "' to exit the program)" . MESSAGE_TEXT_TAIL;
    $file_path = trim(readline('Please enter the data file name (.csv) : '));

    if ( strtoupper($file_path) == QUIT ) {
        $result[0] = QUIT;
    } else {        
        $result[0] = ERROR_TEXT_HEAD . ERROR_TEXT_COLOR_HEAD . "File not found! Please check the file path" . ERROR_TEXT_TAIL . ERROR_TEXT_COLOR_TAIL;

        if ( $file_path == '' ) {
            $result[0] = ERROR_TEXT_HEAD . ERROR_TEXT_COLOR_HEAD . "File path can not be empty! Please enter the full file path" . ERROR_TEXT_TAIL . ERROR_TEXT_COLOR_TAIL;
        } 
        
        if ( file_exists($file_path) ) {
            $result[0] = 'ok';
            $result[1] = $file_path;
        }       
    }

    return $result;
}

 /**
 * A Function to return a message after user type 'QUIT'
 */
function bye() {
    return MESSAGE_TEXT_HEAD . 'bye' . MESSAGE_TEXT_TAIL;
}

 /**
 * A Function to check console parameters
 * @param string $parameters console parameters
 * @return string retuns $result, if parameters are okay $returns is set to 'ok', otherwise it is set to an error message
 */
function is_parameters_ok() {
    $parameters = readline(MESSAGE_TEXT_HEAD . MESSAGE_TEXT_HEAD . 'Enter [Device from] [Device to] [Latency] : ' . MESSAGE_TEXT_TAIL);

    $result[0] = '';
    $parameters = trim( $parameters );

    if ( $parameters == '' ) {
        $result[0] = ERROR_TEXT_HEAD . ERROR_TEXT_COLOR_HEAD . "Empty Parameters! Please enter the parameters" . ERROR_TEXT_TAIL . ERROR_TEXT_COLOR_TAIL;
    } else {
        if ( strtoupper($parameters) == QUIT ) {
            $result[0] = QUIT;
        } else {
            $result_parameters = explode( ' ', $parameters );

            $parameters_count = count($result_parameters);
    
            if ( ($parameters_count > 0) && ($parameters_count < 3) ) {
                $result[0] = ERROR_TEXT_HEAD . ERROR_TEXT_COLOR_HEAD . "Missing Parameters! Please check the parameters" . ERROR_TEXT_TAIL . ERROR_TEXT_COLOR_TAIL;
            } 
                
            if ( ($parameters_count > 0) && ($parameters_count > 3) ) {
               $result[0] = ERROR_TEXT_HEAD . ERROR_TEXT_COLOR_HEAD . "Too Many Parameters! Please check the parameters" . ERROR_TEXT_TAIL . ERROR_TEXT_COLOR_TAIL;
            }     
            
            if ( $result[0] == '' ) {
                $result[0] = 'ok';
                $result[1] = $result_parameters;
            }        
        }
    }

    return $result;
}

 /**
 * A Function to print the search results to the console
 * @param string $search_result
 */
function print_to_console( $search_result ) {
    if ( $search_result[0] != NOTFOUND ) {
        $result = '';
        $i = 0;
        $found = $search_result[0];
        $total_latency = $search_result[1];
        $array_reversed = $search_result[2];
        $array_size = count( $search_result[3] );
        $result_array = $search_result[3];

        if ( $array_size > 0 ) {
            $array_size = $array_size - 1;

            for ( $i = 0; $i <= $array_size; $i++ ) {
                if ( $i == 0 ) {
                    $result = $result_array[$i][0] . ' => ' . $result_array[$i][1];
                }
    
                if ( $i == $array_size ) {
                    if ( $array_size > 0 ) {
                        $result = $result . ' => ' . $result_array[$i][1];
                    }
                }
    
                if ( ($i>0) && ($i < $array_size) ) {
                    $result = $result . ' => ' . $result_array[$i][1];                
                }
            }
    
            if ( $array_reversed == 'true' ) {
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
    
        $result = "\nRESULT : " . $result . ' => ' . $total_latency . "\n";
    
        echo $result. "\n";
    } else {
        echo ERROR_TEXT_HEAD . "RESULT : " . NOTFOUND . ERROR_TEXT_TAIL;
    }
    
}

 /**
 * A Function to search the data file for network paths
 * @param string $data_file_path, $device_from, $device_to, $latency console parameters
 * @return string retuns $result array with search result and information parameters
 */
function search( $data_file_path, $device_from, $device_to, $latency ) {     
    try {    
        $lines = file( $data_file_path, FILE_IGNORE_NEW_LINES );

        $array_reversed = false;

        if ( $device_from > $device_to ) {
            $array_reversed = true;

            $temp = $device_from;
            $device_from = $device_to;
            $device_to = $temp;
        }

        $device_from_indexes = [];
        $i = 0;

        foreach ( $lines as $key => $value ) {
            $values = explode(',', $value);

            if ( $device_from == $values[0] ) {
                $device_from_indexes[] = $i;
            }
            $i++;
        }

        $found = false;
        $local_device_to = $device_to;
        $local_latency = $latency;

        foreach ( $device_from_indexes as $key => $device_index ) {
            if ( !$found ) {
                $path = Array();
                $local_device_from = $device_from;
                $total_latency = 0;
                $device_index_should_match = true;

                for ( $x = 0; $x <= count($lines) - 1; $x++ ) {
                    if ( $device_index_should_match ) {
                        if ( $x != $device_index ) {
                            continue;
                        }
                    }
                    
                    $device_index_should_match = false;

                    $value = $lines[$x];
                    $values = explode(',', $value);
    
                    $line_device_from = $values[0];
                    $line_device_to = $values[1];
                    $line_latency = $values[2];
                        
    
                    if ( $local_device_from == $line_device_from ) {
                        $total_latency = $total_latency + $line_latency; 
                        $path[] = [$line_device_from, $line_device_to];    
                        $local_device_from = $line_device_to;

                        if ( ($local_device_to == $line_device_to) && ($total_latency <= $local_latency) ) {
                            $found = true;
                        }
                    }
    
                    if ( $found ) {
                        break;
                    }  
                }
            }
        }     
        
        $result_path = Array();

        if ( !$found ) {
            $path = [];
            $result_path[0] = NOTFOUND;
            $total_latency = 0;
        } else {
            $result_path[0] = 'found';
            $result_path[1] = $total_latency;
            $result_path[2] = $array_reversed ? 'true' : 'false';
            $result_path[3] = $path;
        }

        print_to_console( $result_path );
        
    } catch( Exception $exception ) {
        throw $exception;
    }   
}