<?php

namespace Sto\Mediaoembed\Tests\Unit\Response;

use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\LinkResponse;
use Sto\Mediaoembed\Response\PhotoResponse;
use Sto\Mediaoembed\Response\ResponseBuilder;
use Sto\Mediaoembed\Response\RichResponse;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\PhotoDownloadService;
use Sto\Mediaoembed\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class ResponseBuilderTest extends AbstractUnitTest
{
    public function testBuildResponseGeneric()
    {
        $responseData = [
            'type' => 'a custom response',
            'title' => 'Some generic response',
        ];
        $responseClass = GenericResponse::class;

        $response = $this->buildResponse($responseClass, $responseData);
        $this->assertEquals('Some generic response', $response->getTitle());
    }

    public function testBuildResponseLink()
    {
        $responseData = [
            'type' => 'link',
            'title' => 'My link title',
        ];
        $responseClass = LinkResponse::class;

        /** @var LinkResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        $this->assertEquals('My link title', $response->getTitle());
    }

    public function testBuildResponsePhoto()
    {
        $responseData = [
            'type' => 'photo',
            'url' => 'https://my-awsome.tld/photo',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = PhotoResponse::class;

        $fileProphecy = $this->prophesize(FileInterface::class);
        $file = $fileProphecy->reveal();

        $photoDownloadServiceProphecy = $this->prophesize(PhotoDownloadService::class);
        $photoDownloadServiceProphecy->downloadPhoto(
            'https://my-embed-url.tld/embed/4kgfjk',
            'https://my-awsome.tld/photo'
        )
            ->shouldBeCalledOnce()
            ->willReturn($file);

        /** @var PhotoResponse $response */
        $response = $this->buildResponse($responseClass, $responseData, $photoDownloadServiceProphecy);
        $this->assertEquals('https://my-awsome.tld/photo', $response->getUrl());
        $this->assertEquals($file, $response->getLocalFile());
    }

    public function testBuildResponseRich()
    {
        $responseData = [
            'type' => 'rich',
            'html' => '<div>dummyrich</div>',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = RichResponse::class;

        /** @var RichResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        $this->assertEquals('<div>dummyrich</div>', $response->getHtml());
    }

    public function testBuildResponseVideo()
    {
        $responseData = [
            'type' => 'video',
            'html' => '<div>dummyvideo</div>',
            'width' => 100,
            'height' => 60,
        ];
        $responseClass = VideoResponse::class;

        /** @var VideoResponse $response */
        $response = $this->buildResponse($responseClass, $responseData);
        $this->assertEquals('<div>dummyvideo</div>', $response->getHtml());
    }

    protected function buildResponse(
        string $responseClass,
        array $responseData,
        $photoDownloadServiceProphecy = null
    ): GenericResponse {
        $objectManager = $this->prophesize(ObjectManagerInterface::class);
        $objectManager->get($responseClass)->shouldBeCalledOnce()->willReturn(new $responseClass());

        if (!$photoDownloadServiceProphecy) {
            $photoDownloadServiceProphecy = $this->prophesize(PhotoDownloadService::class);
        }

        $reponseBuilder = new ResponseBuilder($objectManager->reveal(), $photoDownloadServiceProphecy->reveal());
        $response = $reponseBuilder->buildResponse(
            'https://my-embed-url.tld/embed/4kgfjk'
        );

        $this->assertInstanceOf($responseClass, $response);

        return $response;
    }
}
