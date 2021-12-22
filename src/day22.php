<?php

class Cube {
    public $xmin;
    public $xmax;
    public $ymin;
    public $ymax;
    public $zmin;
    public $zmax;

    public function contains( int $x, int $y, int $z ) : bool {
        return $x >= $this->xmin && $x <= $this->xmax && $y >= $this->ymin && $y <= $this->ymax && $z >= $this->zmin && $z <= $this->zmax;
    }

    public function intersects(Cube $c) {
        return $this->contains( $c->xmin, $c->ymin, $c->zmin)
            || $this->contains( $c->xmax, $c->ymin, $c->zmin)
            || $this->contains( $c->xmin, $c->ymax, $c->zmin)
            || $this->contains( $c->xmin, $c->ymin, $c->zmax)
            || $this->contains( $c->xmax, $c->ymax, $c->zmin)
            || $this->contains( $c->xmax, $c->ymin, $c->zmax)
            || $this->contains( $c->xmin, $c->ymax, $c->zmax)
            || $this->contains( $c->xmax, $c->ymax, $c->zmax);
    }
}

function day22a(array $cubes, array $instr) {
    $c = new Cube();
    $c->xmin = -50;
    $c->xmax = 50;
    $c->ymin = -50;
    $c->ymax = 50;
    $c->zmin = -50;
    $c->zmax = 50;

    $grid = [];

    foreach ( $cubes as $i => $cube ) {
        if ( !$c->intersects($cube) ) {
            continue;
        }
        for ( $x = $cube->xmin; $x <= $cube->xmax; $x++ ) {
            if (!isset($grid[$x])) {
                $grid[$x] = [];
            }
            for ( $y = $cube->ymin; $y <= $cube->ymax; $y++ ) {
                if (!isset($grid[$x][$y])) {
                    $grid[$x][$y] = [];
                }
                for ( $z = $cube->zmin; $z <= $cube->zmax; $z++ ) {
                    $grid[$x][$y][$z] = $instr[$i];
                }
            }
        }
    }
    $res = 0;
    foreach ( $grid as $x ) {
        foreach ( $x as $y ) {
            foreach ( $y as $v) {
                $res += $v;
            }
        }
    }
    return $res;
}

function day22b(array $cubes) {
    return 0;
}

function day22(string $filename): void
{
    $strcontent = file_get_contents($filename);

    $lines = explode("\n", $strcontent);
    $cubes = [];
    $instr = [];
    foreach ( $lines as $line ) {
        if ( strlen($line) > 0 ) {
            $groups=[];
            preg_match("/(on|off) x=(-?\d+)\.\.(-?\d+),y=(-?\d+)\.\.(-?\d+),z=(-?\d+)\.\.(-?\d+)/", $line, $groups);
            $cube = new Cube();
            $cube->xmin = intval($groups[2]);
            $cube->xmax = intval($groups[3]);
            $cube->ymin = intval($groups[4]);
            $cube->ymax = intval($groups[5]);
            $cube->zmin = intval($groups[6]);
            $cube->zmax = intval($groups[7]);
            $cubes[] = $cube;
            $instr[] = ($groups[1] == 'on' ? 1 : 0);
        }
    }

    $day22a = day22a($cubes, $instr);
    print ($day22a . PHP_EOL);
    $day22b = day22b($cubes);
    print ($day22b . PHP_EOL);
}

day22('../data/day22:q.txt');
