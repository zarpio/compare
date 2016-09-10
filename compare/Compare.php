<?php

/**
 * @author: Muhammad Khalil
 * @created_at May 22, 2016
 */
class Compare
{
    private $scanList;
    private $ignoreList;

    public function __construct()
    {

    }

    public function setScanList($scanList)
    {
        $this->scanList = $scanList;
    }

    public function getScanList()
    {
        return $this->scanList;
    }

    public function setIgnoreList($ignoreList)
    {
        $this->ignoreList = $ignoreList;
    }

    public function getIgnoreList()
    {
        return $this->ignoreList;
    }

    public function scanner()
    {
        $files = array('files' => array(), 'dirs' => array());
        $directories = array();

        foreach ($this->getScanList() as $row) {
            $last_letter = $row[strlen($row) - 1];
            $directories[] = ($last_letter == '\\' || $last_letter == '/') ? $row : $row . '/';
        }

        while (sizeof($directories)) {
            $dir = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {

                    // Set ignores from everywhere
                    if (in_array($file, unserialize(IGNORE_EVERYWHERE_LIST))) {
                        continue;
                    }

                    // Set ignore for dirs and files
                    if (in_array($dir . $file, $this->getIgnoreList())) {
                        continue;
                    }

                    $file = $dir . $file;
                    if (is_dir($file)) {
                        $directory_path = $file . '/';
                        array_push($directories, $directory_path);
                        $files['dirs'][] = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][md5($file)] = array(
                            'path' => $file,
                            'hash' => hash_file('md5', $file),
                        );

                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }

    function read_all_files($root = '.'){
        $files  = array('files'=>array(), 'dirs'=>array());
        $directories  = array();
        $last_letter  = $root[strlen($root)-1];
        $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;

        $directories[]  = $root;

        while (sizeof($directories)) {
            $dir  = array_pop($directories);
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file  = $dir.$file;
                    if (is_dir($file)) {
                        $directory_path = $file.DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][]  = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][]  = $file;
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }
}