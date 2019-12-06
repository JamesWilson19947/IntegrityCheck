<?php

namespace Integrity;

class IntegrityCheck
{
    private $filesToIgnore = [];

    public function __construct($filesToIgnore = [])
    {
        $this->filesToIgnore = $filesToIgnore;
    }

    public function CompareTwoDirectorys($dir1, $dir2, $verbose = false)
    {
        if ($verbose) {
            echo 'Searching '. $dir1 . PHP_EOL;
        }
        $dir1 = $this->generateFileHashes($dir1, $verbose = true);
        if ($verbose) {
            echo 'Searching '. $dir2 . PHP_EOL;
        }
        $dir2 = $this->generateFileHashes($dir2, $verbose = true);
        return $this->compareArray($dir1, $dir2);
    }

    public function generateFileHashes($dir, $verbose = false)
    {
        if(!is_dir($dir)){
            die("Directory does not exist");
        }

        $fileInfo = scandir($dir);
        $allFileLists = [];

        foreach ($fileInfo as $folder) {
            if ($folder !== '.' && $folder !== '..') {
                if (
                is_dir($dir . DIRECTORY_SEPARATOR . $folder) === true) {
                    $allFileLists[$folder] = $this->generateFileHashes($dir . DIRECTORY_SEPARATOR . $folder, $verbose);
                } else {
                    if (in_array($folder, $this->filesToIgnore)) {
                        continue;
                    }
                    $allFileLists[$folder] = md5_file($dir . DIRECTORY_SEPARATOR . $folder);
                }
            }
        }
        return $allFileLists;
    }

    public function compareArray($array1, $array2)
    {
        $result = array();

        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if (is_array($value)) {
                    $compareArray = $this->compareArray($value, $array2[$key]);
                    if (count($compareArray)) {
                        $result[$key] = $compareArray;
                    }
                } else {
                    if ($value != $array2[$key]) {
                        $result[$key] = $value;
                    }
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
