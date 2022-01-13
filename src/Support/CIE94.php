<?php

namespace Itwmw\ColorDifference\Support;

enum CIE94{
    case GraphicArts;
    case Textiles;

    public function kl(): int
    {
        return match ($this) {
            CIE94::GraphicArts => 1,
            CIE94::Textiles    => 2,
        };
    }

    public function k1(): float
    {
        return match ($this) {
            CIE94::GraphicArts => 0.045,
            CIE94::Textiles    => 0.048,
        };
    }

    public function k2(): float
    {
        return match ($this) {
            CIE94::GraphicArts => 0.015,
            CIE94::Textiles    => 0.014,
        };
    }
}
