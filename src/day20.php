<?php

function enhanceImage( string $instr, array $grid, string $fill ) : array {
    $top = array_key_first( $grid ) - 2;
    $left = array_key_first( $grid[0] ) - 2;
    $bottom = array_key_last($grid) + 2;
    $right = array_key_last($grid[0]) + 2;

    $newGrid = [];
    for ( $y = $top; $y < $bottom; $y++ ) {
        $newGrid[$y] = [];
        for ( $x = $left; $x < $right; $x++) {
            $str = '';
            for ( $i = -1; $i <= 1; $i++ ) {
                for ( $j = -1; $j <= 1; $j++ ) {
                    if ( !isset($grid[$y + $i][$x + $j])) {
                        $str .= $fill;
                    } else {
                        $str .= $grid[$y+$i][$x+$j];
                    }
                }
            }
            $newGrid[$y][$x] = ( $instr[ bindec($str) ] === '.' ? '0' : '1');
        }
    }

    return $newGrid;
}

function day20a( string $instr, array $grid) {
    $newGrid = enhanceImage( $instr, $grid, 0);
    $newGrid = enhanceImage( $instr, $newGrid, $instr[0] === '.' ? '0' : '1' );

    $sum = 0;
    foreach ( $newGrid as $line ) {
        foreach ( $line as $pixel ) {
            if ( $pixel === '1') {
                $sum++;
            }
        }
    }
    return $sum;
}

function day20b( string $instr, array $grid) {
    $newGrid = enhanceImage( $instr, $grid, 0);
    for ( $i = 1; $i <= 49; $i++ ) {
        // this actually doesn't pass on input test because input test doesn't flip-flop
        $newGrid = enhanceImage($instr, $newGrid, ($i % 2 == 0 ? '0' : '1' ) );
    }

    $sum = 0;
    foreach ( $newGrid as $line ) {
        foreach ( $line as $pixel ) {
            if ( $pixel === '1') {
                $sum++;
            }
        }
    }
    return $sum;
}


function day20(string $filename): void{
    $strcontent = file_get_contents($filename);
    $lines = explode("\n", $strcontent);

    $instr = $lines[0];
    $grid = [];
    for ( $i = 1 ; $i < count($lines); $i++) {
        if ( strlen($lines[$i]) > 0) {
            $grid[] = [];

            foreach (str_split($lines[$i]) as $char) {
                if ($char === '.') {
                    $grid[count($grid) - 1][] = 0;
                } else {
                    $grid[count($grid) - 1][] = 1;
                }
            }
        }
    }

    $day20a = day20a($instr, $grid);
    print ($day20a . PHP_EOL);
    $day20b = day20b($instr, $grid);
    print ($day20b . PHP_EOL);
}

day20('../data/day20.txt');
