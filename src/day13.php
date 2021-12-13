<?php

use Ds\Set;

function foldAlong( Set $points, string $inst) : Set {
    $newPoints = new Set();
    $toks = explode('=', $inst );
    $coord = -1;
    if ( $toks[0] === 'x') {
        $coord = 0;
    } else {
        $coord = 1;
    }

    $fold = intval( $toks[1] );

    foreach ( $points as $point) {
        if ( $point[$coord] > $fold ) {
            $newPoint = $point;
            $newPoint[$coord] = $point[$coord] - 2 * ( $point[$coord] - $fold );
            $newPoints->add($newPoint);
        } else {
            $newPoints->add($point);
        }
    }
    return $newPoints;
}

function day13a(Set $points, array $instr ) : int {
    $points = foldAlong( $points, $instr[0] );
    return count( $points );
}

function day13b( Set $points, array $instr ) : int {
    foreach ( $instr as $i ) {
        $points = foldAlong( $points, $i );
    }
    $maxX = 0;
    $maxY = 0;
    foreach ( $points as $point ) {
        if ( $point[0] > $maxX ) {
            $maxX = $point[0];
        }
        if ( $point[1] > $maxY ) {
            $maxY = $point[1];
        }
    }

    for ( $y = 0; $y <= $maxY; $y++) {
        for ( $x = 0; $x <= $maxX; $x++ ) {
            if ( $points->contains([$x, $y])) {
                print('#');
            } else {
                print(' ');
            }
        }
        print("\n");
    }

    return 0;
}

function day13( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $points = new Set();
    $instr = [];

    foreach ( $lines as $line ) {
        if (strlen($line) === 0) {
            continue;
        }
        if ( str_contains( $line, ',') ) {
            $coords = explode(',', $line );
            $points->add([intval($coords[0]), intval( $coords[1])]);
        } else {
            $instr[] = explode( ' ', $line )[2];
        }
    }

    $day13a = day13a( $points, $instr );
    $day13b = day13b( $points, $instr );
    print ( $day13a . PHP_EOL);
    print ( $day13b . PHP_EOL);
}

day13( '../data/day13.txt');
