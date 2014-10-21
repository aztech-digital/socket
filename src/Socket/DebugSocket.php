<?php

namespace Aztech\Socket\Socket;

use Aztech\Socket\Socket as SocketInterface;
use Aztech\Socket\Socket\SocketReader;
use Aztech\Socket\Socket\SocketWriter;
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
        self::dumpHex($read);

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
        self::dumpHex($buffer);
    }

    public static function dumpHex($bytes, $caption = '')
    {
        if ($caption) {
            echo PHP_EOL . 'HEXDUMP -- ' . $caption . PHP_EOL;
        }

        $unpacked = unpack('H*', $bytes);
        $hexDump = reset($unpacked);

        $inc = 32;

        for ($i = 0; $i < strlen($hexDump); $i += $inc) {
            $buffer  = str_pad($i / 2, 4, '0', STR_PAD_LEFT) . '-';
            $buffer .= str_pad((($i + $inc) / 2) - 1, 4, '0', STR_PAD_LEFT);
            $buffer .= ' [ ';

            for ($j = $i; $j < $i + $inc && $j < strlen($hexDump); $j += 2) {
                $buffer .= substr($hexDump, $j, 2);

                if ($j + 2 == $i + ($inc / 2)) {
                    $buffer .= ' ';
                }

                $buffer .= ' ';
            }

            $buffer = str_pad($buffer, 61, ' ');
            $buffer .= '] ';

            echo $buffer;

            $packedHex = pack('H*', substr($hexDump, $i, $inc));

            for ($j = 0; $j < strlen($packedHex); $j++) {
                $ord = ord($packedHex[$j]);
                if ($ord < 32 || $ord > 126) {
                    echo '.';
                }
                else {
                    echo $packedHex[$j];
                }
            }

            echo PHP_EOL;
        }
    }
}
