<?php

function day15a( array $grid ) : int {
    $arrival = [count($grid) - 1, count($grid[count($grid) - 1]) - 1];
    $queue = new SplPriorityQueue();
    $queue->setExtractFlags( SplPriorityQueue::EXTR_BOTH );
    $dists= [];
    $visited = [];
    for ( $i = 0; $i < count( $grid ); $i++ ) {
        $dists[$i] = [];
        $visited[$i] = [];
        for ( $j = 0; $j < count($grid[0]); $j++ ) {
            $dists[$i][] = PHP_INT_MIN;
            $visited[$i][] = false;
        }
    }
    $queue->insert( [0, 0], 0);
    $dists[0][0] = 0;
    while ( ! $queue->isEmpty() ) {
        $current = $queue->extract();
        $node = $current['data'];
        $prio = $current['priority'];
        if ( $visited[$node[0]][$node[1]] === true ) {
            continue;
        }
        if ( $node === $arrival ) {
            return -$prio;
        }
        for ( $i = -1; $i <= 1; $i++ ) {
            for ( $j = -1; $j <= 1; $j++ ) {
                if ( isset( $grid[$node[0] + $i][$node[1] + $j] ) &&
                    ( $i == 0 || $j == 0 ) && ( $i != 0 || $j != 0 ) ) {
                    $dist = $prio - $grid[$node[0] + $i][$node[1] + $j];
                    if ( $dist > $dists[$node[0] + $i][$node[1] + $j] ) {
                        $dists[$node[0] + $i][$node[1] + $j] = $dist;
                        $queue->insert( [$node[0] + $i, $node[1] + $j], $dist );
                    }
                }
            }
        }
        $visited[$node[0]][$node[1]] = true;
    }
    return - 1;
}

function day15b( array $grid ) : int
{
    $newGrid = [];
    for ($i = 0; $i < count($grid) * 5; $i++) {
        $newGrid = [];
        for ($j = 0; $j < count($grid[0]) * 5; $j++) {
            $newGrid[$i][] = 0;
        }
    }

    $sizeX = count($grid[0]);
    $sizeY = count($grid);
    for ($i = 0; $i < 5; $i++) {
        for ($j = 0; $j < 5; $j++) {
            for ($y = 0; $y < $sizeY; $y++) {
                for ($x = 0; $x < count($grid); $x++) {
                    if ($i == 0 && $j == 0) {
                        $newGrid[$i * $sizeX + $x][$j * $sizeY + $y] = $grid[$x][$y];
                    } else if ($i == 0) {
                        $newGrid[$i * $sizeX + $x][$j * $sizeY + $y] = $newGrid[$i * $sizeX + $x][($j - 1) * $sizeY + $y] + 1;
                    } else {
                        $newGrid[$i * $sizeX + $x][$j * $sizeY + $y] = $newGrid[($i - 1) * $sizeX + $x][$j * $sizeY + $y] + 1;
                    }
                    if ($newGrid[$i * $sizeX + $x][$j * $sizeY + $y] > 9) {
                        $newGrid[$i * $sizeX + $x][$j * $sizeY + $y] = 1;
                    }
                }
            }
        }
    }
    return day15a( $newGrid );
}

function day15( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $grid = [];
    foreach ( $lines as $line ) {
        if ( strlen( $line ) > 0 ) {
            $grid[] = [];
            $risks = str_split( $line );
            foreach ( $risks as $risk ) {
                $grid[ count($grid) - 1][] = intval ( $risk );
            }
        }
    }

    $day15a = day15a( $grid );
    $day15b = day15b( $grid );
    print ( $day15a . PHP_EOL);
    print ( $day15b . PHP_EOL);
}

day15( '../data/day15.txt');
