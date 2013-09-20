<?php
namespace Sto\Mediaoembed\Domain\Model;

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
 * A provider offering oEmbed services
 */
class Provider extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Description of the provider
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Shortname used by the embed.ly service
	 *
	 * @var string
	 */
	protected $embedlyShortname;

	/**
	 * Endpoint that should be used to get the embed data
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * TRUE if the provider is generic and supports multiple other
	 * providers
	 *
	 * @var boolean
	 */
	protected $isGeneric;

	/**
	 * Name of the provider
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * Defines the priority of the server
	 *
	 * @var int
	 */
	protected $sorting;

	/**
	 * URL Schemes that this provider can handle
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $urlSchemes;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sto\Mediaoembed\Domain\Model\Provider>
	 */
	protected $useGenericProviders;

	/**
	 * @param \Sto\Mediaoembed\Domain\Model\Provider $genericProvider
	 */
	public function addGenericProvider(\Sto\Mediaoembed\Domain\Model\Provider $genericProvider) {

		if (!$genericProvider->getIsGeneric()) {
			throw new \RuntimeException('Trying to set a non generic provider as generic provider');
		}

		$this->initializeGenericProviders();
		$this->useGenericProviders->attach($genericProvider);
	}

	/**
	 * @param \Sto\Mediaoembed\Domain\Model\Provider $provider
	 * @return bool
	 */
	public function equals(\Sto\Mediaoembed\Domain\Model\Provider $provider) {
		$myUid = $this->getUid();
		$theirUid = $provider->getUid();
		if ($myUid === $theirUid) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getEmbedlyShortname() {
		return $this->embedlyShortname;
	}

	/**
	 * @return string
	 */
	public function getEndpoint() {
		return $this->endpoint;
	}

	public function getUsesGenericProvider($provider) {
		$this->initializeGenericProviders();
		return $this->useGenericProviders->contains($provider);
	}

	/**
	 * @return boolean
	 */
	public function getIsGeneric() {
		return $this->isGeneric;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * @return string
	 */
	public function getUrlSchemes() {
		return $this->urlSchemes;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public function getUseGenericProviders() {
		return $this->useGenericProviders;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param string $embedlyShortname
	 */
	public function setEmbedlyShortname($embedlyShortname) {
		$this->embedlyShortname = $embedlyShortname;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	public function setSorting($sorting) {
		$this->sorting = intval($sorting);
	}

	/**
	 * @param array $urlSchemeArray
	 */
	public function setUrlSchemesFromArray($urlSchemeArray) {
		$this->urlSchemes = implode(LF, $urlSchemeArray);
	}

	protected function initializeGenericProviders() {
		if (!isset($this->useGenericProviders)) {
			$this->useGenericProviders = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}
	}
}

?>