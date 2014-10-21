<?php

namespace Aztech\Socket\Buffer;

use Aztech\Socket\AbstractReader;

class BufferReader extends AbstractReader
{

    private $data;

    private $offset = 0;

    private $throw = false;

    public function __construct($dataBuffer, $throw = false)
    {
        $this->data = $dataBuffer;
        $this->throw = (bool) $throw;
    }

    /**
     * Reads a number of bytes.
     * @param int $length Number of bytes to read, or 0 to read to end. If length is greater than the available characters, it is truncated to that count.
     * @return false|string False if no bytes are available, or the read bytes.
     */
    public function read($length = 0)
    {
        if ($this->offset >= strlen($this->data)) {
            if ($this->throw === true) {
                throw new \RuntimeException('EOF');
            }

            return false;
        }

        if ($length <= 0 || $this->offset + $length >= strlen($this->data)) {
            $length = strlen(substr($this->data, $this->offset));
        }

        $offset = $this->offset;
        $this->offset += $length;

        return substr($this->data, $offset, $length);
    }

    public function readFrom($offset, $length = 0)
    {
        if ($offset >= strlen($this->data)) {
            return false;
        }

        if ($length <= 0 || $offset + $length >= strlen($this->data)) {
            return substr($this->data, $offset);
        }

        return substr($this->data, $offset, $length);
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getReadByteCount()
    {
        return $this->offset;
    }

    public function getBuffer()
    {
        return $this->data;
    }

    public function getBufferSize()
    {
        return strlen($this->data);
    }

    public function reset()
    {
        $this->offset = 0;
    }

    public function seek($offset)
    {
        $this->offset = $offset;
    }

    public function readTo($hex, $exclude = false)
    {
        $buffer = '';

        while (substr(bin2hex($buffer), - strlen($hex)) !== $hex && $this->offset < $this->getBufferSize()) {
            $buffer .= $this->read(2);
        }

        if ($exclude) {
            $buffer = substr($buffer, 0, -2);
        }

        return $buffer;
    }
}
