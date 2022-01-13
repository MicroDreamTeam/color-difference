<?php

namespace Itwmw\ColorDifference\Tests;

use Itwmw\ColorDifference\Color;
use Itwmw\ColorDifference\Lib\RGB;
use Itwmw\ColorDifference\Support\CIE94;
use Itwmw\ColorDifference\Support\CMC;
use PHPUnit\Framework\TestCase;

class TestColorDifference extends TestCase
{
    public function testDifference()
    {
        $color  = new Color(new RGB(255, 183, 255));
        $color2 = new Color(new RGB(55, 65, 53));

        $this->assertSame(59.040824, round($color->getDifferenceDin99($color2), 6));
        $this->assertSame(78.463853, round($color->getDifferenceCIE76($color2), 6));
        $this->assertSame(62.839302, round($color->getDifferenceCIE94($color2, CIE94::GraphicArts), 6));
        $this->assertSame(39.372464, round($color->getDifferenceCIE94($color2, CIE94::Textiles), 6));
        $this->assertSame(34.622627, round($color->getDifferenceCMC($color2, CMC::Acceptability), 6));
        $this->assertSame(49.683409, round($color->getDifferenceCMC($color2, CMC::Imperceptibility), 6));
        $this->assertSame(65.897927, round($color->getDifferenceCIEDE2000($color2), 6));
    }
}
