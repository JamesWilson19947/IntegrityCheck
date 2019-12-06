<?php 

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Integrity\IntegrityCheck;

$integrity = new IntegrityCheck();

$result = $integrity->CompareTwoDirectorys(
	'C:\example1',
	'C:\example2',
	true
);

$json = json_encode($result);

$result = $integrity->generateFileHashes('C:\github\scripts\cp', true);
$json = json_encode($result);

file_put_contents('version.json', print_r($json, true));

print_r($json);