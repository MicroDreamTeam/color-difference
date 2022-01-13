<?php

namespace Itwmw\ColorDifference\Support;

use JetBrains\PhpStorm\Pure;

enum CIEIlluminant
{
    case A; //Incandescent/tungsten
    case B; //Old direct sunlight at noon
    case C; //Old daylight
    case D50; //ICC profile PCS
    case D55; //Mid-morning daylight
    case D65; //Daylight, sRGB, Adobe-RGB
    case D75; //North sky daylight
    case E; //Equal energy
    case F1; //Daylight Fluorescent
    case F2; //Cool fluorescent
    case F3; //White Fluorescent
    case F4; //Warm White Fluorescent
    case F5; //Daylight Fluorescent
    case F6; //Lite White Fluorescent
    case F7; //Daylight fluorescent, D65 simulator
    case F8; //Sylvania F40, D50 simulator
    case F9; //Cool White Fluorescent
    case F10; //Ultralume 50, Philips TL85
    case F11; //Ultralume 40, Philips TL84
    case F12; //Ultralume 30, Philips TL83

    public function x(bool $degree2 = true): float
    {
        if ($degree2) {
            return match ($this) {
                CIEIlluminant::A   => 109.850,
                CIEIlluminant::B   => 99.0927,
                CIEIlluminant::C   => 98.074,
                CIEIlluminant::D50 => 96.422,
                CIEIlluminant::D55 => 95.682,
                CIEIlluminant::D65 => 95.047,
                CIEIlluminant::D75 => 94.972,
                CIEIlluminant::E   => 100.000,
                CIEIlluminant::F1  => 92.834,
                CIEIlluminant::F2  => 99.187,
                CIEIlluminant::F3  => 103.754,
                CIEIlluminant::F4  => 109.147,
                CIEIlluminant::F5  => 90.872,
                CIEIlluminant::F6  => 97.309,
                CIEIlluminant::F7  => 95.044,
                CIEIlluminant::F8  => 96.413,
                CIEIlluminant::F9  => 100.365,
                CIEIlluminant::F10 => 96.174,
                CIEIlluminant::F11 => 100.966,
                CIEIlluminant::F12 => 108.046,
            };
        } else {
            return match ($this) {
                CIEIlluminant::A   => 111.144,
                CIEIlluminant::B   => 99.178,
                CIEIlluminant::C   => 97.285,
                CIEIlluminant::D50 => 96.720,
                CIEIlluminant::D55 => 95.799,
                CIEIlluminant::D65 => 94.811,
                CIEIlluminant::D75 => 94.416,
                CIEIlluminant::E   => 100.000,
                CIEIlluminant::F1  => 94.791,
                CIEIlluminant::F2  => 103.280,
                CIEIlluminant::F3  => 108.968,
                CIEIlluminant::F4  => 114.961,
                CIEIlluminant::F5  => 93.369,
                CIEIlluminant::F6  => 102.148,
                CIEIlluminant::F7  => 95.792,
                CIEIlluminant::F8  => 97.115,
                CIEIlluminant::F9  => 102.116,
                CIEIlluminant::F10 => 99.001,
                CIEIlluminant::F11 => 103.866,
                CIEIlluminant::F12 => 111.428,
            };
        }
    }

    public function y(): float
    {
        return 100.000;
    }

    #[Pure]
    public function z(bool $degree2 = true): float
    {
        if ($degree2) {
            return match ($this) {
                CIEIlluminant::A   => 35.585,
                CIEIlluminant::B   => 85.313,
                CIEIlluminant::C   => 118.232,
                CIEIlluminant::D50 => 82.521,
                CIEIlluminant::D55 => 92.149,
                CIEIlluminant::D65 => 108.883,
                CIEIlluminant::D75 => 122.638,
                CIEIlluminant::E   => 100.000,
                CIEIlluminant::F1  => 103.665,
                CIEIlluminant::F2  => 67.395,
                CIEIlluminant::F3  => 49.861,
                CIEIlluminant::F4  => 38.813,
                CIEIlluminant::F5  => 98.723,
                CIEIlluminant::F6  => 60.191,
                CIEIlluminant::F7  => 108.755,
                CIEIlluminant::F8  => 82.333,
                CIEIlluminant::F9  => 67.868,
                CIEIlluminant::F10 => 81.712,
                CIEIlluminant::F11 => 64.370,
                CIEIlluminant::F12 => 39.228,
            };
        } else {
            return match ($this) {
                CIEIlluminant::A   => 35.200,
                CIEIlluminant::B   => 84.3493,
                CIEIlluminant::C   => 116.145,
                CIEIlluminant::D50 => 81.427,
                CIEIlluminant::D55 => 90.926,
                CIEIlluminant::D65 => 107.304,
                CIEIlluminant::D75 => 120.641,
                CIEIlluminant::E   => 100.000,
                CIEIlluminant::F1  => 103.191,
                CIEIlluminant::F2  => 69.026,
                CIEIlluminant::F3  => 51.965,
                CIEIlluminant::F4  => 40.963,
                CIEIlluminant::F5  => 98.636,
                CIEIlluminant::F6  => 62.074,
                CIEIlluminant::F7  => 107.687,
                CIEIlluminant::F8  => 81.135,
                CIEIlluminant::F9  => 67.826,
                CIEIlluminant::F10 => 83.134,
                CIEIlluminant::F11 => 65.627,
                CIEIlluminant::F12 => 40.353,
            };
        }
    }
}
