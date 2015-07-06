#!/usr/bin/env php
<?php

// needs PHP 7

use Braskit\Tripcode\SecureTripcode;

require '../../vendor/autoload.php';

const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

for ($x = 100; $x--;) {
    $key = base64_encode(random_bytes(random_int(2, 24)));

    $rounds = random_int(10000, 500000);
    $salt = random_int(0, 15) ? base64_encode(random_bytes(48)) : '';
    $outputLength = random_int(6, 16);
    $charset = substr(ALPHABET, 0, random_int(0, 8) ? random_int(16, 61) : 9001);

    $tripper = new SecureTripcode($salt, $rounds, $outputLength, str_split($charset));
    $trip = $tripper->hashKey($key);

    echo "$key;$trip;$salt;$rounds;$outputLength;$charset\n";
}
