<?php

namespace Aztech\Socket;

abstract class AbstractWriter implements Writer
{

    private $byteOrder;

    public function __construct($byteOrder = ByteOrder::MACHINE)
    {
        $this->byteOrder = $byteOrder;
    }

    private function signedToUnsignedInt($value, $maxValue)
    {
        if ($value > $maxValue) {
            $value = $maxValue;
        }

        return $value;
    }

    public function setByteOrder($order)
    {
        $this->byteOrder = $order;
    }

    public function writeChr($char)
    {
        return $this->write(chr($char));
    }

    public function writeInt8($value)
    {
        return $this->writeUInt8($this->signedToUnsignedInt($value, DataTypes::INT8_MAX));
    }

    public function writeInt16($value)
    {
        return $this->writeUInt16($this->signedToUnsignedInt($value, DataTypes::INT64_MAX));
    }

    public function writeInt32($value)
    {
        return $this->writeUInt32($this->signedToUnsignedInt($value, DataTypes::INT32_MAX));
    }

    public function writeInt64($value)
    {
        return $this->writeUInt64($this->signedToUnsignedInt($value, DataTypes::INT64_MAX));
    }

    public function writeUInt8($value)
    {
        $format = ByteOrder::getPackFormat(ByteOrder::BIG_ENDIAN, ByteOrder::FMT_UINT_16);

        return $this->write(hex2bin(substr(bin2hex(pack($format, $value)), 2)));
    }

    public function writeUInt16($value)
    {
        $format = ByteOrder::getPackFormat($this->byteOrder, ByteOrder::FMT_UINT_16);

        return $this->write(pack($format, $value));
    }

    public function writeUInt32($value)
    {
        $format = ByteOrder::getPackFormat($this->byteOrder, ByteOrder::FMT_UINT_32);

        return $this->write(pack($format, $value));
    }

    public function writeUInt64($value)
    {
        if (is_string($value)) {
            if (substr($value, 0, 2) == '0x') {
                $value = substr($value, 2);
            }
        }
        else {
            $value = dechex($value);
        }

        $value = str_pad($value, "0", DataTypes::INT64_SZ * 2, STR_PAD_LEFT);

        $upper = substr($value, 0, 8);
        $lower = substr($value, 8);

        if ($this->byteOrder == ByteOrder::BIG_ENDIAN && $upper != $lower) {
            $upper = $upper ^ $lower;
            $lower = $upper ^ $lower;
            $upper = $upper ^ $lower;
        }

        $this->writeUInt32(hexdec($lower));
        $this->writeUInt32(hexdec($upper));
    }

    public function writeHex($value, $size = 0)
    {
        $format = ($this->byteOrder == ByteOrder::LITTLE_ENDIAN) ? 'H' : 'h';

        if ($size == 0) {
            $size = strlen($value);
        }

        $format .= $size;

        return $this->write(pack($format , $value));
    }
}
