<?php

use Ds\Map;

class Segment {
   public int $x1, $y1, $x2, $y2;

   public function __construct( int $x1, int $y1, int $x2, int $y2) {
       $this->x1 = $x1;
       $this->y1 = $y1;
       $this->x2 = $x2;
       $this->y2 = $y2;
   }

   public static function createFromInput( string $line ) : Segment {
       $coords = explode( ' -> ', $line );
       $p1coords = explode ( ',', $coords[0] );
       $p2coords = explode ( ',', $coords[1] );
       return new Segment(
           intval( $p1coords[0] ),
           intval( $p1coords[1] ),
           intval( $p2coords[0] ),
           intval( $p2coords[1] )
       );
   }

   public function isVertical() : bool {
       return $this->x1 == $this->x2;
   }

   public function isHorizontal() : bool {
       return $this->y1 == $this->y2;
   }
}

function createHorVertGrid( array $segments ) : Map {
    $grid = new Map();
    foreach ( $segments as $segment ) {
        /** @var $segment Segment */
        if ( $segment->isHorizontal() ) {
            $x1 = $segment->x1 < $segment->x2 ? $segment->x1 : $segment->x2;
            $x2 = $segment->x1 < $segment->x2 ? $segment->x2 : $segment->x1;
            for ( $x = $x1; $x <= $x2; $x++ ) {
                $curVal = $grid->hasKey( [$x, $segment->y1] ) ? $grid->get( [$x, $segment->y1] ) : 0;
                $grid->put( [ $x, $segment->y1 ], $curVal + 1 );
            }
        }
        if ( $segment->isVertical() ) {
            $y1 = $segment->y1 < $segment->y2 ? $segment->y1 : $segment->y2;
            $y2 = $segment->y1 < $segment->y2 ? $segment->y2 : $segment->y1;
            for ( $y = $y1; $y <= $y2; $y++ ) {
                $curVal = $grid->hasKey( [$segment->x1, $y] ) ? $grid->get( [$segment->x1, $y] ) : 0;
                $grid->put( [ $segment->x1, $y ], $curVal + 1 );
            }
        }
    }
    return $grid;
}

function createFullGrid( array $segments ) : Map {
    $grid = createHorVertGrid( $segments);
    foreach ( $segments as $segment ) {
        /** @var $segment Segment */
        if ( $segment->isVertical() || $segment->isHorizontal() ) {
            // been there, done that
            continue;
        }
        $diffX = $segment->x2 - $segment->x1;
        $diffY = $segment->y2 - $segment->y1;

        $y = $segment->y1;
        for ( $x = $segment->x1; $diffX > 0 ? $x <= $segment->x2 : $x >= $segment->x2; $diffX > 0 ? $x++ : $x--) {
            $curVal = $grid->hasKey( [$x, $y] ) ? $grid->get( [$x, $y] ) : 0;
            $grid->put( [$x, $y ], $curVal + 1 );
            $diffY > 0 ? $y++ : $y--;
        }
    }
    return $grid;
}

function countOverlaps( Map $grid ) : int {
    $res = 0;
    foreach ( $grid->values() as $val ) {
        if ( $val > 1 ) {
            $res++;
        }
    }
    return $res;
}

function day05a( array $segments) : int {
    $grid = createHorVertGrid( $segments );
    return countOverlaps( $grid );
}

function day05b( array $segments) : int {
    $grid = createFullGrid( $segments );
    return countOverlaps( $grid );
}

function day05( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode("\n", $strcontent);

    $segments = [];

    foreach ( $lines as $line ) {
        if ( str_contains( $line, '->' ) ) {
            $segments[] = Segment::createFromInput($line);
        }
    }
    $day05a = day05a( $segments );
    $day05b = day05b( $segments );
    print ( $day05a . PHP_EOL);
    print ( $day05b . PHP_EOL);
}

day05( '../data/day05.txt');