<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This Exception will be thrown when no matching provider is found for a given URL.
 */
class NoMatchingProviderException extends OEmbedException
{
    /**
     * Initializes the Exception with a default message and a default code (1303248669).
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $message = 'No provider was found for the given URL: %s.';
        $message = sprintf($message, $url);
        parent::__construct($message, 1_303_248_669);
    }
}
