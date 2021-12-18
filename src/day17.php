<?php

function hitsTarget ($xmin, $xmax, $ymin, $ymax, $vx, $vy) : bool {
    $x = 0;
    $y = 0;
    while (true) {
        if ( $y < $ymin ) {
            return false;
        }
        if ( $x >= $xmin && $x <= $xmax && $y >= $ymin && $y <= $ymax ) {
            return true;
        }
        $x += $vx;
        $y += $vy;
        if ( $vx > 0 ) {
            $vx--;
        }
        $vy--;
    }
}

function day17a(int $xmin, int $xmax, int $ymin, int $ymax): int
{
    // vxMin needs to be able to reach before drag kills it
    for ( $vxMin = 0; $vxMin < $xmin; $vxMin++ ) {
        if ( ( $vxMin * ($vxMin + 1) ) / 2 > $xmin) {
            break;
        }
    }

    $maxY = 0;
    for ( $vx = $vxMin; $vx < $xmax; $vx++ ) {
        for ( $vy = 0; $vy < 1000; $vy++ ) {
            if ( $vy * ($vy + 1) / 2 < $maxY ) {
                continue;
            }
            if ( hitsTarget( $xmin, $xmax, $ymin, $ymax, $vx, $vy) ) {
                $maxY = $vy * ($vy + 1) / 2;
            }
        }
    }


    return $maxY;
}

function day17b(int $xmin, int $xmax, int $ymin, int $ymax): int {
    // vxMin needs to be able to reach before drag kills it
    for ( $vxMin = 0; $vxMin < $xmin; $vxMin++ ) {
        if ( ( $vxMin * ($vxMin + 1) ) / 2 > $xmin) {
            break;
        }
    }

    $hits = 0;
    for ( $vx = $vxMin; $vx <= $xmax; $vx++ ) {
        for ( $vy = $ymin; $vy < 300; $vy++ ) {
            if ( hitsTarget( $xmin, $xmax, $ymin, $ymax, $vx, $vy) ) {
                $hits++;
            }
        }
    }


    return $hits;
}

function day17(string $filename): void
{
    $strcontent = file_get_contents($filename);

    $groups = [];
    preg_match('/target area: x=([\-\d]+)..([\-\d]+), y=([\-\d]+)..([\-\d]+)/', $strcontent, $groups );

    $xmin = intval( $groups[1] );
    $xmax = intval( $groups[2] );
    $ymin = intval( $groups[3] );
    $ymax = intval( $groups[4] );

    $day17a = day17a($xmin, $xmax, $ymin, $ymax);
    $day17b = day17b($xmin, $xmax, $ymin, $ymax);

    print ($day17a . PHP_EOL);
    print ($day17b . PHP_EOL);
}

day17('../data/day17.txt');