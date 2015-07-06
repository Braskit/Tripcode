<?php

namespace Braskit\Tripcode\Tests;

use Braskit\Tripcode\Tripcode;

class TripcodeTests extends \PHPUnit_Framework_TestCase {
    private $trip;

    public function setUp() {
        $this->trip = new Tripcode();
    }

    /**
     * @dataProvider tripcodeProvider
     */
    public function testDigestKey($key, $expectedHash) {
        $this->assertEquals($this->trip->hashKey($key), $expectedHash);
    }

    public function tripcodeProvider() {
        // some clowns' tripcodes
        return [
            ['c0sp4o', 'L8qRJlW3g2'],
            ['faggot', 'Ep8pui8Vw2'],
            ['mahousen', 'aeNZeP7XP2'],
            ['mahousensei', 'aeNZeP7XP2'],
            ['ubermicr', 'uKc.KnUlaI'],
            ['ubermicro', 'uKc.KnUlaI'],
        ];
    }
}
