<?php
/**
 * Database Configuration (Prepared for Future Database Integration)
 * The application works completely without requiring a database at this stage.
 */

return [
    'enabled'  => false, // Set to true when ready to connect to a real database
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => getenv('DB_PORT') ?: 3306,
    'dbname'   => getenv('DB_NAME') ?: 'it_portal',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset'  => 'utf8mb4',
];
