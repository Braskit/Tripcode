<?php

namespace Braskit\Tripcode\Tests;

use Braskit\Tripcode\SecureTripcode;

class SecureTripcodeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider secureTripcodeProvider
     */
    public function testSecureTripcode($key, $hash, $salt, $rounds, $len, $charset) {
        $tripper = new SecureTripcode($salt, $rounds, $len, str_split($charset));

        $this->assertEquals($tripper->hashKey($key), $hash);
    }

    public function secureTripcodeProvider() {
        foreach (file(__DIR__.'/securetrip_data.txt') as $line) {
            $line = trim($line);

            yield explode(';', $line);
        }
    }
}
