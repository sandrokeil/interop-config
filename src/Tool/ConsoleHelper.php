<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2020 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Interop\Config\Tool;

/**
 * Utilities for console tooling.
 *
 * Provides the following facilities:
 *
 * - Colorize strings using markup (e.g., `<info>message</info>`,
 *   `<error>message</error>`)
 * - Write output to a specified stream, optionally with colorization.
 * - Write a line of output to a specified stream, optionally with
 *   colorization, using the system EOL sequence..
 * - Write an error message to STDERR.
 * - Reads a single line from the user
 *
 * Colorization will only occur when expected sequences are discovered and then, only if the console terminal allows it.
 *
 * Essentially, provides the bare minimum to allow you to provide messages to the current console.
 *
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */
class ConsoleHelper
{
    const COLOR_GREEN = "\033[32m";
    const COLOR_YELLOW = "\033[33m";
    const COLOR_RED = "\033[31m";
    const COLOR_RESET = "\033[0m";

    const HIGHLIGHT_INFO = 'info';
    const HIGHLIGHT_VALUE = 'value';
    const HIGHLIGHT_ERROR = 'error';

    private $highlightMap = [
        self::HIGHLIGHT_VALUE => self::COLOR_GREEN,
        self::HIGHLIGHT_INFO => self::COLOR_YELLOW,
        self::HIGHLIGHT_ERROR => self::COLOR_RED,
    ];

    /**
     * @var string Exists only for testing.
     */
    private $eol = PHP_EOL;

    /**
     * @var bool
     */
    private $supportsColor;

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

    public function __construct($inputStream = STDIN, $outputStream = STDOUT, $errorStream = STDERR)
    {
        $this->inputStream = $inputStream;
        $this->outputStream = $outputStream;
        $this->errorStream = $errorStream;
        $this->supportsColor = $this->detectColorCapabilities($outputStream);
    }

    public function readLine(string $name, string $text = 'Please enter a value for'): string
    {
        fwrite($this->outputStream, $this->colorize(sprintf('%s <info>%s</info>: ', $text, $name)));
        return trim(fread($this->inputStream, 1024), "\n");
    }

    /**
     * Colorize a string for use with the terminal.
     *
     * Takes strings formatted as `<key>string</key>` and formats them per the
     * $highlightMap; if color support is disabled, simply removes the formatting
     * tags.
     *
     * @param string $string
     * @return string
     */
    public function colorize(string $string): string
    {
        $reset = $this->supportsColor ? self::COLOR_RESET : '';
        foreach ($this->highlightMap as $key => $color) {
            $pattern = sprintf('#<%s>(.*?)</%s>#s', $key, $key);
            $color = $this->supportsColor ? $color : '';
            $string = preg_replace($pattern, $color . '$1' . $reset, $string);
        }
        return $string;
    }

    /**
     * @param string $string
     * @param bool $colorize Whether or not to colorize the string
     * @return void
     */
    public function write(string $string, bool $colorize = true): void
    {
        if ($colorize) {
            $string = $this->colorize($string);
        }
        $string = $this->formatNewlines($string);

        fwrite($this->outputStream, $string);
    }

    /**
     * @param string $string
     * @param bool $colorize Whether or not to colorize the line
     * @return void
     */
    public function writeLine(string $string, bool $colorize = true): void
    {
        $this->write($string . $this->eol, $colorize);
    }

    /**
     * @param string $string
     * @param bool $colorize Whether or not to colorize the line
     * @return void
     */
    public function writeErrorLine(string $string, bool $colorize = true): void
    {
        $string .= $this->eol;

        if ($colorize) {
            $string = $this->colorize($string);
        }
        $string = $this->formatNewlines($string);

        fwrite($this->errorStream, $string);
    }

    /**
     * Emit an error message.
     *
     * Wraps the message in `<error></error>`, and passes it to `writeLine()`,
     * using STDERR as the resource; emits an additional empty line when done,
     * also to STDERR.
     *
     * @param string $message
     * @return void
     */
    public function writeErrorMessage(string $message): void
    {
        $this->writeErrorLine(sprintf('<error>%s</error>', $message), true);
        $this->writeErrorLine('', false);
    }

    /**
     * @param resource $resource
     * @return bool
     */
    private function detectColorCapabilities($resource = STDOUT): bool
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            // Windows
            return false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        try {
            return function_exists('posix_isatty') && posix_isatty($resource);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ensure newlines are appropriate for the current terminal.
     *
     * @param string
     * @return string
     */
    private function formatNewlines(string $string): string
    {
        $string = str_replace($this->eol, "\0PHP_EOL\0", $string);
        $string = preg_replace("/(\r\n|\n|\r)/", $this->eol, $string);
        return str_replace("\0PHP_EOL\0", $this->eol, $string);
    }
}
