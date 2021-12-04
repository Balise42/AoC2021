<?php

class BingoGrid {
    private array $nums;
    private array $marked;

    public function addLine( $numsStr ) {
        $line = [];
        $markedLine = [];
        foreach ( $numsStr as $numstr ) {
            $line[] = intval( $numstr );
            $markedLine[] = false;
        }
        $this->nums[] = $line;
        $this->marked[] = $markedLine;
    }

    public function mark( $num ) {
        for ( $i = 0; $i < count( $this->nums ); $i++ ) {
            for ( $j = 0; $j < count ( $this->nums[ $i ] ); $j++ ) {
                if ( $this->nums[$i][$j] == $num ) {
                    $this->marked[$i][$j] = true;
                }
            }
        }
    }

    public function winningSum() {
        $sum = 0;
        for ( $i = 0; $i < count( $this->nums ); $i++ ) {
            for ($j = 0; $j < count($this->nums[$i]); $j++) {
                if ( !$this->marked[$i][$j]) {
                    $sum += $this->nums[$i][$j];
                }
            }
        }

        for ( $i = 0; $i < count( $this->nums); $i++ ) {
            $win = true;
            for ( $j = 0; $j < count ( $this->nums[$i] ); $j++ ) {
                $win = $win && $this->marked[$i][$j];
            }
            if ( $win ) {
                return $sum;
            }
        }

        for ( $i = 0; $i < count( $this->nums[0]); $i++ ) {
            $win = true;
            for ( $j = 0; $j < count ( $this->nums ); $j++ ) {
                $win = $win && $this->marked[$j][$i];
            }
            if ( $win ) {
                return $sum;
            }
        }

        return -1;
    }
}

function day04( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode("\n", $strcontent);

    $nums = [];

    $numsStr = explode( ',', $lines[0]);
    foreach ( $numsStr as $numStr ) {
        $nums[] = intval($numStr);
    }

    $grids = [];

    $grid = null;
    for ( $i = 1; $i < count( $lines ); $i++ ) {
        $line = $lines[$i];
        $lineNrsStr = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
        if ( count($lineNrsStr) < 5 ) {
            if ( $grid !== null) {
                $grids[] = $grid;
            }
            $grid = new BingoGrid();
        } else {
            $grid->addLine( $lineNrsStr );
        }
    }

    $day04a = day04a( $nums, $grids );
    $day04b = day04b( $nums, $grids );
    print ( $day04a . PHP_EOL);
    print ( $day04b . PHP_EOL);
}

function day04a( array $nums, array $grids ) : int {
    foreach ( $nums as $num ) {
        foreach ($grids as $grid) {
            /** @var $grid BingoGrid */
            $grid->mark($num);
            $winningSum = $grid->winningSum();
            if ( $winningSum != -1) {
                return $winningSum * $num;
            }
        }
    }
    return -1;
}

function day04b( array $nums, array $grids ) : int {
    $won = [];
    $numGrids = count( $grids );
    for ( $i = 0; $i < $numGrids; $i++ ) {
        $won[] = false;
    }

    $numWonGrids = 0;

    foreach ( $nums as $num ) {
        foreach ($grids as $i => $grid) {
            if ( !$won[$i] ) {
                /** @var $grid BingoGrid */
                $grid->mark($num);
                $winningSum = $grid->winningSum();
                if ($winningSum != -1) {
                    $won[$i] = true;
                    $numWonGrids++;
                    if ( $numWonGrids == $numGrids ) {
                        return $winningSum * $num;
                    }
                }
            }
        }
    }

    return -1;
}

day04( '../data/day04.txt');