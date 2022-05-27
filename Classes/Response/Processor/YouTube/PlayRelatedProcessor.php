<?php

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use InvalidArgumentException;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\ResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeAwareProcessorTrait;
use Sto\Mediaoembed\Response\VideoResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PlayRelatedProcessor implements ResponseProcessorInterface
{
    use IframeAwareProcessorTrait;

    private ?Configuration $configuration = null;

    public function injectConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function processResponse(GenericResponse $response)
    {
        if ($response instanceof VideoResponse === false) {
            throw new InvalidArgumentException('This processor only works with video responses!');
        }

        $this->processVideoResponse($response);
    }

    private function processVideoResponse(VideoResponse $response)
    {
        $replaceYoutubeUrl = function (array $urlParts) {
            $newUrl = $urlParts['scheme'] . '://';
            $newUrl .= $urlParts['host'];
            $newUrl .= $urlParts['path'] ?? '';

            $query = $urlParts['query'] ?? '';
            $queryParams = $query !== '' ? GeneralUtility::explodeUrl2Array($query) : [];
            $queryParams['rel'] = $this->configuration->shouldPlayRelated() ? '1' : '0';

            return $newUrl . ('?' . ltrim(GeneralUtility::implodeArrayForUrl('', $queryParams), '&'));
        };

        $this->modifyIframeUrl($response, $replaceYoutubeUrl);
    }
}
