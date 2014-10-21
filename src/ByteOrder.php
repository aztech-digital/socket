<?php

namespace Aztech\Socket;

class ByteOrder
{

    const MACHINE = 0;

    const BIG_ENDIAN = 1;

    const LITTLE_ENDIAN = 2;

    const FMT_INT_32 = 0;

    const FMT_UINT_32 = 1;

    const FMT_INT_16 = 2;

    const FMT_UINT_16 = 3;

    private static $formats = [
        self::FMT_INT_16 => [
            self::MACHINE => 's'
        ],
        self::FMT_INT_32 => [
            self::MACHINE => 'l'
        ],
        self::FMT_UINT_16 => [
            self::MACHINE => 'S',
            self::BIG_ENDIAN => 'n',
            self::LITTLE_ENDIAN => 'v'
        ],
        self::FMT_UINT_32 => [
            self::MACHINE => 'L',
            self::BIG_ENDIAN => 'N',
            self::LITTLE_ENDIAN => 'V'
        ]
    ];

    /**
     *
     * @param int $byteOrder
     * @param int $format
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function getPackFormat($byteOrder, $format)
    {
        if ($byteOrder == ByteOrder::MACHINE) {
            // Detect machine byte order
            $machineValue = pack('L', 0xaabbccdd);
            $littleEndianValue = pack('V', 0xaabbccdd);

            $byteOrder = ($machineValue == $littleEndianValue) ? ByteOrder::LITTLE_ENDIAN : ByteOrder::BIG_ENDIAN;
        }

        if (! array_key_exists($format, self::$formats)) {
            throw new \InvalidArgumentException('Unknown format');
        }

        if (! array_key_exists($byteOrder, self::$formats[$format])) {
            throw new \RuntimeException('Byte-order - format combination not available.');
        }

        return self::$formats[$format][$byteOrder];
    }
}
