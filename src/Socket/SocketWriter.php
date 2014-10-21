<?php

namespace Aztech\Socket\Socket;

use Aztech\Socket\AbstractWriter;
use Aztech\Socket\Socket as SocketInterface;

class SocketWriter extends AbstractWriter
{

    private $socket;

    public function __construct(SocketInterface $socket, $byteOrder)
    {
        $this->socket = $socket;

        parent::__construct($byteOrder);
    }

    public function write($buffer)
    {
        return $this->socket->writeRaw($buffer);
    }

}
