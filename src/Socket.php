<?php

namespace Aztech\Socket;

interface Socket
{

    /**
     *
     * @return SocketReader
     */
    public function getReader();

    /**
     *
     * @return SocketWriter
     */
    public function getWriter();

    /**
     *
     * @param int $bytes
     * @return string
     */
    public function readRaw($bytes);

    /**
     *
     * @param int $bytes
     * @return string
     */
    public function readTimeout($bytes, $timeout = null);

    /**
     *
     * @param string $buffer
     * @return void
     */
    public function writeRaw($buffer);


    /**
     *
     * @param string $buffer
     * @return void
     */
    public function writeTimeout($buffer, $timeout = null);
}
