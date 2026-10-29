<?php
namespace Core;

use PDO;
use Exception;
use RuntimeException;

/**
 * Lazy, Safe Database Connection Factory
 * Designed so that the application works 100% without a database server.
 */
class Database
{
    private static ?PDO $connection = null;
    private static bool $attempted = false;
    private static ?string $lastError = null;

    /**
     * Lazily get a PDO connection instance.
     * Returns null if database is disabled or unreachable, without crashing.
     *
     * @return PDO|null
     */
    public static function getConnection(): ?PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        if (self::$attempted) {
            return null; // Don't retry repeatedly in the same request if already failed
        }

        self::$attempted = true;

        $configPath = CONFIG_PATH . '/database.php';
        if (!file_exists($configPath)) {
            self::$lastError = "Database configuration file not found.";
            return null;
        }

        $config = require $configPath;

        // Check if database feature is enabled
        if (empty($config['enabled'])) {
            self::$lastError = "Database integration is not enabled in config/database.php.";
            return null;
        }

        $host    = $config['host'] ?? '127.0.0.1';
        $port    = $config['port'] ?? 3306;
        $dbname  = $config['dbname'] ?? '';
        $user    = $config['username'] ?? 'root';
        $pass    = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';

        if (empty($dbname)) {
            self::$lastError = "Database name is not configured.";
            return null;
        }

        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbname, $charset);
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 3, // Fast fail (3 seconds max)
            ];

            self::$connection = new PDO($dsn, $user, $pass, $options);
            return self::$connection;
        } catch (Exception $e) {
            self::$lastError = "Unable to connect to database server.";
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a database connection is actively configured and reachable.
     *
     * @return bool
     */
    public static function isConnected(): bool
    {
        return self::getConnection() !== null;
    }

    /**
     * Retrieve the last connection error message safely (without exposing internal credentials).
     *
     * @return string|null
     */
    public static function getLastError(): ?string
    {
        return self::$lastError;
    }
}
