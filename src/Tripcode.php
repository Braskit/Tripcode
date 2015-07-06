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

class Tripcode implements TripcodeInterface {
    /**
     * {@inheritdoc}
     */
    public function hashKey($tripkey) {
        if (extension_loaded('mbstring')) {
            $tripkey = mb_convert_encoding($tripkey, 'UTF-8', 'SJIS');
        }

        $salt = substr($tripkey.'H..', 1, 2);

        $salt = preg_replace('/[^\.-z]/', '.', $salt);
        
        $salt = strtr($salt, ':;<=>?@[\\]^_`', 'ABCDEFGabcdef');

        $tripcode = substr(crypt($tripkey, $salt), -10);

        return $tripcode;
    }
}
