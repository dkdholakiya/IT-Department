<?php
namespace Core;

use PDO;
use RuntimeException;

/**
 * Base Model for IT Department Portal
 * Provides lazy database access and transaction utilities.
 */
abstract class Model
{
    /**
     * Lazily obtain the PDO instance.
     * Throws a friendly RuntimeException if persistent database storage is requested but not configured.
     *
     * @return PDO
     * @throws RuntimeException
     */
    protected function getDb(): PDO
    {
        $db = Database::getConnection();
        if ($db === null) {
            $msg = Database::getLastError() ?: "Database connection is not configured yet.";
            throw new RuntimeException($msg);
        }
        return $db;
    }

    /**
     * Safe check whether database is currently available.
     *
     * @return bool
     */
    public function hasDatabase(): bool
    {
        return Database::isConnected();
    }
}
