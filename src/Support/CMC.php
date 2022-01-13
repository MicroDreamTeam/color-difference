<?php

namespace Itwmw\ColorDifference\Support;

enum CMC
{
    case Acceptability;
    case Imperceptibility;

    public function l(): int
    {
        return match ($this) {
            CMC::Acceptability    => 2,
            CMC::Imperceptibility => 1
        };
    }

    public function c(): int
    {
        return match ($this) {
            CMC::Acceptability, CMC::Imperceptibility => 1
        };
    }
}
