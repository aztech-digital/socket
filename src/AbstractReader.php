<?php

namespace Aztech\Socket;

abstract class AbstractReader implements Reader
{

    private $byteOrder;

    public function __construct($byteOrder = ByteOrder::LITTLE_ENDIAN)
    {
        $this->byteOrder = $byteOrder;
    }

    private function unsignedToSignedInt($value, $size, $maxValue)
    {
        $lmb = $value >> ($size * 8) - 1;

        if ($lmb == 1) {
            $value = bcsub($value, $maxValue);
        }

        return $value;
    }

    public function readInt8()
    {
        $value = $this->readUInt8();

        return $this->unsignedToSignedInt($value, DataTypes::INT8_SZ, DataTypes::INT8_MAX);
    }

    public function readInt16()
    {
        $value = $this->readUInt16();

        return $this->unsignedToSignedInt($value, DataTypes::INT16_SZ, DataTypes::INT16_MAX);
    }

    public function readInt32()
    {
        $value = $this->readUInt32();

        return $this->unsignedToSignedInt($value, DataTypes::INT32_SZ, DataTypes::INT32_MAX);
    }

    public function readInt64()
    {
        $value = $this->readUInt64();

        return $this->unsignedToSignedInt($value, DataTypes::INT64_SZ, DataTypes::INT64_MAX);
    }

    public function readUInt8()
    {
        $value = $this->read(DataTypes::INT8_SZ);

        return hexdec(bin2hex($value));
    }

    public function readUInt16()
    {
        $format = ByteOrder::getPackFormat($this->byteOrder, ByteOrder::FMT_UINT_16);
        $unpacked = unpack($format, $this->read(DataTypes::INT16_SZ));

        return reset($unpacked);
    }

    public function readUInt32()
    {
        $format = ByteOrder::getPackFormat($this->byteOrder, ByteOrder::FMT_UINT_32);
        $unpacked = unpack($format, $this->read(DataTypes::INT32_SZ));

        return reset($unpacked);
    }

    public function readUInt64()
    {
        throw new \InvalidArgumentException();
    }

    public function skip($count)
    {
        $this->read($count);
    }

}
