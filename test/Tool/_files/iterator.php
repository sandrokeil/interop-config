<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

return new ArrayIterator(
    [
        // vendor name
        'doctrine' => [
            // package name
            'connection' => [
                // container id
                'orm_default' => [
                    // mandatory params
                    'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                    'params' => [
                        'host'     => 'localhost',
                        'port'     => 3306,
                        'user'     => 'username',
                        'password' => 'password',
                        'dbname'   => 'database',
                    ],
                ],
            ],
            // package name
            'universal' => [
                // container id
                'orm_default' => [
                    // mandatory params
                    'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                    'params' => [
                        'host'     => 'localhost',
                        'user'     => 'username',
                        'password' => 'password',
                        'dbname'   => 'database',
                    ],
                ],
                // container id
                'orm_second' => [
                    // mandatory params
                    'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                    'params' => [
                        'host'     => 'localhost',
                        'port'     => 3306,
                        'user'     => 'username',
                        'password' => 'password',
                        'dbname'   => 'database',
                    ],
                ],
            ],
        ],
        'one' => [
            'two' => [
                'three' => [
                    'four' => [
                        'name' => 'test',
                        'class' => 'stdClass',
                    ],
                ],
            ],
        ],
    ]
);
