<div align="center">

## Color Difference

![License](https://icon.itwmw.com/badge/License-Apache--2.0-blue)
![PHP Version Support](https://icon.itwmw.com/badge/PHP-%5E8.1-green?logo=php&logoColor=violet)

</div>
A library for calculating the perceptual difference between colors (∆E). 

The difference or distance between two colors is a metric of interest in color science. It allows quantified examination of a notion that formerly could only be described with adjectives. Quantification of these properties is of great importance to those whose work is color-critical. Common definitions make use of the Euclidean distance in a device independent color space.

The following measurements are supported:

- Euclidean
- Weighted Euclidean
- DIN99 (2007)
- CIE76 (1976)
- CIE94 (1994)
- CIEDE2000 (2000)
- CMC l:c (1984)

All calculations are performed in either L\*a\*b* or L\*C\*H* space (as the metrics prescribe).

## Quick Start
This library is available on packagist, like so:
```shell
composer require itwmw/color-difference
```

## Usage
The color difference metrics are implemented via the following functions:

- Euclidean RGB `getDifferenceEuclideanRGB(Color $color): float`
- Euclidean Lab `getDifferenceEuclideanLab(Color $color): float`
- Weighted Euclidean RGB `getDifferenceWeightedEuclideanRGB(Color $color): float`
- Din99 `getDifferenceDin99(Color $color): float`
- CIE76: `getDifferenceCIE76(Color $color): float`
- CIE94: `getDifferenceCIE94(Color $color, CIE94 $type = CIE94::GraphicArts): float`
- CIEDE2000: `getDifferenceCIEDE2000(Color $color): float`
- CMC l:c: `getDifferenceCMC(Color $color, CMC $type = CMC::Imperceptibility): float`

```php
use Itwmw\ColorDifference\Color;
use Itwmw\ColorDifference\Lib\RGB;
use Itwmw\ColorDifference\Support\CIE94;
use Itwmw\ColorDifference\Support\CMC;

$color  = new Color(new RGB(255, 183, 255));
$color2 = new Color(new RGB(55, 65, 53));
echo('Din99:' . $color->getDifferenceDin99($color2) . "\n");
echo('CIE76:' . $color->getDifferenceCIE76($color2) . "\n");
echo('CIE94-GraphicArts:' . $color->getDifferenceCIE94($color2, CIE94::GraphicArts) . "\n");
echo('CIE94-Textiles:' . $color->getDifferenceCIE94($color2, CIE94::Textiles) . "\n");
echo('CMC-Acceptability:' . $color->getDifferenceCMC($color2, CMC::Acceptability) . "\n");
echo('CMC-Imperceptibility:' . $color->getDifferenceCMC($color2, CMC::Imperceptibility) . "\n");
echo('CIEDE2000:' . $color->getDifferenceCIEDE2000($color2) . "\n");
echo('Euclidean RGB:' . $color->getDifferenceEuclideanRGB($color2) . "\n");
echo('Euclidean Lab:' . $color->getDifferenceEuclideanLab($color2) . "\n");
echo('Weighted Euclidean RGB:' . $color->getDifferenceWeightedEuclideanRGB($color2) . "\n");
```

CIE94 and CMC l:c accept an optional parameter adjusting the metric for the kind of quasimetric being evaluated. CIE94 offers an application type choice of either 'graphicArts' or 'textiles' for their eponymous use. CMC l:c offers a threshold choice of either 'acceptability' or 'imperceptibility' that nuances the just-noticeable difference between the colors.

## Reference White
Use `CIEIlluminant` to specify Reference White for the Color class
Support:

- A; //Incandescent/tungsten
- B; //Old direct sunlight at noon
- C; //Old daylight
- D50; //ICC profile PCS
- D55; //Mid-morning daylight
- D65; //Daylight, sRGB, Adobe-RGB
- D75; //North sky daylight
- E; //Equal energy
- F1; //Daylight Fluorescent
- F2; //Cool fluorescent
- F3; //White Fluorescent
- F4; //Warm White Fluorescent
- F5; //Daylight Fluorescent
- F6; //Lite White Fluorescent
- F7; //Daylight fluorescent, D65 simulator
- F8; //Sylvania F40, D50 simulator
- F9; //Cool White Fluorescent
- F10; //Ultralume 50, Philips TL85
- F11; //Ultralume 40, Philips TL84
- F12; //Ultralume 30, Philips TL83

```php
new Color(new RGB(255, 183, 255), referenceWhite: ReferenceWhite::D65);
```

## RGB Working Space
Use `RGBSpace` to specify RGB Working Space for the Color class
Support:

- Adobe RGB (1998)
- AppleRGB
- Best RGB
- Beta RGB
- Bruce RGB
- CIE RGB
- ColorMatch RGB
- Don RGB 4
- ECI RGB
- Ekta Space PS5
- NTSC RGB
- PAL/SECAM RGB
- ProPhoto RGB
- SMPTE-C RGB
- sRGB
- Wide Gamut RGB

```php
new Color(new RGB(255, 183, 255), RGBSpace: RGBSpace::sRGB_D65);
```
> In general, `RGB Working Space` needs to correspond to `Reference White`.

The default is:
```php
new Color(new RGB(255, 183, 255), referenceWhite: ReferenceWhite::D65, RGBSpace: RGBSpace::sRGB_D65);
```