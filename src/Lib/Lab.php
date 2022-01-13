<?php

namespace Itwmw\ColorDifference\Lib;

class Lab
{
    public function __construct(
        public float $L = 0,
        public float $a = 0,
        public float $b = 0
    ) {
    }
}
