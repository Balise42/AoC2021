<?php

use Ds\Map;

function day06a(array $fishes ) : int {
    $sims = [];
    for ( $i = 0; $i <= 5; $i++ ) {
        $sims[] = simulate( $i, 80);
    }
    $res = 0;
    foreach ( $fishes as $fish ) {
        $res = $res + $sims[$fish];
    }
    return $res;
}

function day06b( array $fishes ) : int {
    $sims = [];
    $memoFish = new Map();
    for ( $i = 0; $i <= 5; $i++ ) {
        $sims[] = simulateFaster( $i, 256, $memoFish );
    }
    $res = 0;
    foreach ( $fishes as $fish ) {
        $res = $res + $sims[$fish];
    }
    return $res;
}

function simulate( int $initFish, int $days ) {
    $fishes = [ $initFish ];
    for ( $day = 1; $day <= $days; $day++ ) {
        for ($i = 0; $i < count( $fishes ); $i++ ) {
            $fishes[ $i ]--;
            if ( $fishes[ $i ] == -1 ) {
                // we set it to 9 because we'll also iterate on the fishes at the end
                $fishes[] = 9;
                $fishes[ $i ] = 6;
            }
        }
    }
    return count( $fishes );
}

function simulateFaster( int $initFish, int $days, map $memoFish ) {
    if ( $days == 0 ) {
        return 1;
    }
    if ( $memoFish->hasKey( [ $initFish, $days ]) ) {
        return $memoFish->get([ $initFish, $days ]);
    }
    if ( $initFish == 0 ) {
        $res = simulateFaster(6, $days-1, $memoFish ) + simulateFaster( 8, $days - 1, $memoFish);
        $memoFish->put([$initFish, $days], $res);
        return ( $res );
    }
    $res = simulateFaster( $initFish - 1, $days - 1, $memoFish );
    $memoFish->put([$initFish, $days], $res);
    return ( $res );
}

function day05( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $fishesStr = explode( ',',  $strcontent);
    $fishes = [];
    foreach ( $fishesStr as $fishStr ) {
        $fishes[] = intval( $fishStr);
    }
    $day06a = day06a( $fishes );
    $day06b = day06b( $fishes );
    print ( $day06a . PHP_EOL);
    print ( $day06b . PHP_EOL);
}

day05( '../data/day06.txt');