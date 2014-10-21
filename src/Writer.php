<?php

namespace Aztech\Socket;

interface Writer
{

    public function write($buffer);

    /**
     * Shortcut for write(chr($char)).
     *
     * @param int $char
     */
    public function writeChr($char);

    public function writeInt8($value);

    public function writeInt16($value);

    public function writeInt32($value);

    public function writeInt64($value);

    public function writeUInt8($value);

    public function writeUInt16($value);

    public function writeUInt32($value);

    public function writeUInt64($value);
}
