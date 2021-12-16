<?php

class Packet
{
    public $version;
    public $type;
    public $data = '';

    public $subPackets = [];
}

function decodePacket(string $bincode, int &$i)
{
    $packet = new Packet();
    $packet->version = bindec(substr($bincode, $i, 3));
    $packet->type = bindec(substr($bincode, $i+3, 3));
    $i = $i + 6;

    if ($packet->type == 4) {
        while (true) {
            $packet->data .= substr( $bincode,$i + 1, 4);
            if ($bincode[$i] === "0") {
                $i = $i+5;
                break;
            }
            $i = $i + 5;
        }
    } else {
        if ( $bincode[$i] == 0) {
            $bitLength = bindec(substr($bincode, $i+1, 15));
            $i += 16;
            $j = 0;
            $packet->subPackets = decodePackets( substr($bincode, $i, $bitLength), $j );
            $i = $i + $bitLength;
        } else {
            $numPackets = bindec(substr($bincode, $i+1, 11) );
            $i += 12;
            for ( $j = 0; $j < $numPackets; $j++ ) {
                $packet->subPackets[] = decodePacket( $bincode, $i );
            }
        }
    }
    return $packet;
}

function decodePackets(string $bincode ): array
{
    $packets = [];

    $i = 0;
    while ($i < strlen($bincode) - 6) {
        $packets[] = decodePacket($bincode, $i);
    }

    return $packets;
}

function sumPacketVersions( array $packets ) : int {
    $res = 0;
    foreach ( $packets as $packet ) {
        $res = $res + $packet->version + sumPacketVersions( $packet->subPackets );
    }
    return $res;
}

function day16a(string $hexcode): int
{
    $bincode = '';
    foreach (str_split($hexcode) as $char) {
        $bin = str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        $bincode .= $bin;
    }
    $packets = decodePackets($bincode);

    return sumPacketVersions( $packets );
}

function computeOp( Packet $packet ) : int {
    switch ( $packet->type) {
        case 0:
            $res = 0;
            foreach ( $packet->subPackets as $subPacket ) {
                $res += computeOp( $subPacket );
            }
            return $res;
        case 1:
            $res = 1;
            foreach ( $packet->subPackets as $subPacket ) {
                $res *= computeOp( $subPacket );
            }
            return $res;
        case 2:
            $res = computeOp($packet->subPackets[0]);
            foreach ( $packet->subPackets as $subPacket ) {
                $subRes = computeOp($subPacket);
                if ($subRes < $res) {
                    $res = $subRes;
                }
            }
            return $res;
        case 3:
            $res = computeOp($packet->subPackets[0]);
            foreach ( $packet->subPackets as $subPacket ) {
                $subRes = computeOp($subPacket);
                if ($subRes > $res) {
                    $res = $subRes;
                }
            }
            return $res;
        case 4:
            return bindec( $packet->data );
        case 5:
            if ( computeOp($packet->subPackets[0] ) > computeOp($packet->subPackets[1] ) ) {
                return 1;
            }
            return 0;
        case 6:
            if ( computeOp($packet->subPackets[0] ) < computeOp($packet->subPackets[1] ) ) {
                return 1;
            }
            return 0;
        case 7:
            if ( computeOp($packet->subPackets[0] ) === computeOp($packet->subPackets[1] ) ) {
                return 1;
            }
            return 0;
    }
    throw new Exception("eeeh.");
}

function day16b(string $hexcode): int
{
    $bincode = '';
    foreach (str_split($hexcode) as $char) {
        $bin = str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        $bincode .= $bin;
    }
    $packets = decodePackets($bincode);

    return computeOp( $packets[0] );
}

function day16(string $filename): void
{
    $strcontent = file_get_contents($filename);
    $lines = explode("\n", $strcontent);

    $day16a = day16a($lines[0]);
    $day16b = day16b($lines[0]);
    print ($day16a . PHP_EOL);
    print ($day16b . PHP_EOL);
}

day16('../data/day16.txt');