<?php
namespace Sto\Mediaoembed\Domain\Repository;

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
 * Repository for finding providers
 */
class ProviderRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * If TRUE all queries will also return disabled providers
	 *
	 * @var bool
	 */
	protected $findDisabledProviders = FALSE;

	/**
	 * Creates a query that ignores storage page and enable fields
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function createQuery() {

		$query = parent::createQuery();

		if ($this->findDisabledProviders) {
			$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		}

		return $query;
	}

	/**
	 * After calling this method all queries will exclude disabled providers
	 * which is the default behaviour
	 */
	public function excludeDisabledProviders() {
		$this->findDisabledProviders = FALSE;
	}

	/**
	 * Finds all providers including disabled in all pages
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllEverywhere() {
		$query = $this->createQuery();
		return $query->execute();
	}

	/**
	 * Tries to find a provider that has the given embed.ly short name.
	 * If more than one is found an exception will be thrown.
	 *
	 * @param string $shortname
	 * @return \Sto\Mediaoembed\Domain\Model\Provider
	 * @throws \RuntimeException
	 */
	public function findOneByEmbedlyShortname($shortname) {

		$query = $this->createQuery();
		$query->matching($query->equals('embedlyShortname', $shortname));

		if ($query->execute()->count() > 1) {
			throw new \RuntimeException(sprintf('More than one provider with embed.ly shortname %s was found.', $shortname));
		}

		return $query->execute()->getFirst();
	}

	/**
	 * Tries to find a provider that uses the given URL scheme.
	 *
	 * If this fails  the hostname will be extracted from the scheme
	 * and a new search will be started.
	 *
	 * @param string $urlScheme
	 * @return \Sto\Mediaoembed\Domain\Model\Provider
	 * @throws \RuntimeException
	 */
	public function findOneByUrlScheme($urlScheme) {

		$query = $this->createQuery();
		$query->matching($query->like('urlSchemes', '%' . $urlScheme . '%', FALSE));

		$provider = NULL;

		if ($query->execute()->count() === 1) {
			$provider = $query->execute()->getFirst();
		} else if ($query->execute()->count() === 0) {
			$provider = $this->findOneByUrlSchemeHost($urlScheme);
		} else {
			throw new \RuntimeException(sprintf('More than one provider was founc with scheme %s', $urlScheme));
		}

		return $provider;
	}

	/**
	 * Ties to find a provider that has at least one of the given URL
	 * schemes as configured URL scheme
	 *
	 * @param array $urlSchemeArray
	 * @return \Sto\Mediaoembed\Domain\Model\Provider
	 * @throws \RuntimeException
	 */
	public function findOneByUrlSchemeArray($urlSchemeArray) {

		/** @var \Sto\Mediaoembed\Domain\Model\Provider $provider */
		$provider = NULL;
		$searchedSchemes = array();

		if (!is_array($urlSchemeArray)) {
			return NULL;
		}

		foreach ($urlSchemeArray as $urlScheme) {

			$foundProvider = $this->findOneByUrlScheme($urlScheme);
			$searchedSchemes[] = $urlScheme;

			if (isset($provider) && isset($foundProvider) && !$foundProvider->equals($provider)) {
				throw new \RuntimeException('Found more than on provider matching these schemes: ' . "\n" . implode("\n", $searchedSchemes));
			}

			$provider = $foundProvider;
		}

		return $provider;
	}

	/**
	 * After calling this method all queries will include disabled providers
	 */
	public function includeDisabledProviders() {
		$this->findDisabledProviders = TRUE;
	}

	/**
	 * Tries to find a provider by extracting the host from the given URL
	 * scheme and looking up the lost in the configured URL schemes
	 *
	 * @param string $urlScheme
	 * @return \Sto\Mediaoembed\Domain\Model\Provider
	 * @throws \RuntimeException
	 */
	protected function findOneByUrlSchemeHost($urlScheme) {

		$urlScheme = str_replace('*', '', $urlScheme);
		$schemeHost = parse_url($urlScheme, PHP_URL_HOST);
		$schemeHost = trim($schemeHost);
		$schemeHost = trim($schemeHost, '.');

		if (!strlen($schemeHost)) {
			throw new \RuntimeException(sprintf('Could not get host name from scheme %s', $urlScheme));
		}

		$query = $this->createQuery();
		$query->matching($query->like('urlSchemes', '%' . $schemeHost . '%', FALSE));

		if ($query->execute()->count() > 1) {
			throw new \RuntimeException(sprintf('More than one provider was found with host %s', $schemeHost));
		}

		return $query->execute()->getFirst();
	}
}

?>