<?php

class Burrow {
    public array $corridor;
    public array $rooms;

    public function copy() : Burrow {
        $b = new Burrow();
        $b->corridor = $this->corridor;
        $b->rooms = [
            $this->rooms[0],
            $this->rooms[1],
            $this->rooms[2],
            $this->rooms[3]
        ];
        return $b;
    }

    public function isTidy() : bool {
        foreach ( $this->corridor as $c ) {
            if ( $c != -1 ) {
                return false;
            }
        }
        foreach ( $this->rooms as $r => $room ) {
            foreach ( $room as $a ) {
                if ( $r != $a ) {
                    return false;
                }
            }
        }
        return true;
    }

    public function __toString() : string {
        $str = '';
        foreach ( $this->corridor as $c ) {
            if ( $c == -1 ) {
                $str .= '.';
            } else {
                $str .= chr(ord($c) + (ord('A') - ord('0')));
            }
        }
        foreach ( $this->rooms as $room ) {
            $str .= '/';
            foreach ( $room as $r ) {
                if ($r == '-1') {
                    $str .= ".";
                } else {
                    $str .= chr(ord($r) + (ord('A') - ord('0')));
                }
            }
        }
        return $str;
    }
}

function findPossibleNextStep( Burrow $burrow ) : array {
    $res = [];
    for ( $i = -2; $i <= 8; $i++ ) {
        if ( $burrow->corridor[$i] !== -1 ) {
            $arth = $burrow->corridor[$i];
            $validRoom = true;
            foreach ( $burrow->rooms[$arth] as $r ) {
                $validRoom = $validRoom && ($r == $arth || $r == -1);
            }
            if (!$validRoom) {
                continue;
            }
            $arthRoom = $arth * 2;
            $pathFree = true;
            if ( $arthRoom < $i ) {
                for ( $j = $i - 1; $j >= $arthRoom; $j-- ) {
                    $pathFree = $pathFree && $burrow->corridor[$j] === -1;
                }
            } else {
                for ( $j = $i + 1; $j <= $arthRoom; $j++ ) {
                    $pathFree = $pathFree && $burrow->corridor[$j] === -1;
                }
            }
            if ( !$pathFree ) {
                continue;
            }
            for ( $j = 0; $j < count($burrow->rooms[$arth]); $j++ ) {
                if ($burrow->rooms[$arth][$j] == -1) {
                    $newBurrow = $burrow->copy();
                    $newBurrow->corridor[$i] = -1;
                    $newBurrow->rooms[$arth][$j] = $arth;
                    $res[] = [$newBurrow, pow(10, $arth) * (($arthRoom > $i ? ($arthRoom - $i) : ($i - $arthRoom)) + count($burrow->rooms[$arth]) - $j)];
                    break;
                }
            }
        }
    }

    foreach ( $burrow->rooms as $r => $room ) {
        $complete = true;
        $hasArth = false;
        for ( $i = 0; $i < count($room); $i++) {
            $complete = $complete && $room[$i] == $r;
            $hasArth = $hasArth || ( $room[$i] !== -1 );
        }
        if ( $complete ) {
            continue;
        }
        if ( $hasArth ) {
            $acc = [];
            for ($i = -2; $i <= 8; $i++) {
                if ($i == 0 || $i == 2 || $i == 4 || $i == 6) {
                    continue;
                }
                $isAcc = true;
                if ($i <= 2 * $r) {
                    for ($j = 2 * $r; $j >= $i; $j--) {
                        $isAcc = $isAcc && $burrow->corridor[$j] == -1;
                    }
                } else {
                    $isAcc = true;
                    for ($j = 2 * $r; $j <= $i; $j++) {
                        $isAcc = $isAcc && $burrow->corridor[$j] == -1;
                    }
                }
                if ($isAcc) {
                    $acc[] = $i;
                }
            }
            for ($j = count($room) - 1; $j >= 0; $j--) {
                if ($room[$j] != -1) {
                    foreach ($acc as $dest) {
                        $newBurrow = $burrow->copy();
                        $newBurrow->corridor[$dest] = $room[$j];
                        $newBurrow->rooms[$r][$j] = -1;
                        $res[] = [$newBurrow, pow(10, $room[$j]) * ((($dest > $r * 2) ? ($dest - $r * 2) : ($r * 2 - $dest)) + count($room) - $j)];
                    }
                    break;
                }
            }
        }
    }
    return $res;
}

function day23() {
    $burrow = new Burrow();
    $burrow->corridor = [];
    for ( $i = -2; $i <= 8; $i++ ) {
        $burrow->corridor[$i] = -1;
    }
    $burrow->rooms = [
        /*[ 1, 0 ],
        [ 2, 3 ],
        [ 1, 2 ],
        [ 3, 0 ]*/
        [2,3,3,0],
        [2,1,2,3],
        [3,0,1,0],
        [1,2,0,1]
       /*[0, 1],
        [1, 0],
        [2, 2],
        [3, 3]*/
    ];

    $dist = [];
    $prev = [];

    $queue = new SplPriorityQueue();
    $queue->insert( $burrow, 0 );
    $queue->setExtractFlags( SplPriorityQueue::EXTR_BOTH );
    $res = -1;
    $dep = $burrow->__toString();


    while ( !$queue->isEmpty() ) {
        print $queue->count() . PHP_EOL;
        $curr = $queue->extract();
        /** @var Burrow $u */
        $u = $curr['data'];
        $prio = $curr['priority'];

        if ( $u->isTidy() ) {
            $str = [];
            $str[] = $u->__toString();
            $p = $prev[$u->__toString()] ?? null;
            while ( $p !== $dep ) {
                $str[] = $p . ' ' . $dist[$p];
                $p = $prev[$p] ?? null;
            }
            $str = array_reverse( $str );
            print ( join(PHP_EOL, $str) . PHP_EOL );
            $res = -$prio;
            break;
        }
        if ( isset($dist[$u->__toString()]) && $dist[$u->__toString()] > $prio ) {
            continue;
        }
        $neighs = findPossibleNextStep($u);
        foreach ( $neighs as $n ) {
            if ( $prio - $n[1] > (isset($dist[$n[0]->__toString()]) ? $dist[$n[0]->__toString()] : PHP_INT_MIN )) {
                $dist[$n[0]->__toString()] = $prio - $n[1];
                $queue->insert($n[0], $prio - $n[1]);
                $prev[$n[0]->__toString()] = $u->__toString();
            }
        }
    }
    print $res;
}

day23();
