<?php

namespace Itwmw\ColorDifference\Support;

use JetBrains\PhpStorm\Pure;

enum ReferenceWhite
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
                ReferenceWhite::A   => 109.850,
                ReferenceWhite::B   => 99.0927,
                ReferenceWhite::C   => 98.074,
                ReferenceWhite::D50 => 96.422,
                ReferenceWhite::D55 => 95.682,
                ReferenceWhite::D65 => 95.047,
                ReferenceWhite::D75 => 94.972,
                ReferenceWhite::E   => 100.000,
                ReferenceWhite::F1  => 92.834,
                ReferenceWhite::F2  => 99.187,
                ReferenceWhite::F3  => 103.754,
                ReferenceWhite::F4  => 109.147,
                ReferenceWhite::F5  => 90.872,
                ReferenceWhite::F6  => 97.309,
                ReferenceWhite::F7  => 95.044,
                ReferenceWhite::F8  => 96.413,
                ReferenceWhite::F9  => 100.365,
                ReferenceWhite::F10 => 96.174,
                ReferenceWhite::F11 => 100.966,
                ReferenceWhite::F12 => 108.046,
            };
        } else {
            return match ($this) {
                ReferenceWhite::A   => 111.144,
                ReferenceWhite::B   => 99.178,
                ReferenceWhite::C   => 97.285,
                ReferenceWhite::D50 => 96.720,
                ReferenceWhite::D55 => 95.799,
                ReferenceWhite::D65 => 94.811,
                ReferenceWhite::D75 => 94.416,
                ReferenceWhite::E   => 100.000,
                ReferenceWhite::F1  => 94.791,
                ReferenceWhite::F2  => 103.280,
                ReferenceWhite::F3  => 108.968,
                ReferenceWhite::F4  => 114.961,
                ReferenceWhite::F5  => 93.369,
                ReferenceWhite::F6  => 102.148,
                ReferenceWhite::F7  => 95.792,
                ReferenceWhite::F8  => 97.115,
                ReferenceWhite::F9  => 102.116,
                ReferenceWhite::F10 => 99.001,
                ReferenceWhite::F11 => 103.866,
                ReferenceWhite::F12 => 111.428,
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
                ReferenceWhite::A   => 35.585,
                ReferenceWhite::B   => 85.313,
                ReferenceWhite::C   => 118.232,
                ReferenceWhite::D50 => 82.521,
                ReferenceWhite::D55 => 92.149,
                ReferenceWhite::D65 => 108.883,
                ReferenceWhite::D75 => 122.638,
                ReferenceWhite::E   => 100.000,
                ReferenceWhite::F1  => 103.665,
                ReferenceWhite::F2  => 67.395,
                ReferenceWhite::F3  => 49.861,
                ReferenceWhite::F4  => 38.813,
                ReferenceWhite::F5  => 98.723,
                ReferenceWhite::F6  => 60.191,
                ReferenceWhite::F7  => 108.755,
                ReferenceWhite::F8  => 82.333,
                ReferenceWhite::F9  => 67.868,
                ReferenceWhite::F10 => 81.712,
                ReferenceWhite::F11 => 64.370,
                ReferenceWhite::F12 => 39.228,
            };
        } else {
            return match ($this) {
                ReferenceWhite::A   => 35.200,
                ReferenceWhite::B   => 84.3493,
                ReferenceWhite::C   => 116.145,
                ReferenceWhite::D50 => 81.427,
                ReferenceWhite::D55 => 90.926,
                ReferenceWhite::D65 => 107.304,
                ReferenceWhite::D75 => 120.641,
                ReferenceWhite::E   => 100.000,
                ReferenceWhite::F1  => 103.191,
                ReferenceWhite::F2  => 69.026,
                ReferenceWhite::F3  => 51.965,
                ReferenceWhite::F4  => 40.963,
                ReferenceWhite::F5  => 98.636,
                ReferenceWhite::F6  => 62.074,
                ReferenceWhite::F7  => 107.687,
                ReferenceWhite::F8  => 81.135,
                ReferenceWhite::F9  => 67.826,
                ReferenceWhite::F10 => 83.134,
                ReferenceWhite::F11 => 65.627,
                ReferenceWhite::F12 => 40.353,
            };
        }
    }
}
