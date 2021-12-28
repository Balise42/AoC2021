<?php

function moveOneStep( array &$grid ) : bool {
    $lline = count($grid[0]);
    $nline = count($grid);
    $moving = [];
    $moved = false;
    foreach ( $grid as $l => $line ) {
        $moving[] = [];
        foreach( $line as $i => $char ) {
            if ( $char === '>' ) {
                if ( $line[ ($i + 1) % $lline ] === '.' ) {
                    $moving[$l][$i] = true;
                    $moved = true;
                }
            }
        }
    }
    foreach ( $moving as $l => $line ) {
        foreach ( $line as $i => $cuc ) {
            $grid[$l][$i] = '.';
            $grid[$l][($i + 1)%$lline] = '>';
        }
    }

    $moving = [];
    foreach ( $grid as $l => $line ) {
        $moving[] = [];
        foreach( $line as $i => $char ) {
            if ( $char === 'v' ) {
                if ( $grid[($l+1) % $nline][$i] === '.' ) {
                    $moving[$l][$i] = true;
                    $moved = true;
                }
            }
        }
    }
    foreach ( $moving as $l => $line ) {
        foreach ( $line as $i => $cuc ) {
            $grid[$l][$i] = '.';
            $grid[($l+1) % $nline][$i]  = 'v';
        }
    }

    return $moved;
}

function day25( string $filename ) {
    $content = file_get_contents( $filename );
    $lines = explode("\n", $content );
    $grid = [];
    foreach ( $lines as $i => $line ) {
        if ( strlen($line) > 0 ) {
            $grid[$i] = [];
            foreach ( str_split( $line ) as $char ) {
                $grid[$i][] = $char;
            }
        }
    }

    $moved = true;
    $numSteps = 0;
    while ( $moved ) {
        $moved = moveOneStep( $grid);
        $numSteps++;
    }

    print $numSteps . PHP_EOL;
}

day25('../data/day25.txt');
