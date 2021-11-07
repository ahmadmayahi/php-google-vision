<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\WebEntity as WebEntityData;
use AhmadMayahi\Vision\Data\WebImage as WebImageData;
use AhmadMayahi\Vision\Data\WebPage as WebPageData;
use AhmadMayahi\Vision\Detectors\Web;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\WebDetection;
use Google\Cloud\Vision\V1\WebDetection\WebEntity;
use Google\Cloud\Vision\V1\WebDetection\WebImage;
use Google\Cloud\Vision\V1\WebDetection\WebLabel;
use Google\Cloud\Vision\V1\WebDetection\WebPage;

final class WebTest extends TestCase
{
    /** @test */
    public function it_should_get_web_original_response(): void
    {
        $client = $this->createMock(ImageAnnotatorClient::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $webDetection = $this->createMock(WebDetection::class);

        $annotateImageResponse
            ->expects($this->once())
            ->method('getWebDetection')
            ->willReturn($webDetection);

        $client
            ->expects($this->once())
            ->method('webDetection')
            ->willReturn($annotateImageResponse);

        $web = new Web($client, $this->getFile());
        $web->getOriginalResponse();
    }

    /** @test */
    public function it_should_detect_web(): void
    {
        $label = $this->createMock(WebLabel::class);
        $label
            ->expects($this->once())
            ->method('getLabel')
            ->willReturn('sergey brin');

        $webDetection = $this->createMock(WebDetection::class);

        $webDetection
            ->expects($this->once())
            ->method('getBestGuessLabels')
            ->willReturn($this->createRepeatedFieldIter([$label]));

        $webDetection
            ->expects($this->once())
            ->method('getPagesWithMatchingImages')
            ->willReturn($this->createRepeatedFieldIter($this->getPagesWithMatchingImagesMocked()));

        $webDetection
            ->expects($this->once())
            ->method('getFullMatchingImages')
            ->willReturn($this->createRepeatedFieldIter($this->getFullMatchingImagesMocked()));

        $webDetection
            ->expects($this->once())
            ->method('getPartialMatchingImages')
            ->willReturn($this->createRepeatedFieldIter($this->partialMatchingImagesMocked()));

        $webDetection
            ->expects($this->once())
            ->method('getVisuallySimilarImages')
            ->willReturn($this->createRepeatedFieldIter($this->visuallySimilarImagesMocked()));

        $webDetection
            ->expects($this->once())
            ->method('getWebEntities')
            ->willReturn($this->createRepeatedFieldIter($this->webEntitiesMocked()));

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getWebDetection')
            ->willReturn($webDetection);

        $client = $this->createMock(ImageAnnotatorClient::class);
        $client
            ->expects($this->once())
            ->method('webDetection')
            ->willReturn($annotateImageResponse);

        $web = new Web($client, $this->getFile());
        $res = $web->detect();

        $this->assertEquals(['sergey brin'], $res->bestGuessLabels);
        $this->assertEquals($this->getPagesWithMatchingImages(), $res->pagesWithMatchingImages);
        $this->assertEquals($this->getFullMatchingImages(), $res->fullMatchingImages);
        $this->assertEquals($this->partialMatchingImages(), $res->partialMatchingImages);
        $this->assertEquals($this->visuallySimilarImages(), $res->visuallySimilarImages);
        $this->assertEquals($this->webEntities(), $res->webEntities);
    }

    private function getPagesWithMatchingImages(): array
    {
        return [
            new WebPageData(
                'https://www.superyachtfan.com/yacht/dragonfly/owner/',
                '<b>SERGEY BRIN</b> • Net Worth $97 billion • Yacht • House • Private Jet',
                0.0,
            ),
            new WebPageData(
                'https://www.thesun.co.uk/news/7252824/sergey-brin-google-trump-voters-fascists/',
                'Google co-founder <b>Sergey Brin</b> compares Trump election to the rise ...',
                0.0,
            ),
            new WebPageData(
                'https://www.speakerbookingagency.com/talent/sergey-brin',
                '<b>Sergey Brin&#39;s</b> Booking Agent and Speaking Fee',
                0.0,
            ),
            new WebPageData(
                'https://howtoentrepreneur.com/google-sergey-brin',
                'Google&#39;s <b>Sergey Brin</b>: His Life Biography &amp; Success Quotes',
                0.0,
            ),
            new WebPageData(
                'https://tipsmake.com/larry-page-sergey-brin-and-3-principles-of-true-entrepreneurs',
                'Larry Page, <b>Sergey Brin</b> and 3 principles of true entrepreneurs',
                0.0,
            ),
            new WebPageData(
                'https://www.pinterest.com/carliehall5676/sergey-brin/',
                '17 <b>Sergey Brin</b> ideas - Pinterest',
                0.0,
            ),
            new WebPageData(
                'https://www.thesun.ie/news/3116003/google-co-founder-sergey-brin-compares-trump-election-to-the-rise-of-fascism-as-and-vows-to-thwart-populism-in-leaked-video/',
                'Google co-founder <b>Sergey Brin</b> compares Trump election to the rise ...',
                0.0,
            ),
        ];
    }

    private function getPagesWithMatchingImagesMocked(): array
    {
        $list = [];

        foreach ($this->getPagesWithMatchingImages() as $datum) {
            $webPage = $this->createMock(WebPage::class);

            $webPage
                ->method('getUrl')
                ->willReturn($datum->url);

            $webPage
                ->method('getPageTitle')
                ->willReturn($datum->title);

            $webPage
                ->method('getScore')
                ->willReturn($datum->score);

            $list[] = $webPage;
        }

        return $list;
    }

    private function getFullMatchingImages(): array
    {
        return [
            new WebImageData(
                'https://qph.fs.quoracdn.net/main-qimg-5929ee017df63d428111b8d7a4382205-c',
                0.0,
            ),
            new WebImageData(
                'https://cdn.digitalreachagency.com/wp-content/uploads/2017/05/larry_page_sergey_brin-ayn-rand.jpg',
                0.0,
            ),
            new WebImageData(
                'https://qph.fs.quoracdn.net/main-qimg-56561c27eefb81eeeb891eb492fd1e4f-c',
                0.0,
            ),
        ];
    }

    private function getFullMatchingImagesMocked(): array
    {
        $list = [];

        foreach ($this->getFullMatchingImages() as $image) {
            $webImage = $this->createMock(WebImage::class);

            $webImage
                ->method('getUrl')
                ->willReturn($image->url);

            $webImage
                ->method('getScore')
                ->willReturn($image->score);

            $list[] = $webImage;
        }

        return $list;
    }

    private function partialMatchingImages(): array
    {
        return [
            new WebImageData(
                'https://iqhaber.com/upload/media/entries/2020-12/09/445-entry-3-1607510283.jpg',
                0.0,
            ),
            new WebImageData(
                'https://e00-expansion.uecdn.es/imagenes/2015/01/16/empresastecnologia/1421378214_0.jpg',
                0.0,
            ),
            new WebImageData(
                'https://facefun.ir/wp-content/uploads/2021/10/28744__Surgey_Brin-a-220x150.jpg',
                0.0,
            ),
        ];
    }

    private function partialMatchingImagesMocked(): array
    {
        $list = [];

        foreach ($this->partialMatchingImages() as $image) {
            $webImage = $this->createMock(WebImage::class);
            $webImage
                ->method('getUrl')
                ->willReturn($image->url);
            $webImage
                ->method('getScore')
                ->willReturn($image->score);

            $list[] = $webImage;
        }

        return $list;
    }

    private function visuallySimilarImages(): array
    {
        return [
            new WebImageData(
                'https://st.quantrimang.com/photos/image/2016/08/25/Larry-Page-Sergey-Brin-650.jpg',
                0.0,
            ),
            new WebImageData(
                'https://i0.wp.com/centralnewsng.com/wp-content/uploads/2019/12/Larry-Page-Sergey-Brin-Central-News.jpg?fit=533%2C400&ssl=1',
                0.0,
            ),
            new WebImageData(
                'https://cdn.businessinsider.nl/wp-content/uploads/2013/08/Sergey-Brin.jpg',
                0.0,
            ),
        ];
    }

    private function visuallySimilarImagesMocked(): array
    {
        $list = [];

        foreach ($this->visuallySimilarImages() as $image) {
            $webImage = $this->createMock(WebImage::class);
            $webImage
                ->method('getUrl')
                ->willReturn($image->url);
            $webImage
                ->method('getScore')
                ->willReturn($image->score);

            $list[] = $webImage;
        }

        return $list;
    }

    private function webEntities(): array
    {
        return [
            new WebEntityData(
                '/m/0gjq6',
                14.994000434875488,
                'Sergey Brin',
            ),
            new WebEntityData(
                '/m/0gjpq',
                10.805999755859375,
                'Larry Page',
            ),
            new WebEntityData(
                '/g/11bwcf511s',
                0.7024000287055969,
                'Alphabet Inc.',
            ),
            new WebEntityData(
                '/m/045c7b',
                0.6288999915122986,
                'Google',
            ),
            new WebEntityData(
                '/m/0j7m2zm',
                0.5702000260353088,
                'Google Glass',
            ),
        ];
    }

    private function webEntitiesMocked(): array
    {
        $list = [];

        foreach ($this->webEntities() as $item) {
            $webEntity = $this->createMock(WebEntity::class);
            $webEntity
                ->method('getEntityId')
                ->willReturn($item->entityId);
            $webEntity
                ->method('getScore')
                ->willReturn($item->score);
            $webEntity
                ->method('getDescription')
                ->willReturn($item->description);

            $list[] = $webEntity;
        }

        return $list;
    }
}
