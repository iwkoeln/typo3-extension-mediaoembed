<?php

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

/** @var string $_EXTKEY */
// phpcs:ignore Squiz.NamingConventions.ValidVariableName
$EM_CONF[$_EXTKEY] = [
    'title' => 'External media (oEmbed)',
    'description' => 'External media (YouTube, Flickr, ...) content elements using the http://oembed.com/ standard.',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-10.4.99',
            'extbase' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Alexander Stehlik',
    'author_email' => 'alexander.stehlik.deleteme@gmail.com',
    'author_company' => '',
    'version' => '2.1.0-dev',
];
