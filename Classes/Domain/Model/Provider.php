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
 * An oEmbed provider.
 */
class Provider
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $urlSchemes;

    private array $processors = [];

    public function __construct(string $name, string $endpoint, array $urlSchemes, private readonly bool $hasRegexUrlSchemes)
    {
        $this->name = $name;
        $this->endpoint = $endpoint;
        $this->urlSchemes = $urlSchemes;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]|array
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    public function getUrlSchemes(): array
    {
        return $this->urlSchemes;
    }

    public function hasRegexUrlSchemes(): bool
    {
        return $this->hasRegexUrlSchemes;
    }

    public function withProcessor(string $processorClass)
    {
        $this->processors[] = $processorClass;
    }
}
