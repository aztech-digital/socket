<?php

namespace Aztech\Socket\Socket;

use Aztech\Socket\AbstractReader;
use Aztech\Socket\Socket as SocketInterface;

class SocketReader extends AbstractReader
{

    private $socket;

    private $offset = 0;

    public function __construct(SocketInterface $socket, $byteOrder)
    {
        $this->socket = $socket;

        parent::__construct($byteOrder);
    }

    /**
     * Reads a number of bytes.
     * @param int $length Number of bytes to read, or 0 to read to end. If length is greater than the available characters, it is truncated to that count.
     * @return string An empty if no bytes are available, or the read bytes.
     */
    public function read($length = 0)
    {
        if ($length == 0) {
            $buffer = '';

            while (($read = $this->socket->readRaw(1)) && $read != '') {
                $buffer .= $read;
            }

            return $buffer;
        }

        $this->offset += $length;

        return $this->socket->readRaw($length);
    }

    public function getReadByteCount()
    {
        return $this->offset;
    }

    public function resetReadCount()
    {
        $this->offset = 0;
    }
}
