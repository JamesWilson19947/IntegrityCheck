<?php

namespace Integrity;

class IntegrityCheck
{
    private $filesToIgnore = [];
    private $count = 0;

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
        $this->count = 0;
        $dir2 = $this->generateFileHashes($dir2, $verbose = true);
        return $this->compareArray($dir1, $dir2);
    }

    public function generateFileHashes($dir, $verbose = false)
    {
        $fileInfo = scandir($dir);
        $allFileLists = [];

        foreach ($fileInfo as $folder) {
            $this->count++;
            if ($folder !== '.' && $folder !== '..') {
                if (
                is_dir($dir . DIRECTORY_SEPARATOR . $folder) === true) {
                    $allFileLists[$dir . DIRECTORY_SEPARATOR . $folder] = $this->generateFileHashes($dir . DIRECTORY_SEPARATOR . $folder, $verbose);
                } else {
                    if (in_array($folder, $this->filesToIgnore)) {
                        continue;
                    }
                    $allFileLists[$folder] = md5_file($dir . DIRECTORY_SEPARATOR . $folder);
                }
            }
            if ($verbose) {
                if ($this->count % 100 == 0) {
                    echo 'Found:' . $this->count . ' files' . PHP_EOL;
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
