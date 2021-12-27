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
            $str .= $c;
        }
        foreach ( $this->rooms as $room ) {
            $str .= $room[0] . $room[1];
        }
        return $str;
    }
}

function findPossibleNextStep( Burrow $burrow ) : array {
    $res = [];
    for ( $i = -2; $i <= 8; $i++ ) {
        if ( $burrow->corridor[$i] !== -1 ) {
            $arth = $burrow->corridor[$i];
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
            if ( $burrow->rooms[$arth][0] == -1 ) {
                $newBurrow = $burrow->copy();
                $newBurrow->corridor[$i] = -1;
                $newBurrow->rooms[$arth][0] = $arth;
                $res[] = [$newBurrow, pow(10, $arth) * ( ($arthRoom > $i ? ($arthRoom-$i) : ($i - $arthRoom)) + 2)];
            } else if ( $burrow->rooms[$arth][0] == $arth && $burrow->rooms[$arth][1] == -1) {
                $newBurrow = $burrow->copy();
                $newBurrow->corridor[$i] = -1;
                $newBurrow->rooms[$arth][1] = $arth;
                $res[] = [$newBurrow, pow(10, $arth) * ( ($arthRoom > $i ? ($arthRoom-$i) : ($i - $arthRoom)) + 1)];
            }
        }
    }

    foreach ( $burrow->rooms as $r => $room ) {
        if ( $room[0] == $r && $room[1] == $r ) {
            continue;
        }
        if ( $room[1] != -1 || $room[0] != -1) {
            $acc = [];
            for ( $i = -2; $i <= 8; $i++ ) {
                if ( $i == 0 || $i == 2 || $i == 4 || $i == 6 ) {
                    continue;
                }
                $isAcc = true;
                if ( $i <= 2* $r ) {
                    for ( $j = 2*$r; $j >= $i; $j-- ) {
                        $isAcc = $isAcc && $burrow->corridor[$j] == -1;
                    }
                } else {
                    $isAcc = true;
                    for ( $j = 2*$r; $j <= $i; $j++ ) {
                        $isAcc = $isAcc && $burrow->corridor[$j] == -1;
                    }
                }
                if ( $isAcc ) {
                    $acc[] = $i;
                }
            }
            if ( $room[1] != -1 ) {
                foreach ( $acc as $dest ) {
                    $newBurrow = $burrow->copy();
                    $newBurrow->corridor[$dest] = $room[1];
                    $newBurrow->rooms[$r][1] = -1;
                    $res[] = [$newBurrow, pow(10, $room[1]) * ((($dest > $r*2) ? ($dest - $r * 2) : ($r*2 - $dest)) + 1)];
                }
            } else {
                foreach ( $acc as $dest ) {
                    $newBurrow = $burrow->copy();
                    $newBurrow->corridor[$dest] = $room[0];
                    $newBurrow->rooms[$r][0] = -1;
                    $res[] = [$newBurrow, pow(10, $room[0]) * ((($dest > $r*2) ? ($dest - $r * 2) : ($r*2 - $dest)) + 2)];
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
        [0,1],
        [3,2],
        [2,1],
        [0,3]
       /*[0, 1],
        [1, 0],
        [2, 2],
        [3, 3]*/
    ];

    $dist = [];

    $queue = new SplPriorityQueue();
    $queue->insert( $burrow, 0 );
    $queue->setExtractFlags( SplPriorityQueue::EXTR_BOTH );
    $res = -1;

    while ( !$queue->isEmpty() ) {
        print $queue->count() . PHP_EOL;
        $curr = $queue->extract();
        /** @var Burrow $u */
        $u = $curr['data'];
        $prio = $curr['priority'];

        if ( $u->isTidy() ) {
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
            }
        }
    }
    print $res;
}

day23();
