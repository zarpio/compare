<?php
/**
 * @author: Muhammad Khalil
 * @created_at May 22, 2016
 */
header('Access-Control-Allow-Origin: *');

/******************* FOLLOWING CODE DOES NOT NEED TO BE CHANGED *******************/
require 'compare/config.php';
require 'compare/contents.php';
require 'compare/Helper.php';
require 'compare/Compare.php';
require 'compare/Encrypt.php';

// Scan Remote
$compare = new Compare();
$compare->setScanList(unserialize(SCAN_LIST));
$compare->setIgnoreList(unserialize(IGNORE_LIST));

//region Upload file
if (isset($_FILES['file']['tmp_name']) && $_POST['action'] == 'upload') {

    if ($_POST['key'] == KEY) {
        $path = $_POST['path'];
        if (file_exists($path)) {
            unlink($path);
        }

        if (makeDirs(dirname($path))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
                echo json_encode(['error' => false, 'msg' => 'success']);
            }
        }

    } else {
        echo json_encode(['error' => true, 'msg' => KEY_ERROR]);
    }
    exit();
}
//endregion

//region Download file
if (isset($_POST['action']) && $_POST['action'] == 'download') {

    $output = [];
    $key = $_POST['key'];
    $filesPath = $_POST['file'];

    if (is_array($filesPath)) {

        if ($key == KEY) {
            $data = [];
            foreach ($filesPath as $file) {
                /** Read file */
                $handle = fopen($file, "r");
                $contents = fread($handle, filesize($file));

                $crypt = new Encrypt(ENCRYPTION_KEY);

                /** Encrypt file contents */
                $encryptedData = $crypt->encrypt($contents);
                $data[$file] = base64_encode($encryptedData);
            }

            $output = [
                'response' => true,
                'msg' => 'Sent you encrypted data',
                'data' => $data
            ];
        } else {
            $output = [
                'response' => false,
                'msg' => 'KEY does not matched',
                'data' => ''
            ];
        }

    } else {
        /** Read file */
        $handle = fopen($filesPath, "r");
        //dump($filesPath, 1);
        $contents = fread($handle, filesize($filesPath));

        if ($key == KEY) {
            $crypt = new Encrypt(ENCRYPTION_KEY);

            /** Encrypt file contents */
            $encryptedData = $crypt->encrypt($contents);

            $output = [
                'response' => true,
                'msg' => 'Sent you encrypted data',
                'data' => base64_encode($encryptedData)
            ];

        } else {
            $output = [
                'response' => false,
                'msg' => 'KEY does not matched',
                'data' => ''
            ];
        }
    }

    echo json_encode($output);
    exit();

    $filename = $_POST['file'];
    /** Read file */
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));

    if ($key == KEY) {
        $crypt = new Encrypt(ENCRYPTION_KEY);

        /** Encrypt file contents */
        $encryptedData = $crypt->encrypt($contents);

        echo json_encode(['response' => true, 'msg' => 'Sent you encrypted data', 'data' => base64_encode($encryptedData)]);
    } else {
        echo json_encode(['response' => false, 'msg' => KEY_ERROR]);
    }
    exit();
}
//endregion

//region Delete Remote File
if (isset($_POST['action']) && $_POST['action'] == 'delete-remote') {
    $filesPath = $_POST['file'];

    $output = [];

    if ($_POST['key'] == KEY) {
        if (is_array($filesPath)) {
            $returnFlag = false;
            foreach ($filesPath as $file) {
                if (file_exists($file)) {
                    if (unlink($file)) {
                        $returnFlag = true;
                    }
                }
            }

            if ($returnFlag) {
                header('Content-Type: application/json; charset=UTF-8');
                $output = ['response' => true, 'msg' => FILES_DELETED];
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                $output = ['response' => false, 'msg' => 'Something went wrong.'];
            }
        } else {
            if (file_exists($filesPath)) {
                if (unlink($filesPath)) {
                    header('Content-Type: application/json; charset=UTF-8');
                    $output = ['response' => true, 'msg' => FILE_DELETED];
                }
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                $output = ['response' => false, 'msg' => FILE_NOT_PRESENT];
            }
        }
    } else {
        header('HTTP/1.1 401 Unauthorized', true, 401);
        header('Content-Type: application/json; charset=UTF-8');
        $output = ['response' => false, 'msg' => KEY_ERROR];
    }

    echo json_encode($output);
    exit();
}
//endregion

//region Output Remote Config
if (isset($_GET['json']) && $_GET['request'] == 'config') {
    header('Content-type: application/json');
    echo json_encode(array(
        'scanlist' => $compare->getScanList(),
        'ignorelist' => $compare->getIgnoreList(),
        'remotePath' => __DIR__,
        'baseUrl' => "http://$_SERVER[SERVER_NAME]",
        'remoteUrlFull' => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
        'remoteUrl' => "http://" . $_SERVER["HTTP_HOST"] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    ));
    exit;
}
//endregion

echo json_encode($compare->scanner());
