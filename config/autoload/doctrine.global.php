<?php

declare(strict_types=1);

use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver_class' => Driver::class,
                'params' => [
                    'host'     => 'localhost',
                    'path'  => __DIR__ . "/../../data/database.sqlite3"
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'drivers' => [
                    'App\Entity' => [
                        'class' => AttributeDriver::class,
                        'cache' => 'array',
                        'paths' => [
                            __DIR__ . '/../../src/App/src/Entity',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
