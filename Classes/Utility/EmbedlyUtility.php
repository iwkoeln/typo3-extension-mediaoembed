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
class EmbedlyUtility {

	const EMBEDLY_SERVICES_URL = 'http://api.embed.ly/1/services';

	const EMBEDLY_SHORTNAME = 'embedly';

	/**
	 * @var \Sto\Mediaoembed\Domain\Model\Provider
	 */
	protected $embedlyProvider;

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
	 * Maps a provider property name to the corresponding embedly
	 * data array key
	 *
	 * @var array
	 */
	protected $providerPropertyMapping = array(
		'name' => 'displayname',
		'description' => 'about',
		'embedlyShortname' => 'name',
	);

	/**
	 * @var \Sto\Mediaoembed\Domain\Repository\ProviderRepository
	 */
	protected $providerRepository;

	/**
	 * @var int
	 */
	protected $sorting = 1;

	/**
	 * Initializes the URL that should be used to geht the embed.ly services
	 *
	 * @param string $embedlyServicesUrl The URL that should be used to get the embed.ly service info. If NULL default URL will be used.
	 */
	public function __construct($embedlyServicesUrl = NULL) {

		if (!isset($embedlyServicesUrl)) {
			$this->embedlyServicesUrl = static::EMBEDLY_SERVICES_URL;
		}
	}

	/**
	 * @return \Sto\Mediaoembed\Domain\Model\Provider
	 * @throws \RuntimeException
	 */
	protected function initializeEmbedlyProvider() {

		if (!isset($this->embedlyProvider)) {
			$this->embedlyProvider = $this->providerRepository->findOneByEmbedlyShortname(static::EMBEDLY_SHORTNAME);

			if (!isset($this->embedlyProvider)) {
				throw new \RuntimeException('The embedly provider was not found');
			}
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

		$json = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->embedlyServicesUrl);

		$services = json_decode($json, TRUE);

		if (!isset($services)) {
			throw new \RuntimeException('Error getting services from embed.ly: ' . json_last_error());
		}

		$this->initializeEmbedlyProvider();

		foreach ($services as $serviceData) {

			/** @var \Sto\Mediaoembed\Domain\Model\Provider $provider */
			$provider = $this->providerRepository->findOneByEmbedlyShortname($serviceData['name']);
			$isNewProvider = FALSE;

			if (!isset($provider)) {
				$provider = $this->providerRepository->findOneByUrlSchemeArray($serviceData['regex']);
			}

			if (isset($provider)) {
			} else {
				$provider = $this->objectManager->get('Sto\\Mediaoembed\\Domain\Model\\Provider');
				$isNewProvider = TRUE;
			}

			$providerDataChanged = $this->updateProviderData($serviceData, $provider);

			if ($providerDataChanged) {
				if ($isNewProvider) {
					$this->providerRepository->add($provider);
					$this->log->info(sprintf('Added new provider %s to repository.', $provider->getName()));
				} else {
					$this->providerRepository->update($provider);
					$this->log->info(sprintf('Updated provider %s in repository since data was changed.', $provider->getName()));
				}
			}

			$this->sorting += 512;
		}
	}

	/**
	 * Updates the given provider with the given provider data and writes
	 * any updates to the log
	 *
	 * @param array $providerData provider data from embed.ly
	 * @param \Sto\Mediaoembed\Domain\Model\Provider $provider Provider that should be updated
	 * @return bool TRUE if provider data was changed
	 */
	protected function updateProviderData($providerData, $provider) {

		$providerDataChanged = FALSE;

		foreach ($this->providerPropertyMapping as $property => $arrayKey) {
			$providerDataChanged = $providerDataChanged || $this->updateProviderProperty($provider, $property, $providerData[$arrayKey]);
		}

		$providerDataChanged = $providerDataChanged || $this->updateProviderProperty($provider, 'sorting', $this->sorting);

		$currentUrlSchemes = $provider->getUrlSchemes();
		$provider->setUrlSchemesFromArray($providerData['regex']);
		$newUrlSchemes = $provider->getUrlSchemes();

		if ($currentUrlSchemes !== $newUrlSchemes) {
			$providerDataChanged = TRUE;
			$this->log->info(sprintf('Updated URL schemes for provider %s with value ' . LF . LF . '%s' . LF . LF . 'old value was:' . LF . '%s' . LF, $provider->getName(), $newUrlSchemes, $currentUrlSchemes));
		}

		if (!$provider->getUsesGenericProvider($this->embedlyProvider)) {
			$providerDataChanged = TRUE;
			$provider->addGenericProvider($this->embedlyProvider);
			$this->log->info(sprintf('Added embed.ly as generic provider to provider %s', $provider->getName()));
		}

		return $providerDataChanged;
	}

	/**
	 * Updates the given property in the given provider with the given
	 * value and writes any updates to the log
	 *
	 * @param \Sto\Mediaoembed\Domain\Model\Provider $provider
	 * @param string $property name of the property that should be updated (getters and setters must exist!)
	 * @param mixed $embedlyValue the new value that should be set in the given property
	 * @return bool TRUE if provider data was changed
	 */
	protected function updateProviderProperty($provider, $property, $embedlyValue) {

		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);

		$providerDataChanged = FALSE;
		$currentValue = $provider->$getter();

		if ($currentValue !== $embedlyValue) {
			$providerDataChanged = TRUE;
			$providerName = $provider->getName();
			$provider->$setter($embedlyValue);
			$this->log->info(sprintf('Updated provider property %s of provider %s with value %s (old value was: %s)', $property, $providerName, $embedlyValue, $currentValue));
		}

		return $providerDataChanged;
	}
}

?>