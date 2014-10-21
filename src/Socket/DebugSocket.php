<?php

namespace Aztech\Socket\Socket;

use Aztech\Socket\Socket as SocketInterface;
use Aztech\Socket\Socket\SocketReader;
use Aztech\Socket\Socket\SocketWriter;
use Aztech\Util\Text;
use Aztech\Socket\ByteOrder;

class DebugSocket implements SocketInterface
{

    private $socket;

    public function __construct(SocketInterface $socket)
    {
        $this->socket = $socket;
    }

    public function getReader()
    {
        return new SocketReader($this, ByteOrder::LITTLE_ENDIAN);
    }

    public function getWriter()
    {
        return new SocketWriter($this, ByteOrder::LITTLE_ENDIAN);
    }

    public function readTimeout($bytes, $timeout = null)
    {
        return $this->socket->readTimeout($bytes, $timeout);
    }

    public function readRaw($bytes)
    {
        $read = $this->socket->getReader()->read($bytes);

        echo PHP_EOL . time() . ' : Socket read ' . strlen($read) . ' bytes of ' . $bytes . ' requested : ' . PHP_EOL;
        Text::dumpHex($read);

        return $read;
    }

    public function writeTimeout($buffer, $timeout = null)
    {
        return $this->socket->writeTimeout($buffer, $timeout);
    }

    public function writeRaw($buffer)
    {
        $this->socket->getWriter()->write($buffer);

        echo PHP_EOL . time() . ' : Socket wrote ' . strlen($buffer) . ' bytes : ' . PHP_EOL;
        Text::dumpHex($buffer);
    }
}
