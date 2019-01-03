<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2019 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\Tool;

use Interop\Config\Exception\OptionNotFoundException;
use Interop\Config\Tool\ConfigReader;
use Interop\Config\Tool\ConsoleHelper;
use InteropTest\Config\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interop\Config\Tool\AbstractConfig
 * @covers \Interop\Config\Tool\ConfigReader
 * @covers \Interop\Config\Tool\ConsoleHelper
 */
class ConfigReaderTest extends TestCase
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
        TestAsset\TestStream::$data= [];
    }

    /**
     * @test
     */
    public function itDisplaysConfigFromFactory()
    {
        $cut = new ConfigReader($this->consoleHelper);

        $fullConfig = $this->getTestConfig();

        $config = $cut->readConfig($fullConfig, TestAsset\ConnectionConfiguration::class);

        self::assertSame($fullConfig['doctrine']['connection'], $config);
    }

    /**
     * @test
     */
    public function itDisplaysConfigFromFactoryByConfigId()
    {
        TestAsset\TestStream::$inputStack = ['unknown', 'orm_default'];
        $cut = new ConfigReader($this->consoleHelper);

        $fullConfig = $this->getTestConfig();

        $config = $cut->readConfig($fullConfig, TestAsset\UniversalContainerIdConfiguration::class);

        self::assertSame('No config id with name "unknown" exists.', trim(TestAsset\TestStream::$data['error']));
        self::assertSame(
            'For which config id orm_default, orm_second: For which config id orm_default, orm_second:',
            trim(TestAsset\TestStream::$data['output'])
        );
        self::assertSame($fullConfig['doctrine']['universal']['orm_default'], $config);
    }

    /**
     * @test
     */
    public function itDisplaysConfigFromFactoryForAllConfigIds()
    {
        TestAsset\TestStream::$inputStack = [''];
        $cut = new ConfigReader($this->consoleHelper);

        $fullConfig = $this->getTestConfig();

        $config = $cut->readConfig($fullConfig, TestAsset\UniversalContainerIdConfiguration::class);

        self::assertSame('For which config id orm_default, orm_second:', trim(TestAsset\TestStream::$data['output']));
        self::assertSame($fullConfig['doctrine']['universal'], $config);
    }

    /**
     * @test
     */
    public function itThrowsOptionNotFoundException()
    {
        $cut = new ConfigReader($this->consoleHelper);

        $this->expectException(OptionNotFoundException::class);

        $cut->readConfig([], TestAsset\ConnectionConfiguration::class);
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
