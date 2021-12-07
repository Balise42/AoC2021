<?php

function compute_fuel_b( $crabs, $i ) {
    $res = 0;
    foreach ( $crabs as $crab ) {
        $steps = abs( $i - $crab );
        $res = $res + ( $steps * ( $steps + 1 ) ) / 2;
    }
    return $res;
}

function compute_fuel( $crabs, $i ) {
    $res = 0;
    foreach ( $crabs as $crab ) {
        $res = $res + abs( $i - $crab );
    }
    return $res;
}

function day07a(array $crabs ) : int {
    $min = 20000;
    $max = 0;
    foreach ( $crabs as $crab ) {
        if ( $crab < $min ) {
            $min = $crab;
        }
        if ( $crab > $max) {
            $max = $crab;
        }
    }

    $fuel = 100000000;
    for ($i = $min; $i < $max; $i++) {
        $tmp = compute_fuel( $crabs, $i ) . PHP_EOL;
        if ( $tmp < $fuel ) {
            $fuel = $tmp;
        } else {
            break;
        }
    }
    return $fuel;
}

function day07b(array $crabs ) : int {
    $min = 20000;
    $max = 0;
    foreach ( $crabs as $crab ) {
        if ( $crab < $min ) {
            $min = $crab;
        }
        if ( $crab > $max) {
            $max = $crab;
        }
    }

    $fuel = 10000000000;
    for ($i = $min; $i < $max; $i++) {
        $tmp = compute_fuel_b( $crabs, $i ) . PHP_EOL;
        if ( $tmp < $fuel ) {
            $fuel = $tmp;
        } else {
            break;
        }
    }
    return $fuel;
}

function day07( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $crabsStr = explode( ',',  $strcontent);
    $crabs = [];
    foreach ( $crabsStr as $crabStr ) {
        $crabs[] = intval( $crabStr );
    }
    $day07a = day07a( $crabs );
    $day07b = day07b( $crabs );
    print ( $day07a . PHP_EOL);
    print ( $day07b . PHP_EOL);
}

day07( '../data/day07.txt');
