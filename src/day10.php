<?php

function isMatchingChunk( string $last, string $char ) : bool {
    return
        ( $last === '(' && $char === ')') ||
        ( $last === '[' && $char === ']') ||
        ( $last === '{' && $char === '}') ||
        ( $last === '<' && $char === '>');
}

function illegalCharValue( string $char ) : int {
    $values = [ ')' => 3, ']' => 57, '}' => 1197, '>' => 25137 ];
    return $values[$char];
}

function corruptionScore( array $chars, array &$stack ) : int {
    foreach ( $chars as $char ) {
        if ( str_contains( '([{<', $char ) ) {
            array_push( $stack, $char );
        } else {
            $last = array_pop( $stack );
            if ( isMatchingChunk( $last, $char ) ) {
                continue;
            }
            return illegalCharValue( $char );
        }
    }
    return 0;
}

function completionScore( array $stack ) : int {
    $values = [ '(' => 1, '[' => 2, '{' => 3, '<' => 4 ];
    $closings = [ '(' => 0, '[' => 0, '{' => 0, '<' => 0 ];
    $clChar = [ ')' => '(', ']' => '[', '}' => '{', '>' => '<'];

    $score = 0;
    while ( count( $stack ) > 0 ) {
        $char = array_pop( $stack );
        if ( str_contains( '([{<', $char ) ) {
            if ( $closings[$char] > 0 ) {
                $closings[$char]--;
            } else {
                $score = $score * 5 + $values[$char];
            }
        } else {
            $closings[ $clChar[$char]]++;
        }
    }
    return $score;
}

function day10a( array $lines ) : int {
    $score = 0;
    foreach ( $lines as $line ) {
        $stack = [];
        $chars = str_split( $line );
        $score += corruptionScore( $chars, $stack );
    }
    return $score;
}

function day10b( array $lines ) : int {
    $scores = [];
    foreach ( $lines as $line ) {
        $chars = str_split( $line );
        $stack = [];
        if ( corruptionScore( $chars, $stack ) == 0) {
            $scores[] = completionScore( $stack );
        }
    }
    sort( $scores );
    return  $scores[ count( $scores ) / 2 ];
}

function day10( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $day10a = day10a( $lines );
    $day10b = day10b( $lines );
    print ( $day10a . PHP_EOL);
    print ( $day10b . PHP_EOL);
}

day10( '../data/day10.txt');
