<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\Tool;

use Interop\Config\Tool\ConfigReader;
use Interop\Config\Tool\ConfigReaderCommand;
use Interop\Config\Tool\ConsoleHelper;
use InteropTest\Config\TestAsset;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers \Interop\Config\Tool\AbstractCommand
 * @covers \Interop\Config\Tool\ConfigReaderCommand
 * @covers \Interop\Config\Tool\ConsoleHelper
 */
class ConfigReaderCommandTest extends TestCase
{
    const CONFIG_FILE = 'build/config.php';

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

        if (!stream_wrapper_register("test", TestAsset\TestStream::class)) {
            throw new \RuntimeException('Failed to register protocol');
        }

        $this->inputStream = fopen('test://input', 'r+', false);
        $this->outputStream = fopen('test://output', 'r+', false);
        $this->errorStream = fopen('test://error', 'r+', false);
        $this->consoleHelper = new ConsoleHelper($this->inputStream, $this->outputStream, $this->errorStream);

        file_put_contents(self::CONFIG_FILE, $this->getTestConfig());
    }

    public function tearDown()
    {
        stream_wrapper_unregister('test');
        TestAsset\TestStream::$inputStack = [];
        TestAsset\TestStream::$data = [];

        unlink(self::CONFIG_FILE);
    }

    /**
     * @test
     */
    public function itReadsConfigByConfigId()
    {

        $expectedOutput = <<<EOF
For which config id orm_default, orm_second: [
    'driverClass' => 'Doctrine\\\DBAL\\\Driver\\\PDOMySql\\\Driver',
    'params' => [
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'username',
        'password' => 'password',
        'dbname' => 'database',
    ],
]

EOF;
        TestAsset\TestStream::$inputStack = ['orm_second'];
        $argv = [self::CONFIG_FILE, TestAsset\UniversalContainerIdConfiguration::class];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);
        self::assertSame($expectedOutput, TestAsset\TestStream::$data['output']);
    }

    public function providerMultipleConfigIds()
    {
        return [
            [self::CONFIG_FILE],
            [__DIR__ . '/_files/iterator.php'],
            [__DIR__ . '/_files/iterator_aggregate.php'],
        ];
    }

    /**
     * @test
     * @dataProvider providerMultipleConfigIds
     */
    public function itReadsConfigMultipleConfigIds($file)
    {
        $expectedOutput = <<<EOF
For which config id orm_default, orm_second: [
    'orm_default' => [
        'driverClass' => 'Doctrine\\\DBAL\\\Driver\\\PDOMySql\\\Driver',
        'params' => [
            'host' => 'localhost',
            'user' => 'username',
            'password' => 'password',
            'dbname' => 'database',
        ],
    ],
    'orm_second' => [
        'driverClass' => 'Doctrine\\\DBAL\\\Driver\\\PDOMySql\\\Driver',
        'params' => [
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'username',
            'password' => 'password',
            'dbname' => 'database',
        ],
    ],
]

EOF;

        TestAsset\TestStream::$inputStack = [''];
        $argv = [$file, TestAsset\UniversalContainerIdConfiguration::class];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);
        self::assertSame($expectedOutput, TestAsset\TestStream::$data['output']);
    }

    /**
     * @test
     */
    public function itDisplaysHelp()
    {
        $argv = ['help'];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Usage:', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysHelpIfNoArguments()
    {
        $argv = [];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Usage:', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysError()
    {
        $argv = ['wrong'];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Missing class name', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfClassNotExists()
    {
        $argv = [self::CONFIG_FILE, 'UnknownClassName'];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Class "UnknownClassName"', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfFileIsNotReadable()
    {
        $argv = ['/unknown/place/config.php', TestAsset\UniversalContainerIdConfiguration::class];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Cannot read configuration', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfClassDoesNotImplementRequiresConfig()
    {
        $argv = [self::CONFIG_FILE, TestAsset\TestStream::class];

        $cut = new ConfigReaderCommand($this->consoleHelper, new ConfigReader($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith(
            'Class "InteropTest\Config\TestAsset\TestStream" does not implement',
            TestAsset\TestStream::$data['error']
        );
    }

    /**
     * Returns test config
     *
     * @return array
     */
    private function getTestConfig(): string
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load default
        if (is_readable('test/TestConfig.php')) {
            $config = file_get_contents('test/testing.config.php');
        } else {
            $config = file_get_contents('test/testing.config.php.dist');
        }

        return $config;
    }
}
