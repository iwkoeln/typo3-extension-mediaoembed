<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Model;

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
 * A mediaoembed tt_content record.
 */
class Content
{
    public function __construct(private readonly int $uid, private readonly string $url, private readonly int $maxHeight, private readonly int $maxWidth, private readonly bool $playRelated)
    {
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function shouldPlayRelated(): bool
    {
        return $this->playRelated;
    }
}
