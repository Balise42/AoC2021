<?php

function processRules( string $template, array $rules ) : string {
    $res = '' . $template[0];
    for ( $i = 0; $i < strlen( $template ) - 1; $i++ ) {
        $res .= $rules[substr( $template, $i, 2)] . $template[$i+1];
    }
    return $res;
}

function day14a( string $template, array $rules ) : int {
    for ( $i = 0; $i < 10; $i++ ) {
        $template = processRules( $template, $rules );
    }
    $counts = [];
    foreach ( str_split( $template ) as $char ) {
        if ( !array_key_exists( $char, $counts ) ) {
            $counts[$char] = 0;
        }
        $counts[$char]++;
    }

    $min = PHP_INT_MAX;
    $max = 0;
    foreach ( $counts as $count ) {
        if ( $count < $min ) {
            $min = $count;
        }
        if ( $count > $max ) {
            $max = $count;
        }
    }
    return $max - $min;
}

function computeCountsPair( string $s, array $rules, array &$memo, int $it ) : array {
    if ( isset($memo[$s][$it]) ) {
        return $memo[$s][$it];
    }
    if (!isset($memo[$s])) {
        $memo[$s] = [];
    }
    if ( $it == 1 ) {
        $counts = [ $rules[$s] => 1 ];
        $memo[$s][$it] = $counts;
        return $counts;
    }
    $countPair1 = computeCountsPair($s[0] . $rules[$s] , $rules, $memo, $it - 1);
    $countPair2 = computeCountsPair($rules[$s] . $s[1], $rules, $memo, $it - 1);
    foreach ( $countPair2 as $k => $v) {
        $countPair1[$k] = ($countPair1[$k] ?? 0) + $v;
    }
    $countPair1[$rules[$s]] = ( $countPair1[$rules[$s]] ?? 0) + 1;
    $memo[$s][$it] = $countPair1;
    return $countPair1;
}

function computeCounts( string $template, array $rules ) : array {
    $memo = [];
    $counts = [];
    for ( $i = 0; $i < strlen( $template ) - 1; $i++ ) {
        $countPair = computeCountsPair( substr( $template, $i, 2), $rules, $memo, 40);
        foreach ( $countPair as $k => $v) {
            $counts[$k] = ($counts[$k] ?? 0) + $v;
        }
    }
    foreach ( str_split( $template) as $char ) {
        $counts[$char] = ($counts[$char] ?? 0) + 1;
    }
    return $counts;
}


function day14b( string $template, array $rules ) : int {
    $counts = computeCounts( $template, $rules );
    $min = PHP_INT_MAX;
    $max = 0;
    foreach ( $counts as $count ) {
        if ( $count < $min ) {
            $min = $count;
        }
        if ( $count > $max ) {
            $max = $count;
        }
    }
    return $max - $min;
}

function day14( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $template = array_shift( $lines );
    $rules = [];
    foreach ( $lines as $line ) {
        if ( strlen( $line ) > 0 ) {
            $toks = explode(' -> ', $line);
            $rules[$toks[0]] = $toks[1];
        }
    }

    $day14a = day14a( $template, $rules );
    $day14b = day14b( $template, $rules );
    print ( $day14a . PHP_EOL);
    print ( $day14b . PHP_EOL);
}

day14( '../data/day14.txt');