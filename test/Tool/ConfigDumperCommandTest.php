<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\Tool;

use Interop\Config\Tool\ConfigDumper;
use Interop\Config\Tool\ConfigDumperCommand;
use Interop\Config\Tool\ConsoleHelper;
use InteropTest\Config\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interop\Config\Tool\AbstractCommand
 * @covers \Interop\Config\Tool\ConfigDumperCommand
 * @covers \Interop\Config\Tool\ConsoleHelper
 */
class ConfigDumperCommandTest extends TestCase
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
    }

    public function tearDown()
    {
        stream_wrapper_unregister('test');
        TestAsset\TestStream::$inputStack = [];
        TestAsset\TestStream::$data = [];
    }

    /**
     * @test
     */
    public function itWritesConfig()
    {
        $argv = [self::CONFIG_FILE, TestAsset\ConnectionConfiguration::class];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper());

        $cut($argv);
        self::assertTrue(file_exists(self::CONFIG_FILE));
        unlink(self::CONFIG_FILE);
    }

    /**
     * @test
     */
    public function itWritesConfigToExistingFile()
    {
        $testConfig = <<<EOF
<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

use Interop\Config\RequiresConfig;

// my comment
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

        file_put_contents(self::CONFIG_FILE, $testConfig);

        $argv = [self::CONFIG_FILE, TestAsset\ConnectionConfiguration::class];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper());

        $cut($argv);
        self::assertSame($testConfig, file_get_contents(self::CONFIG_FILE));
        unlink(self::CONFIG_FILE);
    }

    /**
     * @test
     */
    public function itDisplaysHelp()
    {
        $argv = ['help'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Usage:', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysHelpIfNoArguments()
    {
        $argv = [];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Usage:', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfClassNotExists()
    {
        $argv = [self::CONFIG_FILE, 'UnknownClassName'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Class "UnknownClassName"', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfFileIsNotWriteable()
    {
        $argv = ['/unknown/place/config.php', TestAsset\ConnectionConfiguration::class];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Cannot create configuration', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfFileReturnsNoArray()
    {
        $argv = [__DIR__ . '/_files/no_array.php', TestAsset\ConnectionConfiguration::class];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Configuration at path', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysErrorIfClassDoesNotImplementRequiresConfig()
    {
        $argv = [self::CONFIG_FILE, TestAsset\TestStream::class];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith(
            'Class "InteropTest\Config\TestAsset\TestStream" does not implement',
            TestAsset\TestStream::$data['error']
        );
    }

    /**
     * @test
     */
    public function itDisplaysError()
    {
        $argv = ['wrong'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Missing class name', TestAsset\TestStream::$data['error']);
    }
}
