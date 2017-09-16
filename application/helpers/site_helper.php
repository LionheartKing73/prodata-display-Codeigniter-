<?php

    /**
     * @param $input
     * @param Boolean|true $exit
     */
    if ( !function_exists('debug') ) {
        function debug($arg, $exit = true) {
            //if ( !isset($_GET['debug']) ) return false;

            $bt =  debug_backtrace();
            $file = $bt[0]['file'];
            $line = $bt[0]['line'];
            $bt = null;

            echo '<div style="margin:10px auto;border:1px solid #bbb;background: #eee;padding: 10px;border-radius:5px;">';
            echo '<p style="font-size: 16px;font-weight: bold">';
            echo sprintf('File:: %s (Line:: %s)', $file, $line);
            echo '</p>';
            echo '<pre>';
            if(is_array($arg)) {
                print_r($arg);
            } elseif(is_string($arg)) {
                print $arg;
            } else {
                var_dump($arg);
            }
            echo '</pre></div>';
            if($exit) exit();
        }
    }

    /**
     * URL friendly Base 64 Encode
     *
     * @param  String $data
     * @return String
     */
    if ( !function_exists('base64url_encode') ) {
        function base64url_encode($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }
    }

    /**
     * Base-64 Decoder for URL friendly Encode string
     *
     * @param  String $data
     * @return String
     */
    if ( !function_exists('base64url_decode') ) {
        function base64url_decode($data) {
            return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
        }
    }

    /**
     * Generate a random UUID
     *
     * Warning: This method should not be used as a random seed for any cryptographic operations.
     * Instead you should use the openssl or mcrypt extensions.
     *
     * @see http://www.ietf.org/rfc/rfc4122.txt
     * @return string RFC 4122 UUID
     */
    if ( !function_exists('generate_uuid') ) {
        function generate_uuid()
        {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

                // 32 bits for "time_low"
                mt_rand(0, 65535), mt_rand(0, 65535),

                // 16 bits for "time_mid"
                mt_rand(0, 65535),

                // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
                mt_rand(0, 4095) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,

                // 48 bits for "node"
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
            );
        }
    }

    /**
     * Read CSV file and convert to associative array
     *
     * @return array
     */
    if ( !function_exists('csv_to_array') ) {
        function csv_to_array($filename='', $delimiter=',') {
            if(!file_exists($filename) || !is_readable($filename)) return false;

            $header = NULL;
            $data = array();

            if (($handle = fopen($filename, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    if(!$header) $header = $row;
                    else $data[] = array_combine($header, $row);
                }
                fclose($handle);
            }

            return $data;
        }
    }

    if(!function_exists('get_client_ip')){
        function get_client_ip() {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }
    }
