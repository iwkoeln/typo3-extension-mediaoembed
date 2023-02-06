<?php

namespace Sto\Mediaoembed\Request\HttpClient;

use Sto\Mediaoembed\Exception\HttpClientRequestException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetUrlHttpClient implements HttpClientInterface
{
    /**
     * @throws HttpClientRequestException
     */
    public function executeGetRequest(string $requestUrl): string
    {
        return (string)GeneralUtility::getURL($requestUrl);
    }
}
