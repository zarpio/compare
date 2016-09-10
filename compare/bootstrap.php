<?php
/******************* FOLLOWING CODE DOES NOT NEED TO BE CHANGED *******************/
require __DIR__ . '/config.php';
require __DIR__ . '/Helper.php';
require __DIR__ . '/Compare.php';
require __DIR__ . '/FTPClient.php';
require __DIR__ . '/Encrypt.php';

/** Validations checking if config is not set */
if(count(unserialize(SCAN_LIST)) <= 0)
    throw new Exception("Please update your configuration(compare/config.php)!");

/** Validations checking if config is not set */
if(!$remoteFile)
    throw new Exception("Please update your configuration(compare/config.php)!");

/** Validations checking if config is not set */
if(!$sourceFile)
    throw new Exception("Please update your configuration(compare/config.php)!");

// Remote Output
$remoteData = file_get_contents($remoteFile);
$remoteData = json_decode($remoteData, true);

$remoteFiles = [];
foreach ($remoteData['files'] as $row) {
    $remoteFiles[] = $row['path'];
}

// Remote Config
$remoteConfig = file_get_contents($remoteFile . '?json&request=config');
$remoteConfig = json_decode($remoteConfig, true);
// dump($remoteConfig);

// Assign remote config to source config variables
list($scanList, $ignoreList) = array_values($remoteConfig);

// Scan Source
$compare = new Compare();
$compare->setIgnoreList($ignoreList);
$compare->setScanList($scanList);
$sourceData = $compare->scanner();

$sourceFiles = [];
$files_matched = [];
$files_different = [];
$files_new_at_source = [];
$files_new_at_remote = [];
$counter = 0;
foreach ($sourceData['files'] as $key => $file) {
    $sourceFileKey = $key;
    $sourceFilePath = str_replace('./', '', $file['path']);
    $sourceFileHash = $file['hash'];
    $sourceFiles[] = $file['path'];

    if (isset($remoteData['files'][$sourceFileKey])) {
        if ($remoteData['files'][$sourceFileKey]['hash'] == $sourceFileHash) {
            $files_matched[$counter] = $sourceFilePath;
        } else {
            $files_different[$counter] = $sourceFilePath;
        }
        unset($remoteData['files'][$sourceFileKey]['hash']);
    } else {
        $files_new_at_source[$counter] = $sourceFilePath;
    }
    $counter++;
}

// d($files_matched);
// d($files_different);
// d($files_new_at_source);

// Get files exist on remote but not in source
$files_new_at_remote = array_diff($remoteFiles, $sourceFiles);
$files_new_at_remote = str_replace('./', '', $files_new_at_remote);

$final_output['different'] = $files_different;
$final_output['matched'] = $files_matched;
$final_output['new_at_source'] = $files_new_at_source;
$final_output['new_at_remote'] = $files_new_at_remote;
// d($final_output);


/**
 * Upload file to the remote
 */
if (isset($_POST['action']) && $_POST['action'] == 'upload') {
    $remoteUrl = $remoteConfig['remoteUrl'];
    $filePath = $_POST['file'];

    if (is_array($filePath)) {
        /** Upload multiple files */
        $ch = curl_init();

        $output = [];
        $errorFlag = true;

        foreach ($filePath as $path) {
            $file = new CURLFile($path, getMimeType($path), $path);
            $data = array('file' => $file, 'path' => $path, 'action' => 'upload', 'key' => KEY);

            curl_setopt($ch, CURLOPT_URL, $remoteUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $fp = fopen(dirname(__FILE__) . '/errorlog.txt', 'w');

            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);

            $response = curl_exec($ch);
            $response = json_decode($response);
            if (!$response->error) {
                $errorFlag = false;
            } else {
                $errorFlag = true;
            }
        }

        if ($errorFlag) {
            header('HTTP/1.1 500 Internal Server Error');
            //header('HTTP/1.1 401 Unauthorized', true, 401);
            header('Content-Type: application/json; charset=UTF-8');
            $output = json_encode(['response' => false, 'msg' => $response->msg, 'debug' => curl_error($ch)]);
        } else {
            header('Content-Type: application/json; charset=UTF-8');
            $output = json_encode(['response' => true, 'msg' => FILES_UPLOADED]);
        }
        curl_close($ch);

        echo $output;
        exit();
    } else {
        /** Upload a file */
        $ch = curl_init();

        $file = new CURLFile($filePath, getMimeType($filePath), $filePath);
        $data = array('file' => $file, 'path' => $filePath, 'action' => 'upload', 'key' => KEY);

        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $fp = fopen(dirname(__FILE__) . '/errorlog.txt', 'w');

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_STDERR, $fp);

        $response = curl_exec($ch);
        $response = json_decode($response);
        if (!$response->error) {
            header('Content-Type: application/json; charset=UTF-8');
            $output = json_encode(['response' => true, 'msg' => FILE_UPLOADED]);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            //header('HTTP/1.1 401 Unauthorized', true, 401);
            header('Content-Type: application/json; charset=UTF-8');
            $output = json_encode(['response' => false, 'msg' => $response->msg, 'debug' => curl_error($ch)]);
        }
        curl_close($ch);

        echo $output;
        exit();
    }
}

/**
 * Download file from the remote
 */
if (isset($_POST['action']) && $_POST['action'] == 'download') {

    //region Download file using Encryption Technique
    $output = [];
    $filename = $_POST['file'];
    $data = $_POST['encrypted_data'];

    /** Initialize Encryption Library */
    $crypt = new Encrypt(ENCRYPTION_KEY);

    if (is_array($data)) {
        foreach ($data as $file => $encryptedData) {
            /** Decode data */
            $data = base64_decode($encryptedData);

            /** Encrypt file contents */
            $decryptedData = $crypt->decrypt($data);

            if (makeDirs(dirname($file))) {
                /** Write to file */
                $content = $decryptedData;
                $fp = fopen($file, "wb");

                if (fwrite($fp, $content) === false) {
                    $output = ['response' => false, 'msg' => "Cannot write to file ($file)"];
                } else {
                    $output = ['response' => true, 'msg' => FILES_DOWNLOADED];
                }
            } else {
                $output = ['response' => false, 'msg' => "Cannot created path"];
            }

            fclose($fp);
        }
    } else {
        /** Decode data */
        $data = base64_decode($data);

        /** Encrypt file contents */
        $decryptedData = $crypt->decrypt($data);

        if (makeDirs(dirname($filename))) {
            /** Write to file */
            $content = $decryptedData;
            $fp = fopen($filename, "wb");

            if (fwrite($fp, $content) === false) {
                $output = ['response' => false, 'msg' => "Cannot write to file ($filename)"];
            } else {
                $output = ['response' => true, 'msg' => FILE_DOWNLOADED];
            }
        } else {
            $output = ['response' => false, 'msg' => "Cannot created path"];
        }

        fclose($fp);
    }

    echo json_encode($output);

    exit();
    //endregion


    //region Download file using FTP
    // *** Create the FTP object
//    $ftpObj = new FTPClient();
//    // *** Connect
//    if ($ftpObj -> connect(FTP_HOST, FTP_USER, FTP_PASS, PASSIVE_MODE)) {
//        // *** Then add FTP code here
//
//        $filePath = $_POST['file'];
//        $path = dirname($filePath);
//        $fileName = basename($filePath);
//        if (!file_exists($path)) {
//            mkdir($path, 0755, true);
//        }
//
//        // *** Change to folder
//        $ftpObj->changeDir($path);
//
//        $fileFrom = $fileName; # The location on the remote
//        $fileTo = $filePath; # Source dir to save to
//
//        // *** Download file
//        $ftpObj->downloadFile($fileFrom, $fileTo);
//
//        echo json_encode(['response' => true, 'msg' => 'File Downloaded']);
//    } else {
//        echo json_encode(['response' => false, 'msg' => $ftpObj -> getMessages()]);
//    }
//    exit();
    //endregion
}

if (isset($_POST['action']) && $_POST['action'] == 'delete-source') {
    $filesPath = $_POST['file'];

    $output = [];

    if (is_array($filesPath)) {
        $isUnlinked = false;
        $isExist = false;
        $somethingWrong = false;
        foreach ($filesPath as $file) {
            if (file_exists($file)) {
                if (unlink($file)) {
                    $isUnlinked = true;
                } else {
                    $somethingWrong = true;
                }
                $isExist = true;
            }
        }

        if ($isUnlinked) {
            $output = ['response' => true, 'msg' => FILES_DELETED];
        }
        if ($somethingWrong) {
            $output = ['response' => false, 'msg' => SOMETHING_WRONG];
        }
        if ($isExist == false) {
            $output = ['response' => false, 'msg' => FILE_NOT_PRESENT];
        }
    } else {
        if (file_exists($filesPath)) {
            if (unlink($filesPath)) {
                $output = ['response' => true, 'msg' => FILE_DELETED];
            } else {
                $output = ['response' => false, 'msg' => SOMETHING_WRONG];
            }
        } else {
            $output = ['response' => false, 'msg' => FILE_NOT_PRESENT];
        }
    }

    echo json_encode($output);
    exit();
}