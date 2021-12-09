<?php

function isLowPoint( array $grid, int $i, int $j ) : bool {
    return $grid[$i][$j] < (isset($grid[$i-1][$j])?$grid[$i-1][$j]:10) &&
        $grid[$i][$j] < (isset($grid[$i][$j-1])?$grid[$i][$j-1]:10) &&
        $grid[$i][$j] < (isset($grid[$i+1][$j])?$grid[$i+1][$j]:10) &&
        $grid[$i][$j] < (isset($grid[$i][$j+1])?$grid[$i][$j+1]:10);
}

function day09a( array $grid ) {
    $res = 0;
    for ( $i = 0; $i < count($grid); $i++) {
        for ( $j = 0; $j < count($grid[$i]); $j++) {
            if ( isLowPoint( $grid, $i, $j ) ) {
                $res += $grid[$i][$j] + 1;
            }
        }
    }
    return $res;
}

function basinSize( array $grid, int $x, int $y ) : int {
    $visited = [];
    for ( $i = 0; $i < count($grid); $i++) {
        $visited[] = [];
        for ($j = 0; $j < count($grid[$i]); $j++) {
            $visited[$i][] = 0;
        }
    }
    $queue = [ [$x, $y] ];
    while ( count( $queue ) > 0) {
        $u = array_pop( $queue );
        $i = $u[0];
        $j = $u[1];
        if ( $visited[$i][$j] === 1) {
            continue;
        }
        if ( isset($grid[$i-1][$j]) && $grid[$i-1][$j] < 9 ) {
            $queue[] = [$i-1,$j];
        }
        if ( isset($grid[$i][$j-1]) && $grid[$i][$j-1] < 9 ) {
            $queue[] = [$i,$j-1];
        }
        if ( isset($grid[$i+1][$j]) && $grid[$i+1][$j] < 9 ) {
            $queue[] = [$i+1,$j];
        }
        if ( isset($grid[$i][$j+1]) && $grid[$i][$j+1] < 9 ) {
            $queue[] = [$i,$j+1];
        }
        $visited[$u[0]][$u[1]] = 1;
    }
    $size = 0;
    foreach ( $visited as $line ) {
        $size += array_sum( $line );
    }
    return $size;
}

function day09b( array $grid ) {
    $sizes = [];
    for ( $i = 0; $i < count($grid); $i++) {
        for ( $j = 0; $j < count($grid[$i]); $j++) {
            if ( isLowPoint( $grid, $i, $j ) ) {
                $sizes[] = basinSize( $grid, $i, $j );
            }
        }
    }
    rsort( $sizes );
    return ( $sizes[0] * $sizes[1] * $sizes[2]);
}

function day09( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);
    $grid = [];
    foreach ( $lines as $line ) {
        if ( strlen( $line ) > 0 ) {
            $grid[] = [];
            $vents = str_split( $line );
            foreach ( $vents as $vent ) {
                $grid[ count($grid) - 1][] = intval ( $vent );
            }
        }
    }
    $day09a = day09a( $grid );
    $day09b = day09b( $grid );
    print ( $day09a . PHP_EOL);
    print ( $day09b . PHP_EOL);
}

day09( '../data/day09.txt');