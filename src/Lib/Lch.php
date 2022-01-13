<?php

namespace Itwmw\ColorDifference\Lib;

class Lch
{
    public function __construct(
        public float $L = 0,
        public float $c = 0,
        public float $h = 0,
    ) {
    }
}
