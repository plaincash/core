#!/usr/bin/env php
<?php

use function PlainCash\Lib\KeyPair\publicKey;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__.'/../vendor/autoload.php';

$privateKey = bin2hex(random_bytes(32));
$publicKey = publicKey($privateKey);

echo "pub: {$privateKey}\n";
echo "prv: {$publicKey}\n";
