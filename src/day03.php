<?php

function day03( string $filename ) : void {
    $strcontent = file_get_contents($filename );
    $lines = explode("\n", $strcontent);
    $day03a = day03a( $lines );
    print ( $day03a . PHP_EOL);
    $day03b = day03b( $lines );
    print ( $day03b . PHP_EOL);
}

function binMajority ( array $lines, int $i ): string {
    $zeroes = 0;
    $ones = 0;
    foreach ( $lines as $line ) {
        if ( $line[$i] == '0' ) {
            $zeroes++;
        } else {
            $ones++;
        }
    }
    if ( $zeroes > $ones ) {
        return '0';
    }
    return '1';
}

function day03a( array $lines ) : int {
    $gammaStr = '';
    $epsilonStr = '';
    for ( $i = 0; $i < strlen($lines[0]); $i++ ) {
        $maj = binMajority( $lines, $i );
        $gammaStr .= $maj;
        $epsilonStr .= strval( 1 - intval( $maj ) );
    }
    return bindec( $gammaStr ) * bindec( $epsilonStr );
}

function day03b( array $lines ) : int {
    $oxLines = $lines;
    $co2Lines = $lines;
    $size = strlen($lines[0]);

    for ( $i = 0; $i < $size; $i++ ) {
        if ( count( $oxLines) > 1 ) {
            $maj = binMajority( $oxLines, $i );
            $oxLines = array_filter( $oxLines, function( $str ) use ( $i, $maj ) {
                return $str[$i] === $maj;
            } );
        }
    }

    for ( $i = 0; $i < $size; $i++ ) {
        if ( count( $co2Lines) > 1 ) {
            $min = strval( 1 - intval( binMajority( $co2Lines, $i ) ) );
            $co2Lines = array_filter( $co2Lines, function( $str ) use ( $i, $min ) {
                return $str[$i] === $min;
            } );
        }
    }

    // Turns out, when you filter, PHP also keeps the index in int-indexed arrays. Fun!
    return bindec( $oxLines[ array_key_first( $oxLines) ] ) * bindec( $co2Lines[ array_key_first( $co2Lines) ] );
}

day03( '../data/day03.txt' );