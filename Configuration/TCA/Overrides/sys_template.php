<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript',
    'Media oEmbed'
);

ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript/DefaultProviders',
    'Media oEmbed default providers'
);
