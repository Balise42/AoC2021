<?php

function day01() : void {
    $strcontent = file_get_contents('../data/day01.txt');
    $lines = explode("\n", $strcontent);
    $nums = [];
    foreach ($lines as $line) {
        $nums[] = intval($line);
    }
    $day01a = day01a( $nums );
    $day01b = day01b( $nums );
    print ( $day01a . PHP_EOL);
    print ( $day01b . PHP_EOL);
}

function day01a( array $nums ) : int {
    $res = 0;
    for ( $i = 1; $i < sizeof( $nums ); $i++ ) {
        if ( $nums[$i] > $nums[$i-1] ) {
            $res++;
        }
    }
    return $res;
}

function day01b( array $nums ) : int {
    $res = 0;
    $sum = $nums[0] + $nums[1] + $nums[2];
    for ( $i = 3; $i < sizeof( $nums ); $i++ ) {
        $newSum = $sum - $nums[$i - 3] + $nums[$i];
        if ( $newSum > $sum ) {
            $res++;
        }
        $sum = $newSum;
    }
    return $res;

}

day01();
