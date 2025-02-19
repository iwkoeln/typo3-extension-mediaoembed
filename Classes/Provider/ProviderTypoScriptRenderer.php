<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class ProviderTypoScriptRenderer
{
    /**
     * @param array|Endpoint[] $endpointsByName
     * @return string
     */
    public function render(array $endpointsByName)
    {
        $result = '// This file is autogenerated! See README.md!' . PHP_EOL;
        $result .= 'plugin.tx_mediaoembed.settings.providers {' . PHP_EOL;

        foreach ($endpointsByName as $endpoint) {
            $urlsConfigKey = $endpoint->getUrlConfigKey();

            $result .= '    ' . $endpoint->getName() . ' {' . PHP_EOL;
            $result .= '        endpoint = ' . $endpoint->getUrl() . PHP_EOL;
            $result .= '        ' . $urlsConfigKey . ' {' . PHP_EOL;

            $arrayIndex = 10;
            foreach ($endpoint->getUrlSchemes() as $urlScheme) {
                $result .= '            ' . $arrayIndex . ' = ' . $urlScheme . PHP_EOL;
                $arrayIndex += 10;
            }
            $result .= '        }' . PHP_EOL;
            $result .= '    }' . PHP_EOL . PHP_EOL;
        }

        return $result . ('}' . PHP_EOL);
    }
}
