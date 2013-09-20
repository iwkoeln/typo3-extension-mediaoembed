<?php
namespace Sto\Mediaoembed\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Utility class for handling embedly data
 */
class PanziUtility {

	const PANZI_ENDPOINTS_URL = 'https://raw.github.com/panzi/oembedendpoints/master/endpoints-simple.json';

	/**
	 * @var \TYPO3\CMS\Core\Log\Logger
	 */
	protected $log;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $panziEndpointsUrl;

	/**
	 * @var \Sto\Mediaoembed\Domain\Repository\ProviderRepository
	 */
	protected $providerRepository;

	/**
	 * Initializes the URL that should be used to geht the embed.ly services
	 *
	 * @param string $panziEndpointsUrl The URL that should be used to get the service info from the panzi Github repository. If NULL default URL will be used.
	 */
	public function __construct($panziEndpointsUrl = NULL) {

		if (!isset($panziEndpointsUrl)) {
			$this->panziEndpointsUrl = static::PANZI_ENDPOINTS_URL;
		}
	}

	/**
	 * Initializes the logger
	 *
	 * @var \TYPO3\CMS\Core\Log\LogManager $logManager
	 */
	public function injectLogManager(\TYPO3\CMS\Core\Log\LogManager $logManager) {
		$this->log = $logManager->getLogger(__CLASS__);
	}

	/**
	 * Injector for the provider repository, makes sure that disabled providers
	 * are included in all queries
	 *
	 * @param \Sto\Mediaoembed\Domain\Repository\ProviderRepository $providerRepository
	 */
	public function injectProviderRepository(\Sto\Mediaoembed\Domain\Repository\ProviderRepository $providerRepository) {
		$providerRepository->includeDisabledProviders();
		$this->providerRepository = $providerRepository;
	}

	/**
	 * Runs through all providers found at embed.ly and updates the providers in the database.
	 *
	 * @throws \RuntimeException
	 */
	public function updateProviders() {

		$json = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->panziEndpointsUrl);

		$services = json_decode($json, TRUE);

		if (!isset($services)) {
			throw new \RuntimeException('Error getting services from panzi Github repository: ' . json_last_error());
		}

		foreach ($services as $endpoint => $urlSchemeArray) {

			$provider = $this->providerRepository->findOneByUrlSchemeArray(array_merge($urlSchemeArray, array($endpoint)));

			if (isset($provider)) {
				echo $provider->getName() . LF;
			} else {
				echo $endpoint . LF;
			}
		}
	}
}

?>