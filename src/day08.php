<?php

function day08a( array $outputs ) {
    $res = 0;
    foreach ( $outputs as $output ) {
        foreach ( $output as $word ) {
            $l = strlen( $word );
            if ( $l === 2 || $l === 4 || $l === 3 | $l === 7 ) {
                $res++;
            }
        }
    }
    return $res;
}

function updateMapping(array &$possibleValuesForSegment, string $word, array $segments ) {
    $chars = str_split( $word );

    foreach ($possibleValuesForSegment as $k => $v ) {
        if ( array_search( $k, $chars ) !== false ) {
            $possibleValuesForSegment[ $k ] = array_intersect( $possibleValuesForSegment[$k], $segments);
        } else {
            $possibleValuesForSegment[ $k ] = array_diff( $possibleValuesForSegment[$k], $segments);
        }
    }
}

function findMapping( array $words ) : array {
    $map = [];
    $possibleValuesForSegment = [];

    $possibleValuesForSegment['a'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['b'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['c'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['d'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['e'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['f'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];
    $possibleValuesForSegment['g'] = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g' ];

    $fives = [];
    $sixes = [];
    foreach ( $words as $word ) {
        if ( strlen( $word ) == 2 ) {
            updateMapping ( $possibleValuesForSegment, $word, ['c', 'f'] );
        } else if ( strlen( $word ) == 4 ) {
            updateMapping( $possibleValuesForSegment, $word, [ 'b', 'c', 'd', 'f' ] );
        } else if ( strlen( $word ) == 3 ) {
            updateMapping( $possibleValuesForSegment, $word, [ 'a', 'c', 'f' ] );
        } else if ( strlen ( $word ) == 5 ) {
            $fives[] = $word;
        } else if ( strlen ( $word) == 6) {
            $sixes[] = $word;
        }
    }

    $wordFive = implode( array_intersect( str_split( $fives[0] ), str_split( $fives[1] ), str_split ($fives[2] ) ) );
    $wordSix = implode( array_intersect( str_split( $sixes[0] ), str_split( $sixes[1] ), str_split( $sixes[2]) ) );
    updateMapping( $possibleValuesForSegment, $wordFive, ['a', 'd', 'g'] );
    updateMapping( $possibleValuesForSegment, $wordSix, ['a', 'b', 'f', 'g'] );

    $map = [];
    foreach ( $possibleValuesForSegment as $k => $v ) {
        $map[array_values($v)[0]] = $k;
    }
    return $map;
}

function getDigit ( string $word, array $map ) : int {
    $l = strlen( $word );
    if ( $l == 2 ) {
        return 1;
    }
    if ( $l == 3) {
        return 7;
    }
    if ( $l == 4 ) {
        return 4;
    }
    if ( $l == 7) {
        return 8;
    }

    if ( $l == 6) {
        if ( !str_contains( $word, $map[ 'd' ] ) ) {
            return 0;
        } else if ( !str_contains( $word, $map['c'] ) ) {
            return 6;
        }
        return 9;
    }

    if ( str_contains( $word, $map['c']) && str_contains( $word, $map['f']) ) {
        return 3;
    }

    if ( !str_contains( $word, $map[ 'c'] ) ) {
        return 5;
    }
    return 2;
}

function applyMapping( $map, $words ) : int {
    $num = 0;
    for ( $i = 0; $i < 4; $i++ ) {
        $num += pow(10, $i) * getDigit( $words[ 3 - $i], $map );
    }
    return $num;
}

function day08b( array $inputs, array $outputs ) {
    $res = 0;
    for ( $i = 0; $i < count ( $inputs ); $i++ ) {
        $map = findMapping( $inputs[$i] );
        $num = applyMapping( $map, $outputs[$i] );
        $res += $num;
    }
    return $res;
}

function day08( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);
    $outputs = [];
    foreach ( $lines as $line ) {
        $toks = explode( '|', $line );
        if ( count( $toks ) == 2 ) {
            $inputs[] = preg_split('/\s+/', $toks[0], -1, PREG_SPLIT_NO_EMPTY);
            $outputs[] = preg_split('/\s+/', $toks[1], -1, PREG_SPLIT_NO_EMPTY);
        }
    }
    $day08a = day08a( $outputs );
    $day08b = day08b( $inputs, $outputs );
    print ( $day08a . PHP_EOL);
    print ( $day08b . PHP_EOL);
}

day08( '../data/day08.txt');
