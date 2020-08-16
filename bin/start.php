#!/usr/bin/env php
<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$dotenv->required('NETWORK')->notEmpty();

$networks = require __DIR__.'/../config/networks.php';

if (!array_key_exists($_ENV['NETWORK'], $networks))
    throw new \InvalidArgumentException('Specified network not found in config');

