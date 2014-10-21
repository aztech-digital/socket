<?php

namespace Aztech\Socket;

interface Reader
{

    public function read($length = 0);

    public function readInt8();

    public function readInt16();

    public function readInt32();

    public function readInt64();

    public function readUInt8();

    public function readUInt16();

    public function readUInt32();

    public function readUInt64();

    public function getReadByteCount();

}
