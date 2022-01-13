<?php

namespace Itwmw\ColorDifference\Lib;

use Itwmw\ColorDifference\Support\CIEIlluminant;
use Itwmw\ColorDifference\Support\RGBSpace;
use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;

class Convert
{
    #[Pure]
    public static function rgb2lab(
        RGB $color,
        #[ExpectedValues(valuesFromClass: CIEIlluminant::class)]
        CIEIlluminant $illuminant = CIEIlluminant::D65,
        #[ExpectedValues(valuesFromClass: RGBSpace::class)]
        RGBSpace $RGBSpace = RGBSpace::D65_sRGB
    ): Lab {
        return self::xyz2lab(self::rgb2xyz($color, $RGBSpace), $illuminant);
    }

    #[Pure]
    public static function rgb2xyz(
        RGB $color,
        #[ExpectedValues(valuesFromClass: RGBSpace::class)]
        RGBSpace $RGBSpace = RGBSpace::D65_sRGB
    ): XYZ {
        $R = ($color->R / 255);
        $G = ($color->G / 255);
        $B = ($color->B / 255);

        if ($R > 0.04045) {
            $R = pow((($R + 0.055) / 1.055), 2.4);
        } else {
            $R = $R / 12.92;
        }
        if ($G > 0.04045) {
            $G = pow((($G + 0.055) / 1.055), 2.4);
        } else {
            $G = $G / 12.92;
        }
        if ($B > 0.04045) {
            $B = pow((($B + 0.055) / 1.055), 2.4);
        } else {
            $B = $B / 12.92;
        }

        $R *= 100;
        $G *= 100;
        $B *= 100;
        $space = $RGBSpace->rgb2Xyz();

        $xyz    = new XYZ();
        $xyz->X = $R * $space[0][0] + $G * $space[0][1] + $B * $space[0][2];
        $xyz->Y = $R * $space[1][0] + $G * $space[1][1] + $B * $space[1][2];
        $xyz->Z = $R * $space[2][0] + $G * $space[2][1] + $B * $space[2][2];
        return $xyz;
    }

    #[Pure]
    public static function xyz2Rgb(
        XYZ $color,
        #[ExpectedValues(valuesFromClass: RGBSpace::class)]
        RGBSpace $RGBSpace = RGBSpace::D65_sRGB
    ): RGB {
        // Normalizing for RGB
        $x = $color->X / 100;
        $y = $color->Y / 100;
        $z = $color->Z / 100;

        // xyz is multiplied by the reverse transformation matrix to linear rgb
        $space = $RGBSpace->xyz2Rgb();
        $invR  = $space[0][0] * $x + $space[0][1] * $y + $space[0][2] * $z;
        $invG  = $space[1][0] * $x + $space[1][1] * $y + $space[1][2] * $z;
        $invB  = $space[2][0] * $x + $space[2][1] * $y + $space[2][2] * $z;

        // Linear rgb must be gamma corrected to normalized srgb. Gamma correction
        // is linear for values <= 0.0031308 to avoid infinite log slope near zero
        $compand = fn ($c) => $c <= 0.0031308 ? 12.92 * $c : 1.055 * pow($c, 1 / 2.4) - 0.055;
        $cR      = $compand($invR);
        $cG      = $compand($invG);
        $cB      = $compand($invB);

        // srgb is scaled to [0,255]
        // Add zero to prevent signed zeros (force 0 rather than -0)
        $rgb    = new RGB();
        $rgb->R = round($cR * 255);
        $rgb->G = round($cG * 255);
        $rgb->B = round($cB * 255);
        return $rgb;
    }

    #[Pure]
    public static function xyz2lab(
        XYZ $color,
        #[ExpectedValues(valuesFromClass: CIEIlluminant::class)]
        CIEIlluminant $illuminant = CIEIlluminant::D65
    ): Lab {
        // compute xyz, which is XYZ scaled relative to reference white
        $Y = $color->Y / $illuminant->y();
        $Z = $color->Z / $illuminant->z();
        $X = $color->X / $illuminant->x();

        // from CIE standard, which now defines these as a rational fraction
        if ($X > 0.008856) {
            $X = pow($X, 1 / 3);
        } else {
            $X = (7.787 * $X) / (16 / 116);
        }
        if ($Y > 0.008856) {
            $Y = pow($Y, 1 / 3);
        } else {
            $Y = (7.787 * $Y) / (16 / 116);
        }
        if ($Z > 0.008856) {
            $Z = pow($Z, 1 / 3);
        } else {
            $Z = (7.787 * $Z) / (16 / 116);
        }

        $Lab    = new Lab();
        $Lab->L = (116 * $Y) - 16;
        $Lab->a = 500 * ($X - $Y);
        $Lab->b = 200 * ($Y - $Z);
        return $Lab;
    }

    #[Pure]
    public static function lab2Xyz(
        Lab $color,
        #[ExpectedValues(valuesFromClass: CIEIlluminant::class)]
        CIEIlluminant $illuminant = CIEIlluminant::D65
    ): XYZ {
        list($L, $a, $b) = array_values(get_object_vars($color));

        $kap = 24389 / 27;   // 29^3/3^3
        $eps = 216   / 24389;  // 6^3/29^3

        // compute f, starting with the luminance-related term
        $fY = ($L + 16) / 116;
        $fZ = ($fY - $b / 200);
        $fX = $a / 500 + $fY;

        // compute xyz
        $xR = pow($fX, 3) > $eps ? pow($fX, 3) : (116 * $fX - 16)      / $kap;
        $yR = $L          > $kap * $eps ? pow(($L + 16) / 116, 3) : $L / $kap;
        $zR = pow($fZ, 3) > $eps ? pow($fZ, 3) : (116 * $fZ - 16)      / $kap;

        // Normalizing for relative luminance
        $xyz    = new XYZ();
        $xyz->X = $xR * $illuminant->x();
        $xyz->Y = $yR * $illuminant->y();
        $xyz->Z = $zR * $illuminant->z();
        return $xyz;
    }

    #[Pure]
    public static function lab2Lch(Lab $lab): Lch
    {
        // Chroma
        $C = sqrt($lab->a ** 2 + $lab->b ** 2);

        // Convert to polar form
        $h = self::CieLab2Hue($lab->a, $lab->b);

        // L is still L
        return new Lch($lab->L, $C, $h);
    }

    private static function CieLab2Hue($a, $b): float|int
    {
        $var_bias = 0;
        if ($a >= 0 && 0 == $b) {
            return 0;
        }
        if ($a < 0 && 0 == $b) {
            return 180;
        }
        if (0 == $a && $b > 0) {
            return 90;
        }
        if (0 == $a && $b < 0) {
            return 270;
        }
        if ($a < 0) {
            $var_bias = 180;
        }
        if ($a > 0 && $b < 0) {
            $var_bias = 360;
        }
        return (rad2deg(atan2($b, $a)) + $var_bias);
    }
    
    #[Pure]
    public static function lab2Din99(Lab $lab): Din99
    {
        // Brightness transformation
        $L99 = 105.51 * log(1 + 0.0158 * $lab->L);

        if (0 == round($lab->a) && 0 == round($lab->b)) {
            $a99 = 0;
            $b99 = 0;
        } else {
            $cos16 = cos(deg2rad(16));
            $sin16 = sin(deg2rad(16));
            // Redness values (red-green axis)
            $e = $lab->a * $cos16 + $lab->b * $sin16;
            // Yellowness value f (yellow-blue axis)
            $f = 0.7 * (-$lab->a * $sin16 + $lab->b * $cos16);
            // From this, the chroma value G (chroma) is calculated:
            $G = sqrt($e ** 2 + $f ** 2);
            $k = log(1 + 0.045 * $G) / 0.045;
            // Hue values
            $a99 = $k * $e / $G;
            $b99 = $k * $f / $G;
        }

        return new Din99($L99, $a99, $b99);
    }
}
