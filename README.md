<p align="center">
  <img style="width:720px" src="https://raw.githubusercontent.com/ahmadmayahi/php-google-vision/main/art/php-google-vision.png" alt="PHP Google Vision" />
</p>

## STILL UNDER DEVELOPMENT - DO NOT USE IN PRODUCTION

**Requires PHP 8.0+**

For feedback, please [contact me](https://form.jotform.com/201892949858375).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ahmadmayahi/php-google-vision.svg?style=flat-square)](https://packagist.org/packages/ahmadmayahi/php-google-vision)
[![Tests](https://github.com/spatie/fork/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ahmadmayahi/php-google-vision/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ahmadmayahi/php-google-vision/Check%20&%20fix%20styling?label=code%20style)](https://github.com/ahmadmayahi/php-google-vision/actions?query=workflow%3A"Check+%26+fix+styling"+branch%main)
[![Total Downloads](https://img.shields.io/packagist/dt/ahmadmayahi/php-google-vision.svg?style=flat-square)](https://packagist.org/packages/ahmadmayahi/php-google-vision)

This package provides an elegant wrapper around [Google Vision API](https://github.com/googleapis/google-cloud-php-vision) and more.

It's an effort to make Google Vision API easy and fun to work with.

# Contents

- [Installation](#installation)
- [Creating Google Service Account](#creating-google-service-account)
- [Configuration](#configuration)
- [Original Responses](#original-responses)
- [Integration with Laravel](#integration-with-laravel)
- [Face Detection](#face-detection)
  - [Draw box around faces](#draw-box-around-faces)
- [Image Text Detection](#image-text-detection)
  - [Get Plain Text](#get-plain-text)
  - [Get Document](#get-document)
- [Detect Image Properties](#image-properties-detection)
- [Landmark Detection](#landmark-detection)
- [Label Detection](#label-detection)
- [Logo Detection](#logo-detection)
- [Object Localizer](#object-localizer)
  - [Detect Objects](#detect-objects)
  - [Draw Box Around Objects](#draw-box-around-objects)
  - [Draw Box Around Objects With Text](#draw-box-around-objects-with-text)
- [Web Detection](#web-detection)

## Installation

You may install the package via composer:

```bash
composer require ahmadmayahi/php-google-vision
```

## Creating Google Service Account

First, you must [create a Google service account](https://cloud.google.com/iam/docs/creating-managing-service-accounts) and setup the configuration object.

## Configuration

```php
use AhmadMayahi\Vision\Config;

$config = (new Config())
    // optional
    ->setRequestTimeout(50)

    // optional: by default it uses the sys_get_temp_dir() function
    ->setTempDirPath('/path/to/temp')
    
    // Required: path to your google service account;
    ->setCredentialsPathname('path/to/google-service-account.json');
```

## Original Responses

All the features come with `getOriginalResponse()` method which returns the original response that's generated by [PHP Google Vision package](https://github.com/googleapis/google-cloud-php-vision).

You may get the original response for any feature as follows:

```php
use AhmadMayahi\Vision\Vision;

$response = (new Vision($config))
    ->file('/path/to/input/file')
    ->faceDetection()
    ->getOriginalResponse();
```

Depending on the feature, the response type might vary, here is a list of all the response types:

|Feature|Response Type|
|---|---|
|`faceDetection`|`Google\Protobuf\Internal\RepeatedField` contains `Google\Cloud\Vision\V1\FaceAnnotation`|
|`imageTextDetection`|`Google\Protobuf\Internal\RepeatedField`|
|`imagePropertiesDetection`|`Google\Cloud\Vision\V1\ImageProperties`|
|`labelDetection`|`Google\Protobuf\Internal\RepeatedField`|
|`landmarkDetection`|`Google\Protobuf\Internal\RepeatedField`|
|`logoDetection`|`Google\Protobuf\Internal\RepeatedField`|
|`objectLocalizer`|`Google\Protobuf\Internal\RepeatedField`|
|`safeSearchDetection`|`Google\Cloud\Vision\V1\SafeSearchAnnotation`|

The `file()` method accepts the following types:

- Local file path: `path/to/your/file`.
- Google Storage path: `gs://path/to/file`.
- File resource, such as `fopen()`.
- `SplFileInfo`.
- `SplFileObject`.

You may also use the static `new` method to instanciate the `Vision` object:

```php
use AhmadMayahi\Vision\Vision;

$vision = Vision::new($config)
    ->file('/path/to/image.jpg')
    ->faceDetection()
    ->getOriginalResponse();
```

## Integration with Laravel

Open up the `AppServiceProvider` and add the following lines:

```php
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Config;

public function register()
{
    $this->app->singleton(Vision::class, function ($app) {
        $config = (new Config())
            ->setCredentialsPathname(config('vision.service_account_path'));
    
        return new Vision($config);
    });
}
```

Using Dependency Injection:

```php
use AhmadMayahi\Vision\Vision;
use Illuminate\Http\Request;

class FaceDetectionController
{
    public function detect(Request $request, Vision $vision)
    {
        $vision = $vision
            ->file($request->face_file->path())
            ->faceDetection()
            ->detect();
            
        // ...
    }
}
```

You may also resolve the object using the `app` helper as follows:

```php
use AhmadMayahi\Vision\Vision;

/** @var Vision $vision */
$vision = app(Vision::class);

$result = $vision
    ->file('path/to/file')
    ->faceDetection()
    ->detect();

// ...
```

## Face Detection

[Face Detection](https://cloud.google.com/vision/docs/detecting-faces) detects multiple faces within an image along with the associated key facial attributes such as emotional state or `wearing headwear`.

The `detect` method returns a `Generator` of `AhmadMayahi\Vision\Data\FaceData`:

```php
use AhmadMayahi\Vision\Vision;

$faces = (new Vision($config))
    ->file('/path/to/image.jpg')
    ->faceDetection()
    ->detect();

echo count($faces). ' faces found';

foreach ($faces as $faceData) {
    // How angry the face is? 
    $faceData->getAnger();
    
    // Surprise reaction? 
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
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Enums\ColorEnum;

$analyzer = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->faceDetection()
    ->drawBoxAroundFaces(ColorEnum::MAGENTA)
    ->toJpeg('faces.jpg'); // Alternatively, you may use "toPng", "toGif", "toBmp".
```

> All the drawing methods return an object of type `AhmadMayahi\Vision\Utils\Image`.

![Larry Page and Sergey Brin Faces](tests/files/output/larry-sergey.jpg)

> This feature doesn't support Google Storage yet.

## Image Text Detection

### Get plain text

The `getPlainText` returns an object of type `AhmadMayahi\Vision\Data\ImageTextData`.

```php
use AhmadMayahi\Vision\Vision;

$response = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->imageTextDetection()
    ->getPlainText();

$response->getLocale(); // locale, for example "en" for English
$response->getText();   // Image text
```

You may also get the plain text using `__toString()`:

```php
echo $response;
```

### Get Document

The `getDocument` returns an object of type `AhmadMayahi\Vision\Data\ImageTextData`.

```php
use AhmadMayahi\Vision\Vision;

$response = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->imageTextDetection()
    ->getDocument();
 
$response->getLocale(); // locale, for example "en" for English
$response->getText();   // Image text
```

> The difference between `getPlainText()` and `getDocuemnt()` is that the first one only retrieves the plain text (no bullets, signs, etc...), whereas the latter one tries to retrieve the entire document (including bullets, symbols, etc...).

## Image Properties Detection

The [Image Properties](https://cloud.google.com/vision/docs/detecting-properties) feature detects general attributes of the image, such as dominant color.

The `detect` method returns a `Generator` of `AhmadMayahi\Vision\Data\ImagePropertiesData`:

```php
use AhmadMayahi\Vision\Vision;

$properties = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->imagePropertiesDetection()
    ->detect();

foreach ($properties as $item) {
    $item->getRed();

    $item->getBlue();

    $item->getGreen();

    $item->getPixelFraction();    
}
```

## Landmark Detection

[Landmark Detection](https://cloud.google.com/vision/docs/detecting-landmarks) detects popular natural and human-made structures within an image.

```php
use AhmadMayahi\Vision\Vision;

$landmarks = (new Vision($config))
    ->file('/path/to/baghdad.jpg')
    ->landmarkDetection()
    ->detect();

foreach ($landmarks as $landmark) {
    $landmark->getName();
    
    // An array containing the detected locations in latitude/longitude format.
    $landmark->getLocations();
}
```

## Safe Search Detection

[SafeSearch Detection](https://cloud.google.com/vision/docs/detecting-safe-search) detects explicit content such as adult content or violent content within an image.

The `detect` method returns an object of type `AhmadMayahi\Vision\Data\SafeSearchData`:

```php
use AhmadMayahi\Vision\Vision;

$result = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->safeSearchDetection()
    ->detect();

$result->getAdult();

$result->getMedical();

$result->getViolence();

$result->getRacy();

$result->getSpoof();
```

## Label Detection

[Detect](https://cloud.google.com/vision/docs/labels) and extract information about entities in an image, across a broad group of categories.

The `detect` method returns an a `Generator` of labels:

```php
use AhmadMayahi\Vision\Vision;

$labels = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->labelDetection()
    ->detect();
```

## Logo Detection

[Detect](https://cloud.google.com/vision/docs/detecting-logos) and extract information about entities in an image, across a broad group of categories.

The `detect` method returns an `Generator` of logos:

```php
use AhmadMayahi\Vision\Vision;

$labels = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->logoDetection()
    ->detect();
```

## Object Localizer

[Object Localizer](https://cloud.google.com/vision/docs/object-localizer) detects and extract multiple objects in an image with Object Localization.

### Detect Objects

The `detect` method returns a `Generator` of `AhmadMayahi\Vision\Data\LocalizedObjectData`:

```php
use AhmadMayahi\Vision\Vision;

$objects = (new Vision($config))
    ->file('/path/to/image.jpg')
    ->objectLocalizer()
    ->detect();

/** @var AhmadMayahi\Vision\Data\LocalizedObjectData $obj */
foreach ($objects as $obj) {
    $obj->getName();
    
    $obj->getLanguageCode();
    
    $obj->getMid();
    
    $obj->getNormalizedVertices();
    
    $obj->getScore();
}
```

### Draw Box Around Objects

You may draw box around objects using the `drawBoxAroundObjects` method:

```php
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Enums\ColorEnum;

$objects = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->objectLocalizer()
    ->drawBoxAroundObjects(ColorEnum::GREEN)
    ->toJpeg('out.jpg');
```

![Larry Page and Sergey Brin faces](tests/files/output/larry-sergey-objects.jpg)

The `drawBoxAroundObjects()` takes an optional`callback` as a second parameter:

```php
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Utils\Image;
use AhmadMayahi\Vision\Data\LocalizedObjectData;

$objects = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->objectLocalizer()
    ->drawBoxAroundObjects(ColorEnum::GREEN, function(Image $outputImage, LocalizedObjectData $object) {
        // Get GD Image
        $outputImage->getImage();
        
        // Get object info
        $object->getName();
    });
```

> This feature doesn't support Google Storage yet.

### Draw Box Around Objects With Text

You may want to draw box around objects and include the object's text as well:

```php
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\FontEnum;
use AhmadMayahi\Vision\Utils\Image;
use AhmadMayahi\Vision\Data\LocalizedObjectData;

$objects = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->objectLocalizer()
    ->drawBoxAroundObjectsWithText(
        boxColor: ColorEnum::GREEN,
        textColor: ColorEnum::RED,
        fontSize: 15,
        font: FontEnum::OPEN_SANS_BOLD_ITALIC,
    )
    ->toJpeg('output.jpg');
```

![Larry Page and Sergey Brin Objects](tests/files/output/larry-sergey-objects-text.jpg)

> This feature doesn't support Google Storage yet.

## Web Detection

[Web Detection](https://cloud.google.com/vision/docs/detecting-web) detects Web references to an image.

```php
use AhmadMayahi\Vision\Vision;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\FontEnum;
use AhmadMayahi\Vision\Utils\Image;
use AhmadMayahi\Vision\Data\LocalizedObjectData;

$response = (new Vision($config))
    ->file('/path/to/input/image.jpg')
    ->webDetection()
    ->detect(); 

$response->getFullMatchingImages();

$response->getBestGuessLabels();

$response->getPagesWithMatchingImages();

$response->getPartialMatchingImages();

$response->getVisuallySimilarImages();

$response->getWebEntities();
```

The `detect` method returns either an object of tupe `AhmadMayahi\Vision\Data\WebData` or `null` value.

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
