<?php

function simulateOctopedes ( array &$grid ) {
    $queue = [];
    $flashed = [];

    for ( $i = 0; $i < count( $grid ); $i++ ) {
        $flashed[] = [];
        for ( $j = 0; $j < count( $grid[$i] ); $j++ ) {
            $flashed[$i][] = false;
            $grid[$i][$j]++;
            if ( $grid[$i][$j] > 9) {
                $queue[] = [$i, $j];
            }
        }
    }

    $flashes = 0;
    while ( count( $queue ) > 0 ) {
        $flash = array_shift( $queue );
        $i = $flash[0];
        $j = $flash[1];
        if ( $flashed[$i][$j] ) {
            continue;
        }
        $flashes++;
        for ( $k = -1; $k <= 1; $k++ ) {
            for ( $l = -1; $l <= 1; $l++ ) {
                if (!isset( $grid[$i+$k][$j+$l])) {
                    continue;
                }
                if ( $k == 0 && $l == 0 ) {
                    continue;
                }
                if ( $flashed[$i+$k][$j+$l] ) {
                    continue;
                }
                $grid[$i+$k][$j+$l]++;
                if ( $grid[$i+$k][$j+$l] > 9 ) {
                    $queue[] = [$i+$k, $j+$l];
                }
            }
        }
        $grid[$i][$j] = 0;
        $flashed[$i][$j] = true;
    }

    return $flashes;
}

function day11a( array $grid ) : int {
    $res = 0;
    for ( $i = 0; $i < 100; $i++) {
        $res += simulateOctopedes( $grid );
    }
    return $res;
}

function day11b( array $grid ) : int {
    $i = 0;
    while (true) {
        $i++;
        if (simulateOctopedes( $grid ) == 100) {
            return $i;
        }
    }
}

function day11( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $grid = [];
    foreach ( $lines as $line ) {
        if ( strlen( $line ) > 0 ) {
            $grid[] = [];
            $octopedes = str_split( $line );
            foreach ( $octopedes as $octopus ) {
                $grid[ count($grid) - 1][] = intval ( $octopus );
            }
        }
    }

    $day11a = day11a( $grid );
    $day11b = day11b( $grid );
    print ( $day11a . PHP_EOL);
    print ( $day11b . PHP_EOL);
}

day11( '../data/day11.txt');