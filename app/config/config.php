<?php

declare(strict_types=1);

return [
    'app_name' => 'TechSolutions Inventory Suite (PHP MVC)',
    'base_path' => dirname(__DIR__, 2),
    'base_url' => '/techsolutions-inventory-suite/public',
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'techsolutions_inventory',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'igv_rate' => 0.18,
];
