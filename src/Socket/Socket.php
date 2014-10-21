<?php

namespace Aztech\Socket\Socket;

use Aztech\Socket\Socket as SocketInterface;
use Aztech\Socket\Socket\SocketReader;
use Aztech\Socket\Socket\SocketWriter;
use Aztech\Socket\ByteOrder;

class Socket implements SocketInterface
{

    /**
     *
     * @var bool
     */
    private $autoConnect;

    /**
     *
     * @var string
     */
    private $host;

    /**
     *
     * @var bool
     */
    private $isBinary = true;

    /**
     *
     * @var bool
     */
    private $isClosed = false;

    /**
     *
     * @var bool
     */
    private $isConnected = false;

    /**
     *
     * @var int
     */
    private $lastResult = 0;

    /**
     *
     * @var string|int
     */
    private $port;

    /**
     *
     * @var resource
     */
    private $socket;

    /**
     *
     * @param string $host
     * @param string|int $port
     * @throws \RuntimeException
     */
    public function __construct($host, $port, $autoConnect = true)
    {
        $this->host = $host;
        $this->port = $port;
        $this->autoConnect = $autoConnect;
    }

    /**
     * Close connection if it is still open
     */
    public function __destruct()
    {
        if ($this->isAlive()) {
            $this->close();
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Socket\Socket::getReader()
     */
    public function getReader()
    {
        return new SocketReader($this, ByteOrder::LITTLE_ENDIAN);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Socket\Socket::getWriter()
     */
    public function getWriter()
    {
        return new SocketWriter($this, ByteOrder::LITTLE_ENDIAN);
    }

    /**
     * Checks if the socket is still connected.
     *
     * @return boolean
     */
    public function isAlive()
    {
        return ($this->socket !== null && @socket_get_option($this->socket, SOL_SOCKET, SO_ERROR) !== false);
    }

    /**
     * Returns true if the socket reads binary strings instead of regular strings.
     *
     * @return boolean
     */
    public function isBinary()
    {
        return $this->isBinary;
    }

    public function readTimeout($bytes, $timeout = null)
    {
        if ($timeout !== null && $this->select(0, $timeout) === 0) {
            throw new SocketTimeoutException('Socket did not receive any data within alloted time.');
        }

        return $this->readRaw($bytes);
    }

    public function readRaw($bytes)
    {
        $this->validateConnection();

        $mode = $this->isBinary ? PHP_BINARY_READ : PHP_NORMAL_READ;
        $received = @socket_read($this->socket, $bytes, $mode);

        $this->updateLastResult('socket_read', $received);

        return $received;
    }

    public function toggleBinaryMode($enabled)
    {
        $this->isBinary = (bool) $enabled;
    }

    public function writeTimeout($buffer, $timeout = null)
    {
    if ($timeout !== null && $this->select(0, $timeout) === 0) {
            throw new SocketTimeoutException('Socket did not write any data within alloted time.');
        }

        return $this->writeRaw($bytes);
    }

    public function writeRaw($buffer)
    {
        $this->validateConnection();

        $written = @socket_write($this->socket, $buffer);
        $this->updateLastResult('socket_write', $written);

        return $written;
    }

    protected function close()
    {
        @socket_close($this->socket);
        $this->isClosed = true;
    }

    protected function connect()
    {
        if ($this->isClosed) {
            throw new \SocketException("Cannot reconnect a closed socket.");
        }

        if ($this->isAlive()) {
            return;
        }

        if ($this->socket === null) {
            $this->createSocket();
        }

        $result = @socket_connect($this->socket, $this->host, $this->port);
        $this->updateLastResult('socket_connect', $result);

        $this->isConnected = true;
    }

    protected function createSocket()
    {
        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->updateLastResult('socket_create', $this->socket);
    }

    /**
     *
     * @return string
     */
    protected function getErrorFromCode($errorCode)
    {
        return @socket_strerror($errorCode);
    }

    protected function getLastResultCode()
    {
        return $this->lastResult;
    }

    /**
     *
     * @return resource A valid and connected Socket resource.
     */
    protected function getSocket()
    {
        return $this->socket;
    }

    protected function select($mode, $timeout)
    {
        $this->validateConnection();

        $read = $mode == 0 ? [ $this->socket ] : [];
        $write = $mode == 1 ? [ $this->socket ] : [];
        $errs = [];

        $result = @socket_select($read, $write, $errs, max(0, $timeout));
        $this->updateLastResult('socket_select', $result === 1);

        return $result;
    }

    protected function updateLastResult($opName, $result)
    {
        $this->lastResult = 0;

        if ($result === false) {
            $this->lastResult = @socket_last_error($this->socket);

            if ($this->lastResult !== 0) {
                $format = 'Operation \'%s\' failed with result %d : %s.';
                $errorCode = $this->lastResult;
                $errorMessage = $this->getErrorFromCode($errorCode);
                $message = sprintf($format, $opName, $errorCode, $errorMessage);

                throw new SocketException($message, $this->lastResult, null);
            }
        }

        @socket_clear_error($this->socket);
    }

    protected function validateConnection()
    {
        if ($this->isClosed) {
            throw new SocketException('Socket is closed.');
        }

        if (! $this->isConnected && $this->autoConnect) {
            $this->connect();
        }

        if (! $this->isConnected) {
            throw new SocketException('Socket is not connected.');
        }
    }
}
