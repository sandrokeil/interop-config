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
use PHPUnit_Framework_TestCase as TestCase;

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
    public function itDisplaysHelp()
    {
        $argv = ['help'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith("\nUsage:", TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysHelpIfNoArguments()
    {
        $argv = [];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith("\nUsage:", TestAsset\TestStream::$data['error']);
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
        $argv = ['/unknown/place/config.phpp', 'UnknownClassName'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith('Cannot create configuration', TestAsset\TestStream::$data['error']);
    }

    /**
     * @test
     */
    public function itDisplaysError()
    {
        $argv = ['wrong'];

        $cut = new ConfigDumperCommand($this->consoleHelper, new ConfigDumper($this->consoleHelper));

        $cut($argv);

        self::assertStringStartsWith("Missing class name", TestAsset\TestStream::$data['error']);
    }
}
