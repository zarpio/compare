<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL);

/** Source Config */
$sourceFile = '';
$remoteFile = '';

/** Scan and Ignore List of dirs and files */
define('SCAN_LIST', serialize([
    '.', // root
    // 'controllers', // root
]));
define('IGNORE_LIST', serialize([
    './compare',
    './compare-source.php',
    './compare-remote.php',
]));
define('IGNORE_EVERYWHERE_LIST', serialize([
    '.',
    '..',
    'cgi-bin',
    '.DS_Store',
]));

/** Security Keys */
define('KEY', 'JFKJD438KJL');
// bin2hex(openssl_random_pseudo_bytes(32))
define('ENCRYPTION_KEY', 'b5bf4efbe4c1fa7361f5bd723dd95ed80b6fe27efd8e668f677b5f9a14170911');

/** Define all app titles at one place */
define('TOGGLE_TITLE', 'Toggle Selection of the files');
define('DOWNLOAD_TITLE', 'Download From Remote');
define('DOWNLOAD_ALL_TITLE', 'Download selected files from remote');
define('UPLOAD_TITLE', 'Upload To Remote');
define('UPLOAD_ALL_TITLE', 'Upload selected files to remote');
define('DELETE_SOURCE_TITLE', 'Remove file from source');
define('DELETE_ALL_SOURCE_TITLE', 'Remove selected files from source');
define('DELETE_REMOTE_TITLE', 'Remove file from remote');
define('DELETE_ALL_REMOTE_TITLE', 'Remove selected files from remote');
define('COPY_TITLE', 'Copy to clipboard');
define('KEY_ERROR', 'Security key does not matched.');
define('SOMETHING_WRONG', 'Something went wrong.');
define('COPIED_TITLE', 'File path has been copied to clipboard.');
define('FILE_UPLOADED', 'File has been uploaded successfully.');
define('FILES_UPLOADED', 'Selected files has been uploaded successfully.');
define('FILE_DOWNLOADED', 'File has been downloaded successfully.');
define('FILES_DOWNLOADED', 'Selected files has been downloaded successfully.');
define('FILE_DELETED', 'File has been deleted successfully.');
define('FILES_DELETED', 'Selected files has been deleted successfully.');
define('FILE_NOT_PRESENT', 'File does not seems to be present.');
define('FILES_NOT_PRESENT', 'Files do not seems to be present.');
