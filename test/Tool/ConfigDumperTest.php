<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\Tool;

use Interop\Config\Tool\ConfigDumper;
use Interop\Config\Tool\ConsoleHelper;
use InteropTest\Config\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interop\Config\Tool\AbstractConfig
 * @covers \Interop\Config\Tool\ConfigDumper
 * @covers \Interop\Config\Tool\ConsoleHelper
 */
class ConfigDumperTest extends TestCase
{
    /**
     * @var resource Exists only for testing.
     */
    private $errorStream = STDERR;

    /**
     * Input stream
     *
     * @var resource
     */
    private $inputStream;

    /**
     * Output stream
     *
     * @var resource
     */
    private $outputStream;

    /**
     * Console Helper
     *
     * @var ConsoleHelper
     */
    private $consoleHelper;


    public function setUp()
    {
        parent::setUp();

        if (!stream_wrapper_register("test", \InteropTest\Config\TestAsset\TestStream::class)) {
            throw new \RuntimeException('Failed to register protocol');
        }

        $this->inputStream = fopen('test://input', 'r+', false);
        $this->outputStream = fopen('test://output', 'r+', false);
        $this->errorStream = fopen('test://error', 'r+', false);
        $this->consoleHelper = new ConsoleHelper($this->inputStream, $this->outputStream, $this->errorStream);
    }

    public function tearDown()
    {
        stream_wrapper_unregister('test');
        TestAsset\TestStream::$inputStack = [];
        TestAsset\TestStream::$data = [];
    }

    public function providerConfig()
    {
        $testConfig = $this->getTestConfig();

        // order is expected, config from file
        return [
            [
                ['doctrine' => ['connection' => []]],
                [],
            ],
            [
                $testConfig,
                $testConfig,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerConfig
     */
    public function itDumpsConfigFromFactory($expected, $configFromFile)
    {
        $cut = new ConfigDumper($this->consoleHelper);

        $config = $cut->createConfig($configFromFile, TestAsset\ConnectionConfiguration::class);

        self::assertSame($expected, $config);
    }

    public function providerConfigId()
    {
        $testConfig = $this->getTestConfig();

        // order is expected, config from file
        return [
            [
                ['doctrine' => ['connection' => ['orm_default' => []]]],
                [],
            ],
            [
                $testConfig,
                $testConfig,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerConfigId
     */
    public function itDumpsConfigFromFactoryByConfigId($expected, $configFromFile)
    {
        TestAsset\TestStream::$inputStack = ['orm_default'];
        $cut = new ConfigDumper($this->consoleHelper);

        $config = $cut->createConfig($configFromFile, TestAsset\ConnectionContainerIdConfiguration::class);

        self::assertSame(
            'Multiple instances are supported, please enter a config id (default):',
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($expected, $config);
    }

    /**
     * @test
     */
    public function itDumpsConfigFromFactoryByConfigIdWithDefault()
    {
        TestAsset\TestStream::$inputStack = [''];
        $cut = new ConfigDumper($this->consoleHelper);

        $expected =  ['doctrine' => ['connection' => ['default' => []]]];

        $config = $cut->createConfig([], TestAsset\ConnectionContainerIdConfiguration::class);

        self::assertSame(
            trim('Multiple instances are supported, please enter a config id (default):'),
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($expected, $config);
    }

    public function providerDefaultOptions()
    {
        $testConfig = $this->getTestConfig();

        $defaultConfig = [
            'doctrine' => [
                'connection' => [
                    'params' => [
                        'host' => 'awesomehost',
                        'port' => 4444,
                    ],
                ],
            ],
        ];

        $output = 'Please enter a value for params.host (awesomehost): '
            . 'Please enter a value for params.port (4444):';

        // order is expected, config from file, input stack, output
        return [
            [
                $defaultConfig,
                [],
                [],
                $output,
            ],
            [
                array_replace_recursive($testConfig, $defaultConfig),
                $testConfig,
                [],
                $output,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerDefaultOptions
     */
    public function itDumpsConfigFromFactoryWithDefaults($expected, $configFromFile, $inputStack, $output)
    {
        TestAsset\TestStream::$inputStack = $inputStack;
        $cut = new ConfigDumper($this->consoleHelper);

        $config = $cut->createConfig($configFromFile, TestAsset\ConnectionDefaultOptionsConfiguration::class);

        self::assertSame(
            trim($output),
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($expected, $config);
    }

    public function providerDefaultMandatoryOptions()
    {
        $testConfig = $this->getTestConfig();

        $configDefault = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'host' => 'awesomehost',
                            'port' => 4444,
                        ],
                        'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver'
                    ],
                ],
            ],
        ];

        $configExisting = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'host' => 'localhost',
                            'port' => 3306,
                        ],
                        'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver'
                    ],
                ],
            ],
        ];

        $configInput = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'params' => [
                            'host' => 'myhost',
                            'port' => 1111,
                        ],
                        'driverClass' => 'PDO'
                    ],
                ],
            ],
        ];

        // @codingStandardsIgnoreStart
        $output = 'Multiple instances are supported, please enter a config id (default): Please enter a value for driverClass: Please enter a value for params.host (awesomehost): Please enter a value for params.port (4444):';
        $outputExistingConfig = 'Multiple instances are supported, please enter a config id (default): Please enter a value for driverClass (Doctrine\DBAL\Driver\PDOMySql\Driver): Please enter a value for params.host (localhost), current value awesomehost: Please enter a value for params.port (3306), current value 4444:';
        // @codingStandardsIgnoreEnd

        // order is expected, config from file, input stack, output
        return [
            [
                $configDefault,
                [],
                ['orm_default', 'Doctrine\DBAL\Driver\PDOMySql\Driver', '', ''],
                $output,
            ],
            [
                array_replace_recursive($testConfig, $configExisting),
                $testConfig,
                ['orm_default', '', '', ''],
                $outputExistingConfig,
            ],
            [
                array_replace_recursive($testConfig, $configInput),
                $testConfig,
                ['orm_default', 'PDO', 'myhost', 1111],
                $outputExistingConfig,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerDefaultMandatoryOptions
     */
    public function itDumpsConfigFromFactoryWithDefaultsAndMandatory(
        $expected,
        $configFromFile,
        $inputStack,
        $output
    ) {
        TestAsset\TestStream::$inputStack = $inputStack;
        $cut = new ConfigDumper($this->consoleHelper);

        $config = $cut->createConfig(
            $configFromFile,
            TestAsset\ConnectionDefaultOptionsMandatoryContainetIdConfiguration::class
        );

        self::assertSame(
            trim($output),
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($expected, $config);
    }

    public function providerRecursiveMandatoryOptions()
    {
        $testConfig = $this->getTestConfig();

        $config = [
            'doctrine' => [
                'universal' => [
                    'orm_default' => [
                        'params' => [
                            'host' => 'awesomehost',
                            'port' => 4444,
                            'user' => 'root',
                            'dbname' => 'database',

                        ],
                        'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver'
                    ],
                ],
            ],
        ];

        $configExisting = array_replace_recursive(
            $testConfig,
            [
                'doctrine' => [
                    'universal' => [
                        'orm_default' => [
                            'params' => [
                                'port' => 4444,
                                'user' => 'root',
                            ],
                        ],
                    ],
                ],
            ]
        );

        // @codingStandardsIgnoreStart
        $output = 'Multiple instances are supported, please enter a config id (default): Please enter a value for params.user: Please enter a value for params.dbname: Please enter a value for driverClass: Please enter a value for params.host (awesomehost): Please enter a value for params.port (4444):';
        $outputExistingConfig = 'Multiple instances are supported, please enter a config id (default): Please enter a value for params.user (username): Please enter a value for params.dbname (database): Please enter a value for driverClass (Doctrine\DBAL\Driver\PDOMySql\Driver): Please enter a value for params.host (localhost), current value awesomehost: Please enter a value for params.port (4444):';
        // @codingStandardsIgnoreEnd

        // order is expected, config from file, input stack, output
        return [
            [
                $config,
                [],
                ['orm_default', 'root', 'database', 'Doctrine\DBAL\Driver\PDOMySql\Driver', '', ''],
                $output,
            ],
            [
                $configExisting,
                $testConfig,
                ['orm_default', 'root', 'database', 'Doctrine\DBAL\Driver\PDOMySql\Driver', '', ''],
                $outputExistingConfig,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerRecursiveMandatoryOptions
     */
    public function itDumpsConfigFromFactoryWithRecursiveMandatory(
        $expected,
        $configFromFile,
        $inputStack,
        $output
    ) {
        TestAsset\TestStream::$inputStack = $inputStack;
        $cut = new ConfigDumper($this->consoleHelper);

        $config = $cut->createConfig($configFromFile, TestAsset\UniversalContainerIdConfiguration::class);

        self::assertSame(
            trim($output),
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($expected, $config);
    }

    /**
     * @test
     */
    public function itDumpsConfigFile()
    {
        $cut = new ConfigDumper($this->consoleHelper);

        $config = [
            // vendor name
            'doctrine' => [
                // package name
                'connection' => [
                    // container id
                    'orm_default' => [
                        // mandatory params
                        'driverClass' => 'PDO',
                        'params' => [
                            'host'     => 'localhost',
                            'port'     => 3306,
                        ],
                    ],
                ]
            ]
        ];

        $configFile = <<<EOF
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => \PDO::class,
                'params' => [
                    'host' => 'localhost',
                    'port' => 3306,
                ],
            ],
        ],
    ],
];
EOF;

        self::assertSame($configFile, $cut->dumpConfigFile($config));
    }

    /**
     * Returns test config
     *
     * @return array
     */
    private function getTestConfig(): array
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load default
        if (is_readable('test/TestConfig.php')) {
            $config = require 'test/testing.config.php';
        } else {
            $config = require 'test/testing.config.php.dist';
        }

        return $config;
    }
}
