<?php
/**
 * Get Mime Type of a file
 */
if (!function_exists('getMimeType')) {
    function getMimeType($file)
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $output = finfo_file($fileInfo, $file);
        finfo_close($fileInfo);

        return $output;
    }
}

/**
 * Dump helper. Functions to dump variables to the screen, in a nicely formatted manner.
 * @author Muhammad Khalil
 * @version 2.0
 */
if (!function_exists('dump')) {
    function dump($var, $exit = false, $label = false, $echo = true)
    {

        // if (ENVIRONMENT != 'development') {
        //     return;
        // }

        // Store dump in variable
        ob_start();
        //var_dump( $var );
        print_r($var);
        $output = ob_get_clean();
        $label = $label ? $label . ' ' : '';

        // Location and line-number
        $line = '';
        $separator = "<p style='color:blue'>" . str_repeat("-", 100) . "</p>" . PHP_EOL;
        $caller = debug_backtrace();
        if (count($caller) > 0) {
            $tmp_r = $caller[0];
            $line .= "<p style='color:blue'>Location:</p> => <span style='color:red'>" . $tmp_r['file'] . '</span>';
            $line .= " (" . $tmp_r['line'] . ')';
        }

        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">'
            . $label
            . $line
            . PHP_EOL
            . $separator
            . $output
            . '</pre>';

        // Output
        if ($echo == true) {
            echo $output;

            if ($exit) {
                die();
            }
        } else {
            return $output;
        }
    }
}

if (!function_exists('url')) {
    function url()
    {
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            $_SERVER['REQUEST_URI']
        );
    }
}

if (!function_exists('isConnected')) {
    function isConnected()
    {
        $return = false;
        $connected = @fsockopen('www.google.com', 80);

        if ($connected) {
            $return = true;
            fclose($connected);
        }

        return $return;
    }
}

if (!function_exists('checkPermission')) {
    function checkPermission()
    {
//        dump(gethostname());
        switch (gethostname()) {
            case 'zarpio.local': // office mac
            case 'zarpio-mac.local': // home mac
            case 'Khalil': // home ubuntu
                $output = true;
                break;

            default:
                $output = false;
                break;
        }

        return $output;
    }
}

if (!function_exists('makeDirs')) {
    function makeDirs($dirPath, $mode = 0777)
    {
        return is_dir($dirPath) || mkdir($dirPath, $mode, true);
    }
}