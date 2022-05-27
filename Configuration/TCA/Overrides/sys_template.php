<?php

defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript',
    'Media oEmbed'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript/DefaultProviders',
    'Media oEmbed default providers'
);
