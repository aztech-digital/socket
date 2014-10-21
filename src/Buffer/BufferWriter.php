<?php

namespace Aztech\Socket\Buffer;

use Aztech\Util\Text;
use Aztech\Socket\AbstractWriter;

class BufferWriter extends AbstractWriter
{
    private $buffer = '';

    public function write($buffer)
    {
        $this->buffer .= $buffer;
    }

    public function getBuffer()
    {
        return $this->buffer;
    }

    public function getBufferSize()
    {
        return strlen($this->buffer);
    }
}
