<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Install\Repository;

use TYPO3\CMS\Core\Database\Connection;

class UpdateRepositoryFactory
{
    public static function getUpdateRepository(): UpdateRepository
    {
        if (class_exists(Connection::class)) {
            return new DoctrineUpdateRepository();
        }

        return new DatabaseUpdateRepository();
    }
}
