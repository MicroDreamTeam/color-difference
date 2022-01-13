<?php

namespace Itwmw\ColorDifference\Tests;

use Itwmw\ColorDifference\Lib\Convert;
use Itwmw\ColorDifference\Lib\Lab;
use Itwmw\ColorDifference\Lib\RGB;
use Itwmw\ColorDifference\Lib\XYZ;
use PHPUnit\Framework\TestCase;

class TestColorConversion extends TestCase
{
    public function testRgb2xyz()
    {
        $rgb = new RGB(252, 26, 220);
        $xyz = Convert::rgb2xyz($rgb);
        $this->assertSame(53.4335, round($xyz->X, 4));
        $this->assertSame(26.6068, round($xyz->Y, 4));
        $this->assertSame(70.0178, round($xyz->Z, 4));

        $rgb = new RGB(15, 140, 0);
        $xyz = Convert::rgb2xyz($rgb);
        $this->assertSame(9.5745, round($xyz->X, 4));
        $this->assertSame(18.8565, round($xyz->Y, 4));
        $this->assertSame(3.1351, round($xyz->Z, 4));
    }

    public function testXyz2Lab()
    {
        $xyz = new XYZ(100, 60, 52);
        $lab = Convert::xyz2lab($xyz);

        $this->assertSame(81.8382, round($lab->L, 4));
        $this->assertSame(86.8222, round($lab->a, 4));
        $this->assertSame(12.3558, round($lab->b, 4));
    }

    public function testRgb2Lab()
    {
        $rgb = new RGB(252, 26, 220);
        $lab = Convert::rgb2lab($rgb);
        $this->assertSame(58.6086, round($lab->L, 4));
        $this->assertSame(91.0739, round($lab->a, 4));
        $this->assertSame(-43.9931, round($lab->b, 4));

        $rgb = new RGB(15, 140, 0);
        $lab = Convert::rgb2lab($rgb);
        $this->assertSame(50.5189, round($lab->L, 4));
        $this->assertSame(-54.0734, round($lab->a, 4));
        $this->assertSame(53.3881, round($lab->b, 4));
    }

    public function testLab2Lch()
    {
        $lab = new Lab(50, 100, 128);
        $lch = Convert::lab2Lch($lab);

        $this->assertSame(50.0, round($lch->L, 10));
        $this->assertSame(162.43152403397, round($lch->c, 10));
        $this->assertSame(52.001267557495, round($lch->h, 10));

        $lab = new Lab(154.21, 60, 1);
        $lch = Convert::lab2Lch($lab);

        $this->assertSame(154.21, round($lch->L, 10));
        $this->assertSame(60.00833275470, round($lch->c, 10));
        $this->assertSame(0.9548412538721, round($lch->h, 10));
    }
}
