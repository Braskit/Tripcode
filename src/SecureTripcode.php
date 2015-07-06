<?php

/*
 * Copyright (C) 2015 Frank Usrs
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Braskit\Tripcode;

/**
 * Create a secure tripcode.
 */
class SecureTripcode implements TripcodeInterface {
    /**
     * Default character set.
     *
     * @var string[]
     */
    const DEFAULT_CHARSET = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    /**
     * Character set.
     *
     * @var string[]
     */
    private $charset;

    /**
     * Length of output.
     *
     * @var integer
     */
    private $outputLength;

    /**
     * Number of rounds to hash.
     *
     * @var integer
     */
    private $rounds;

    /**
     * Salt.
     *
     * @var string
     */
    private $salt = '';

    /**
     * Constructor.
     *
     * @param string $salt The salt. This MUST be set to something long and
     *                     random in order to get unique outputs.
     * @param integer $rounds Number of rounds to hash the input.
     * @param integer $outputLength Length of output string.
     * @param string[] $charset Array of characters that can appear in output.
     */
    public function __construct(
        $salt = '',
        $rounds = 100000,
        $outputLength = 10,
        array $charset = self::DEFAULT_CHARSET
    ) {
        $this->salt = $salt;
        $this->rounds = $rounds;
        $this->outputLength = $outputLength;
        $this->charset = $charset;
    }

    /**
     * {@inheritdoc}
     *
     * @todo clear up this messy shit
     */
    public function hashKey($tripkey) {
        // settings
        $charset = $this->charset;
        $olen = $this->outputLength;
        $rounds = $this->rounds;
        $usersalt = $this->salt;

        // salt for salt argument (is this really necessary?)
        $sp1 = dechex(crc32($tripkey));
        $sp2 = substr($tripkey, 0, 4);

        $salt = str_pad(base64_encode($sp1.$sp2), 16, '=');

        $saltarg = '$6$rounds='.$rounds.'$'.$salt;

        // hash (sha512)
        $hash = crypt(base64_encode($usersalt.$tripkey), $saltarg);

        // remove salt/rounds shit, leaving the radix64-encoded hash
        $hash = preg_replace('/.*\$/', '', $hash);

        // convert from crypt's encoding to base64 which php recognises
        $hash = strtr($hash,
            './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
        );

        // decode hash
        $hash = base64_decode($hash, true);

        // verify that the decoding occured correctly
        if ($hash === false) {
            throw new \LogicException('holy shit');
        }

        $clen = count($this->charset);
        $hashbc = strlen($hash);

        // pad hash so its length becomes multiple of desired output length.
        if ($hashbc % $olen) {
            $hashbc = intval(ceil($hashbc / $olen) * $olen);

            $hash = str_pad($hash, $hashbc, $hash);
        }

        // i can't explain what's going on below, so have some dank memes
        // instead.
        $a = [];

        // DO IT
        for ($x = $r = $olen; $x--;) {
            $k = 0;

            for ($y = $hashbc / $olen; $y--;) {
                $k += ord($hash[$x + $r * $y]);
            }

            $a[] = $k % $clen;
        }

        $b = '';

        // JUST DO IT
        for ($i = $olen; $i--;) {
            $b .= $charset[$a[$i]];
        }

        return $b;
    }
}
