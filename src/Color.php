<?php

namespace Itwmw\ColorDifference;

use InvalidArgumentException;
use Itwmw\ColorDifference\Lib\Convert;
use Itwmw\ColorDifference\Lib\Din99;
use Itwmw\ColorDifference\Lib\Lab;
use Itwmw\ColorDifference\Lib\Lch;
use Itwmw\ColorDifference\Lib\RGB;
use Itwmw\ColorDifference\Lib\XYZ;
use Itwmw\ColorDifference\Support\CIE94;
use Itwmw\ColorDifference\Support\CIEIlluminant;
use Itwmw\ColorDifference\Support\CMC;
use Itwmw\ColorDifference\Support\RGBSpace;
use JetBrains\PhpStorm\ExpectedValues;
use JetBrains\PhpStorm\Pure;

class Color
{
    protected readonly Lab $lab;

    protected ?Din99 $din99 = null;

    protected ?Lch $lch = null;

    protected ?RGB $rgb = null;

    protected ?XYZ $xyz = null;

    /**
     * @param Lab|RGB|XYZ|string $color Lab,RGB,XYZ or Hex Color
     * @param CIEIlluminant $illuminant Standard illuminant {@see https://en.wikipedia.org/wiki/Standard_illuminant}
     * @param RGBSpace $RGBSpace        CIE RGB color space {@see https://en.wikipedia.org/wiki/CIE_1931_color_space#CIE_RGB_color_space}
     */
    #[Pure]
    public function __construct(
        Lab|RGB|XYZ|string $color,
        #[ExpectedValues(valuesFromClass: CIEIlluminant::class)]
        protected CIEIlluminant $illuminant = CIEIlluminant::D65,
        #[ExpectedValues(valuesFromClass: RGBSpace::class)]
        protected RGBSpace $RGBSpace = RGBSpace::D65_sRGB
    ) {
        if (is_string($color)) {
            $this->rgb = Convert::hex2Rgb($color);
            $this->lab = Convert::rgb2lab($this->rgb);
        } elseif ($color instanceof RGB) {
            $this->rgb = $color;
            $this->lab = Convert::rgb2Lab($color, $illuminant, $RGBSpace);
        } elseif ($color instanceof XYZ) {
            $this->xyz = $color;
            $this->lab = Convert::xyz2lab($color, $illuminant);
        } else {
            $this->lab = $color;
        }
    }

    public function getDin99(): Din99
    {
        if (!$this->din99) {
            $this->din99 = Convert::lab2Din99($this->lab);
        }

        return $this->din99;
    }

    public function getLch(): Lch
    {
        if (!$this->lch) {
            $this->lch = Convert::lab2Lch($this->getLab());
        }

        return $this->lch;
    }

    public function getXyz(): XYZ
    {
        if (!$this->xyz) {
            $this->xyz = Convert::lab2Xyz($this->getLab(), $this->illuminant);
        }

        return $this->xyz;
    }

    public function getRgb(): RGB
    {
        if (!$this->rgb) {
            $this->rgb = Convert::xyz2Rgb($this->getXyz());
        }

        return $this->rgb;
    }

    public function getLab(): Lab
    {
        return $this->lab;
    }

    /**
     * The DIN99 color space system is a further development of the CIELAB color space system developed by the
     * FNF / FNL 2 Colorimetry Working Committee .
     * The calculation is described in DIN 6176:
     * 2001-03 Colorimetric determination of color distances for body colors according to the DIN99 formula .
     * This color space is also quoted in the
     * ASTM (American Society for Testing and Materials) ASTM D 2244 from 2007 Standard Practice for
     * Calculation of Color Tolerances and Color Differences from Instrumentally Measured Color Coordinates .
     *
     * @param Color $color
     * @return float
     */
    public function getDifferenceDin99(Color $color): float
    {
        $d1 = $this->getDin99();
        $d2 = $color->getDin99();

        return sqrt(
            ($d2->L99 - $d1->L99) ** 2
            + ($d2->a99 - $d1->a99) ** 2
            + ($d2->b99 - $d1->b99) ** 2
        );
    }

    /**
     * The 1976 formula is the first formula that related a measured color
     * difference to a known set of CIELAB coordinates.
     * This formula has been succeeded by the 1994 and 2000
     * formulas because the CIELAB space turned out to be not as perceptually uniform as intended,
     * especially in the saturated regions. This means that this formula rates
     * these colors too highly as opposed to other colors.
     *
     * @param Color $color
     * @return float
     */
    #[Pure]
    public function getDifferenceCIE76(Color $color): float
    {
        $d1 = $this->getLab();
        $d2 = $color->getLab();

        return sqrt(
            ($d2->L - $d1->L) ** 2
            + ($d2->a - $d1->a) ** 2
            + ($d2->b - $d1->b) ** 2
        );
    }

    /**
     * The 1976 definition was extended to address perceptual non-uniformities,
     * while retaining the CIELAB color space, by the introduction of application-specific
     * weights derived from an automotive paint test's tolerance data.
     *
     * @param Color $color
     * @param CIE94 $type  the weighting factors kL, K1 and K2 depend on the application
     * @return float
     */
    #[Pure]
    public function getDifferenceCIE94(
        Color $color,
        #[ExpectedValues(valuesFromClass: CIE94::class)]
        CIE94 $type = CIE94::GraphicArts
    ): float {
        list($L1, $a1, $b1) = array_values(get_object_vars($this->getLab()));
        list($L2, $a2, $b2) = array_values(get_object_vars($color->getLab()));

        $C1 = sqrt($a1 * $a1 + $b1 * $b1);
        $C2 = sqrt($a2 * $a2 + $b2 * $b2);

        $dL2 = pow($L1 - $L2, 2);
        $dC2 = pow($C1 - $C2, 2);
        $dH2 = pow($a1 - $a2, 2) + pow($b1 - $b2, 2) - $dC2;

        return sqrt(
            $dL2 / pow($type->kl(), 2)
            + $dC2 / pow(1 + $type->k1() * $C1, 2)
            + $dH2 / pow(1 + $type->k2() * $C1, 2)
        );
    }

    /**
     * DeltaE2000 is a statistically significant improvement
     * over deltaE76 and deltaE94,
     * and is recommended by the CIE and Idealliance
     * especially for color differences less than 10 deltaE76
     *
     * The simplest color difference metric, ΔE76, is simply the Euclidean distance in Lab color space.
     * While this is a good first approximation,
     * color-critical industries such as printing and fabric dyeing soon developed improved formulae.
     * Currently, the most widely used formula is ΔE2000.
     * It corrects a number of known asymmetries and non-linearities compared to ΔE76.
     * @param Color $color
     * @return float
     */
    public function getDifferenceCIEDE2000(Color $color): float
    {
        list($L1, $a1, $b1) = array_values(get_object_vars($this->getLab()));
        list($L2, $a2, $b2) = array_values(get_object_vars($color->getLab()));

        $C1 = $this->getLch()->c;
        $C2 = $color->getLch()->c;

        // This implementation assumes the parametric
        // weighting factors kL, kC and kH
        // (for the influence of viewing conditions)
        // are all 1, as seems typical.
        $kL = $kC = $kH = 1;

        // mean Chroma
        $cBar = ($C1 + $C2) / 2;

        // calculate a-axis asymmetry factor from mean Chroma
        // this turns JND ellipses for near-neutral colors back into circles
        $C7      = pow($cBar, 7);
        $gFactor = pow(25, 7);
        $G       = 0.5 * (1 - sqrt($C7 / ($C7 + $gFactor)));

        // scale a axes by asymmetry factor
        // this by the way is why there is no Lab2000 color space
        $aDash1 = (1 + $G) * $a1;
        $aDash2 = (1 + $G) * $a2;

        // calculate new Chroma from scaled a and original b axes
        $cDash1 = sqrt($aDash1 ** 2 + $b1 ** 2);
        $cDash2 = sqrt($aDash2 ** 2 + $b2 ** 2);

        // calculate new hues, with zero hue for true neutrals
        // and in degrees, not radians
        $pi = pi();
        $h1 = (0 === $aDash1 && 0 === $b1) ? 0 : atan2($b1, $aDash1);
        $h2 = (0 === $aDash2 && 0 === $b2) ? 0 : atan2($b2, $aDash2);

        if ($h1 < 0) {
            $h1 += 2 * $pi;
        }
        if ($h2 < 0) {
            $h2 += 2 * $pi;
        }

        $h1 = rad2deg($h1);
        $h2 = rad2deg($h2);

        // Lightness and Chroma differences; sign matters
        $deltaL = $L2     - $L1;
        $deltaC = $cDash2 - $cDash1;

        // Hue difference, taking care to get the sign correct
        $hDiff = $h2 - $h1;
        $hSum  = $h1 + $h2;
        $hAbs  = abs($hDiff);

        if (0 === $cDash1 * $cDash2) {
            $dh = 0;
        } elseif ($hAbs <= 180) {
            $dh = $hDiff;
        } elseif ($hDiff > 180) {
            $dh = $hDiff - 360;
        } elseif ($hDiff < -180) {
            $dh = $hDiff + 360;
        } else {
            throw new InvalidArgumentException('the unthinkable has happened');
        }

        // weighted Hue difference, more for larger Chroma
        $hH = 2 * sqrt($cDash2 * $cDash1) * sin(deg2rad($dh) / 2);

        // calculate mean Lightness and Chroma
        $lDash  = ($L1 + $L2)         / 2;
        $cDash  = ($cDash1 + $cDash2) / 2;
        $cDash7 = pow($cDash, 7);

        // Compensate for non-linearity in the blue region of Lab.
        // Four possibilities for hue weighting factor,
        // depending on the angles, to get the correct sign
        if (0 == $cDash1 && 0 == $cDash2) {
            $hDash = $hSum;   // which should be zero
        } elseif ($hAbs <= 180) {
            $hDash = $hSum / 2;
        } elseif ($hSum < 360) {
            $hDash = ($hSum + 360) / 2;
        } else {
            $hDash = ($hSum - 360) / 2;
        }

        // positional corrections to the lack of uniformity of CIELAB
        // These are all trying to make JND ellipsoids more like spheres

        // SL Lightness crispening factor
        // a background with L=50 is assumed
        $lsq = ($lDash - 50) ** 2;
        $SL  = 1 + ((0.015 * $lsq) / sqrt(20 + $lsq));

        // SC Chroma factor, similar to those in CMC and deltaE 94 formulae
        $SC = 1 + 0.045 * $cDash;

        // Cross term T for blue non-linearity
        $T = 1;
        $T -= (0.17 * cos(deg2rad(($hDash - 30))));
        $T += (0.24 * cos(deg2rad(2 * $hDash)));
        $T += (0.32 * cos(deg2rad((3 * $hDash) + 6)));
        $T -= (0.20 * cos(deg2rad((4 * $hDash) - 63)));

        // SH Hue factor depends on Chroma,
        // as well as adjusted hue angle like deltaE94.
        $SH = 1 + 0.015 * $cDash * $T;

        // RT Hue rotation term compensates for rotation of JND ellipses
        // and Munsell constant hue lines
        // in the medium-high Chroma blue region
        // (Hue 225 to 315)
        $deltaTheta = exp(-1 * ((($hDash - 275) / 25) ** 2)) * 30;
        $RC         = sqrt($cDash7 / ($cDash7 + $gFactor))   * 2;
        $RT         = sin(deg2rad(2 * $deltaTheta))          * $RC          * -1;

        // Finally calculate the deltaE, term by term as root sum of squares
        $dE = ($deltaL / ($kL * $SL))  ** 2;
        $dE += ($deltaC / ($kC * $SC)) ** 2;
        $dE += ($hH / ($kH * $SH))     ** 2;
        $dE += $RT * ($deltaC / ($kC * $SC)) * ($hH / ($kH * $SH));
        return sqrt($dE);
    }

    /**
     * In 1984, the Colour Measurement Committee of the Society of Dyers and Colourists defined a difference measure,
     * also based on the L*C*h color model. Named after the developing committee, their metric is called CMC l:c.
     * The quasimetric has two parameters: lightness (l) and chroma (c),
     * allowing the users to weight the difference based on the ratio of l:c that is deemed appropriate for the application.
     * Commonly used values are 2:1 for acceptability and 1:1 for the threshold of imperceptibility.
     *
     * @param Color $color
     * @param CMC $type
     * @return float
     */
    public function getDifferenceCMC(
        Color $color,
        #[ExpectedValues(valuesFromClass: CMC::class)]
        CMC $type = CMC::Imperceptibility
    ): float {
        $b1 = $this->getLab();
        $b2 = $color->getLab();
        $c1 = $this->getLch()->c;
        $c2 = $color->getLch()->c;

        $sl = ($b1->L < 16.0) ? (0.511) : ((0.040975 * $b1->L) / (1.0 + 0.01765 * $b1->L));
        $sc = (0.0638 * $c1) / (1.0 + 0.0131 * $c1) + 0.638;
        $h1 = $this->getLch()->h;

        $t = (($h1 >= 164.0) && ($h1 <= 345.0)) ?
            (0.56 + abs(0.2 * cos((pi() * ($h1 + 168.0)) / 180.0))) :
            (0.36 + abs(0.4 * cos((pi() * ($h1 + 35.0)) / 180.0)));

        $c4 = pow($c1, 4);
        $f  = sqrt($c4 / ($c4 + 1900.0));

        $sh = $sc * ($f * $t + 1.0 - $f);

        $delL = $b1->L - $b2->L;
        $delC = $c1    - $c2;
        $delA = $b1->a - $b2->a;
        $delB = $b1->b - $b2->b;
        
        $dH2 = $delA * $delA + $delB * $delB - $delC * $delC;

        return sqrt(pow($delL / ($type->l() * $sl), 2)
            + pow($delC / ($type->c() * $sc), 2)
            + ($dH2 / pow($sh, 2)));
    }
}
