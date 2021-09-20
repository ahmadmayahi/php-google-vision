<p align="center">
  <img style="width:720px" src="https://raw.githubusercontent.com/ahmadmayahi/php-google-vision/main/art/php-google-vision.png" alt="PHP Google Vision" />
</p>

## STILL UNDER DEVELOPMENT - DO NOT USE IN PRODUCTION

For feedback, please [contact me](https://form.jotform.com/201892949858375).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ahmadmayahi/php-google-vision.svg?style=flat-square)](https://packagist.org/packages/ahmadmayahi/php-google-vision)
[![Tests](https://github.com/spatie/fork/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ahmadmayahi/php-google-vision/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ahmadmayahi/php-google-vision/Check%20&%20fix%20styling?label=code%20style)](https://github.com/ahmadmayahi/php-google-vision/actions?query=workflow%3A"Check+%26+fix+styling"+branch%main)
[![Total Downloads](https://img.shields.io/packagist/dt/ahmadmayahi/php-google-vision.svg?style=flat-square)](https://packagist.org/packages/ahmadmayahi/php-google-vision)

This package provides fluent interfaces to interact with Google Vision API.

## Installation

You may install the package via composer:

```bash
composer require ahmadmayahi/php-google-vision
```

## Create Google Service Account

First of all, you must [create a Google service account](https://cloud.google.com/iam/docs/creating-managing-service-accounts) and setup the configuration object as follows:


## Configuration

```php
use AhmadMayahi\GoogleVision\Config;

$config = (new Config())
    ->setFile(__DIR__ . '/files/google-guys.jpg') // Path to your image file
    ->setCredentialsPathname('path/to/google-service-account.json');
```

## Detect faces

### Get the original response
Get the original response (this returns an object of type `Google\Protobuf\Internal\RepeatedField`):

```php
use AhmadMayahi\GoogleVision\Vision;

$response = (new Vision($config))
    ->detectFaces()
    ->getOriginalResponse();
```

> All the features support the `getOriginalResponse()`.

### Get face analyzer

The analyzer returns an array of `AhmadMayahi\GoogleVision\Data\FaceData`:

```php
use AhmadMayahi\GoogleVision\Vision;

$analyzer = (new Vision($config))
    ->detectFaces()
    ->analyze();

echo count($analyzer). ' faces found';

foreach ($analyzer as $faceData) {
    // How angry the face is? 
    $faceData->getAnger();
    
    // Supripse reaction? 
    $faceData->getSurprise();
    
    // Is he/she happy?
    $faceData->getJoy();
    
   // Bounds
   $faceData->getBounds();
}
```

> `getAnger`, `getSurprise` and `getJoy` return Likelihoods ratings which are expressed as 6 different values: `UNKNOWN`, `VERY_UNLIKELY`, `UNLIKELY`, `POSSIBLE`, `LIKELY`, or `VERY_LIKELY`.
> See [Likelihood](https://cloud.google.com/vision/docs/reference/rpc/google.cloud.vision.v1#likelihood).

### Draw box around faces

```php
use AhmadMayahi\GoogleVision\Vision;
use AhmadMayahi\GoogleVision\Enums\ColorEnum;

$outputFile = 'path/to/output/file.jpeg';
$color = ColorEnum::MAGENTA;

$analyzer = (new Vision($config))
    ->detectFaces()
    ->drawBoxAroundFaces($outputFile, $color);
```

![Larry Page and Sergey Brin faces](tests/files/output/larry-sergey.jpg)

## Detect Text in Images

### Get plain text

The `getPlainText` returns an object of type `AhmadMayahi\GoogleVision\Data\ImageTextData`.

```php
use AhmadMayahi\GoogleVision\Vision;

$response = (new Vision($config))
    ->detectImageText()
    ->getPlainText();

$response->getLocale(); // locale, for example "en" for English
$response->getText();   // Image text
```

### Get Document

Returns an object of type `AhmadMayahi\GoogleVision\Data\ImageTextData`:

```php
use AhmadMayahi\GoogleVision\Vision;

$response = (new Vision($config))
    ->detectImageText()
    ->getDocument();
 
$response->getLocale(); // locale, for example "en" for English
$response->getText();   // Image text
```

> The difference between `getPlainText()` and `getDocuemnt()` is that the first one only retrieves the plain text (no bullets, signs, etc...), whereas the latter one tries to retrieve the entire document (including bullets, symbols, etc...).

## Safe Search Detection

[SafeSearch Detection](https://cloud.google.com/vision/docs/detecting-safe-search) detects explicit content such as adult content or violent content within an image.

### Get safe search analyzer

The `getAnalyzer` returns an object of type `AhmadMayahi\GoogleVision\Data\SafeSearchData`:

```php
use AhmadMayahi\GoogleVision\Vision;

$analyzer = (new Vision($config))
    ->detectSafeSearch()
    ->analyze();

$analyzer->getAdult();

$analyzer->getMedical();

$analyzer->getViolence();

$analyzer->getRacy();

$analyzer->getSpoof();
```

> See [SafeSearchAnnotation](https://cloud.google.com/vision/docs/reference/rpc/google.cloud.vision.v1#google.cloud.vision.v1.SafeSearchAnnotation).


## Todo List
- [ ] Label detection
- [ ] Landmark detection
- [ ] Localized Object
- [ ] Logo detection
- [ ] Support Google Cloud
- [ ] Pdf Scanning
- [ ] Tiff Scanning
- [ ] Web
- [ ] Video-to-Text
- [ ] Speech-to-Text

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ahmad Mayahi](https://github.com/ahmadmayahi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
