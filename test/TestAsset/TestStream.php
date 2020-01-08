<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2020 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\TestAsset;

/**
 * Helper class to catch outputs and inputs to emulate CLI in unit tests
 *
 * @see http://no2.php.net/manual/en/stream.streamwrapper.example-1.php
 */
class TestStream
{
    private $position;
    private $varname;

    public static $data = [];
    public static $inputStack = [];

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->varname = parse_url($path)['host'];
        $this->position = 0;
        self::$data[$this->varname] = '';

        return true;
    }

    public function stream_read($count)
    {
        if (!empty(self::$inputStack)) {
            return array_shift(self::$inputStack);
        }
        return '';
    }

    public function stream_write($data)
    {
        $left = substr(self::$data[$this->varname], 0, $this->position);
        $right = substr(self::$data[$this->varname], $this->position + strlen($data));
        self::$data[$this->varname] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        return $this->position >= strlen(self::$data[$this->varname]);
    }

    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen(self::$data[$this->varname]) && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen(self::$data[$this->varname]) + $offset >= 0) {
                    $this->position = strlen(self::$data[$this->varname]) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    public function stream_metadata($path, $option, $var)
    {
        if ($option == STREAM_META_TOUCH) {
            $url = parse_url($path);
            $varname = $url['host'];
            if (!isset(self::$data[$varname])) {
                self::$data[$varname] = '';
            }
            return true;
        }
        return false;
    }

}
