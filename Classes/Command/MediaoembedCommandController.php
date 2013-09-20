<?php
namespace Sto\Mediaoembed\Command;

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
 * Commands for updating / checking the current providers
 */
class MediaoembedCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * @var \TYPO3\CMS\Core\Log\Logger
	 */
	protected $log;

	/**
	 * @var \Sto\Mediaoembed\Domain\Repository\ProviderRepository
	 * @inject
	 */
	protected $providerRepository;

	/**
	 * Initializes the logger
	 *
	 * @var \TYPO3\CMS\Core\Log\LogManager $logManager
	 */
	public function injectLogManager(\TYPO3\CMS\Core\Log\LogManager $logManager) {
		$this->log = $logManager->getLogger(__CLASS__);
	}


	/**
	 * Remove invalid providers
	 *
	 * Providers with no endpoint or no generic provider assigned will
	 * be removed
	 *
	 * @return void
	 */
	public function cleanupProvidersCommand() {

		$this->providerRepository->includeDisabledProviders();
		$providers = $this->providerRepository->findAllEverywhere();
		$removedProviderCount = 0;

		/** @var \Sto\Mediaoembed\Domain\Model\Provider $provider */
		foreach ($providers as $provider) {

			if ($provider->getIsGeneric()) {
				continue;
			}

			$endpointUrl = $provider->getEndpoint();
			if (strlen(trim($endpointUrl))) {
				continue;
			}

			$genericProviders = $provider->getUseGenericProviders();
			if (count($genericProviders)) {
				continue;
			}

			$this->providerRepository->remove($provider);
			$this->log->info(sprintf('removed provider %s', $provider->getName()));
			$removedProviderCount++;
		}

		if ($removedProviderCount) {
			$this->log->info(sprintf('removed %d providers', $removedProviderCount));
		} else {
			$this->log->info('no provider was removed');
		}
	}

	/**
	 * Sync providers panzi Github repo
	 *
	 * @param string $panziProvidersUrl The URL that should the used to fetch the available services, default is https://raw.github.com/panzi/oembedendpoints/master/endpoints-simple.json
	 */
	public function syncPanziProvidersCommand($panziProvidersUrl = NULL) {

		/** @var \Sto\Mediaoembed\Utility\PanziUtility $panziUtility */
		$panziUtility = $this->objectManager->get('Sto\\Mediaoembed\\Utility\\PanziUtility', $panziProvidersUrl);
		$panziUtility->updateProviders();
	}

	/**
	 * Sync providers with embed.ly
	 *
	 * @param string $embedlyServicesUrl The URL that should the used to fetch the available services, default is http://api.embed.ly/1/services
	 */
	public function syncEmbedlyProvidersCommand($embedlyServicesUrl = NULL) {

		/** @var \Sto\Mediaoembed\Utility\EmbedlyUtility $embedlyUtility */
		$embedlyUtility = $this->objectManager->get('Sto\\Mediaoembed\\Utility\\EmbedlyUtility', $embedlyServicesUrl);
		$embedlyUtility->updateProviders();
	}
}

?>