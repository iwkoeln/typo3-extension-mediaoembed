<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Sto\Mediaoembed\Controller\OembedController;
use Sto\Mediaoembed\Install\MigrateContentElementsUpdate;
use Sto\Mediaoembed\Install\MigrateContentElementsUpdateLegacy;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

defined('TYPO3') || die();

$bootMediaoembed = function () {
    $currentVersion = VersionNumberUtility::getNumericTypo3Version();
    $lllPrefix = 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

    $registerPluginLegacy = function () {
        ExtensionUtility::configurePlugin(
            'Mediaoembed',
            'OembedMediaRenderer',
            /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
            [OembedController::class => 'renderMedia'],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    };

    $registerPlugin = function () {
        ExtensionUtility::configurePlugin(
            'Mediaoembed',
            'OembedMediaRenderer',
            /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
            [OembedController::class => 'renderMedia'],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    };

    $usesNewPluginRegistration = version_compare($currentVersion, '10.0.0', '>=');
    $pluginRegistrationMethod = $usesNewPluginRegistration ? $registerPlugin : $registerPluginLegacy;
    $pluginRegistrationMethod();

    $hasNewUpgradeWizard = version_compare($currentVersion, '9.4.0', '>=');
    $upgradeWizardClass = $hasNewUpgradeWizard
        ? MigrateContentElementsUpdate::class
        : MigrateContentElementsUpdateLegacy::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tx_mediaoembed_migratecontentelements'] =
        $upgradeWizardClass;

    ExtensionManagementUtility::addPageTSConfig(
        '
mod.wizards.newContentElement {
	wizardItems {
		special.elements {
			mediaoembed_oembedmediarenderer {
				iconIdentifier = extensions-mediaoembed-content-externalmedia
				title = ' . $lllPrefix . 'tt_content.CType.I.tx_mediaoembed
				description = ' . $lllPrefix . 'new_content_element_wizard_oembedmediarenderer_description
				tt_content_defValues {
					CType = mediaoembed_oembedmediarenderer
				}
			}
		}
		special.show := addToList(mediaoembed_oembedmediarenderer)
	}
}
'
    );
};

$bootMediaoembed();
unset($bootMediaoembed);
