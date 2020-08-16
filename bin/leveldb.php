<?php

/* default open options */
$options = array(
    'create_if_missing' => true,	// if the specified database didn't exist will create a new one
    'error_if_exists'	=> false,	// if the opened database exsits will throw exception
    'paranoid_checks'	=> false,
    'block_cache_size'	=> 8 * (2 << 20),
    'write_buffer_size' => 4<<20,
    'block_size'		=> 4096,
    'max_open_files'	=> 1000,
    'block_restart_interval' => 16,
    'compression'		=> LEVELDB_SNAPPY_COMPRESSION,
    'comparator'		=> NULL,   // any callable parameter which returns 0, -1, 1
);
/* default readoptions */
$readoptions = array(
    'verify_check_sum'	=> false,
    'fill_cache'		=> true,
    'snapshot'			=> null
);

/* default write options */
$writeoptions = array(
    'sync' => false
);

$db = new LevelDB(__DIR__."/../data/chainstate", $options, $readoptions, $writeoptions);

$it = new LevelDBIterator($db);

//const BLOCK_PREFIX = 42; // hex2ascii
//const TX_PREFIX = 43; // hex2ascii
//$r = $db->get(hex2bin("4300005e3e0b8d33487711c99ad03e54a51905c76fd6ac0d9fdd4c5db43161094b00"));//

$k = null;
$v = null;
$i = 0;
foreach($it as $key => $value) {
    if ($i++ == 10)
        break;
    $k = bin2hex($key);
    $v = bin2hex($value);
    printf("%s  :  %s\n", bin2hex($key), bin2hex($value));
}
