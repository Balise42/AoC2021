<?php


function day21a(int $p1, int $p2) {
    $pos1 = $p1 - 1;
    $pos2 = $p2 - 1;
    $s1 = 0;
    $s2 = 0;
    $die = 1;
    $rolls = 0;

    while ( true ) {
        for ( $i = 0; $i < 3; $i++ ) {
            $pos1 = ($pos1 + $die) % 10;
            $rolls++;
            $die++;
            if ($die == 101) {
                $die = 1;
            }
        }
        $s1 += $pos1 + 1;
        if ( $s1 >= 1000 ) {
            return $s2 * $rolls;
        }
        for ( $i = 0; $i < 3; $i++ ) {
            $pos2 = ($pos2 + $die) % 10;
            $rolls++;
            $die++;
            if ($die == 101) {
                $die = 1;
            }
        }
        $s2 += $pos2 + 1;
        if ( $s2 >= 1000 ) {
            return $s1 * $rolls;
        }
    }
}

global $memoWins;

function numWins(int $p, int $p1, int $p2, int $s1, int $s2, int $limit ) {
    global $memoWins;

    if ( isset ( $memoWins[$p1][$p2][$s1][$s2][$p]  ) ) {
        return $memoWins[$p1][$p2][$s1][$s2][$p];
    }
    if ( !isset($memoWins[$p1])) {
        $memoWins[$p1] = [];
    }
    if ( !isset($memoWins[$p1][$p2])) {
        $memoWins[$p1][$p2] = [];
    }
    if ( !isset($memoWins[$p1][$p2][$s1])) {
        $memoWins[$p1][$p2][$s1] = [];
    }
    if ( !isset($memoWins[$p1][$p2][$s1][$s2])) {
        $memoWins[$p1][$p2][$s1][$s2] = [];
    }

    if ( $s1 >= $limit ) {
        $memoWins[$p1][$p2][$s1][$s2][1] = 1;
        $memoWins[$p1][$p2][$s1][$s2][2] = 0;
        return $memoWins[$p1][$p2][$s1][$s2][$p];
    }
    if ($s2 >= $limit ) {
        $memoWins[$p1][$p2][$s1][$s2][1] = 0;
        $memoWins[$p1][$p2][$s1][$s2][2] = 1;
        return $memoWins[$p1][$p2][$s1][$s2][$p];
    }

    $winsP1 = 0;
    $winsP2 = 0;
    for ( $i = 1; $i <= 3; $i++ ) {
        for ( $j = 1; $j <= 3; $j++ ) {
            for ( $k = 1; $k <= 3; $k++ ) {
                $pos1 = ($p1 + $i + $j + $k);
                if ( $pos1 > 10 ) {
                    $pos1 = $pos1 - 10;
                }
                $newS1 = $s1 + $pos1;
                if ( $newS1 >= $limit) {
                    $winsP1++;
                    continue;
                }

                for ( $l = 1; $l <= 3; $l++ ) {
                    for ( $m = 1; $m <= 3; $m++ ) {
                        for ( $n = 1; $n <= 3; $n++ ) {
                            $pos2 = ($p2 + $l + $m + $n);
                            if ( $pos2 > 10 ) {
                                $pos2 = $pos2 - 10;
                            }
                            $newS2 = $s2 + $pos2;

                            $winsP1 += numWins( 1, $pos1, $pos2, $newS1, $newS2, $limit);
                            $winsP2 += numWins( 2, $pos1, $pos2, $newS1, $newS2, $limit);
                        }
                    }
                }
            }
        }
    }
    $memoWins[$p1][$p2][$s1][$s2][1] = $winsP1;
    $memoWins[$p1][$p2][$s1][$s2][2] = $winsP2;

    return $memoWins[$p1][$p2][$s1][$s2][$p];
}

function day21b( $p1, $p2 ) {
    $a = numWins( 1, $p1, $p2, 0, 0, 21 );
    $b = numWins( 2, $p1, $p2, 0, 0, 21 );
    return ($a > $b) ? $a : $b;
}

function day21(int $p1, int $p2) {
    print day21a( $p1, $p2 ) . PHP_EOL;
    print day21b( $p1, $p2 ) . PHP_EOL;
}

day21(2, 1 );