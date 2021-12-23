<?php

class Cube
{
    public $xmin;
    public $xmax;
    public $ymin;
    public $ymax;
    public $zmin;
    public $zmax;

    public function contains(int $x, int $y, int $z): bool
    {
        return $x >= $this->xmin && $x <= $this->xmax && $y >= $this->ymin && $y <= $this->ymax && $z >= $this->zmin && $z <= $this->zmax;
    }

    public function containsCube(Cube $c) :bool {
        return $this->contains($c->xmin, $c->ymin, $c->zmin)
            && $this->contains($c->xmax, $c->ymax, $c->zmax);
    }

    public function intersects(Cube $c) : bool
    {
        return !($c->xmax < $this->xmin
            || $c->xmin > $this->xmax
            || $c->ymax < $this->ymin
            || $c->ymin > $this->ymax
            || $c->zmax < $this->zmin
            || $c->zmin > $this->zmax
        );
    }

    public function isValid() :bool {
        return $this->xmin <= $this->xmax && $this->ymin <= $this->ymax && $this->zmin <= $this->zmax;
    }

    public function getSplitCubes(Cube $c): array
    {
        $xs = [$this->xmin, $this->xmax, $c->xmin, $c->xmax];
        $ys = [$this->ymin, $this->ymax, $c->ymin, $c->ymax];
        $zs = [$this->zmin, $this->zmax, $c->zmin, $c->zmax];

        sort($xs);
        sort($ys);
        sort($zs);

        $cubes = [];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                for ($k = 0; $k < 3; $k++) {
                    $cube = new Cube();
                    $cube->xmin = $xs[$i] + ( $i === 2 ? 1 : 0);
                    $cube->xmax = $xs[$i + 1] - ( $i === 0 ? 1 : 0 );
                    $cube->ymin = $ys[$j] + ( $j=== 2 ? 1 : 0);
                    $cube->ymax = $ys[$j + 1] - ( $j === 0 ? 1 : 0 );
                    $cube->zmin = $zs[$k] + ( $k === 2 ? 1 : 0);
                    $cube->zmax = $zs[$k + 1] - ( $k === 0 ? 1 : 0 );
                    if ($cube->isValid()) {
                        $cubes[] = $cube;
                    }
                }
            }
        }
        return $cubes;
    }

    public function volume() : int {
        return ( $this->xmax - $this->xmin + 1 ) * ( $this->ymax - $this->ymin + 1) * ($this->zmax - $this->zmin + 1);
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

function day22b(array $toInsert, array $instr) : int {
    $onCubes = [];
    while ( $toInsert != null ) {
        /** @var Cube $newCube */
        $newCube = array_shift( $toInsert );
        $ins = array_shift( $instr );
        /** @var Cube $cube */
        $shouldInsertNewCube = ($ins === 1);
        foreach ( $onCubes as $key => $cube ) {
            if ( $newCube->intersects( $cube ) ) {
                unset ( $onCubes[$key] );
                $smallCubes = $newCube->getSplitCubes($cube);
                if ( $ins === 1 ) {
                    // we're keeping all the cubes that in the union of both cubes
                    foreach ($smallCubes as $smallCube) {
                        if ( $cube->containsCube($smallCube)) {
                            $onCubes[] = $smallCube;
                        } else if ($newCube->containsCube($smallCube)) {
                            array_unshift( $toInsert, $smallCube);
                            array_unshift( $instr, 1);
                        }
                    }
                    $shouldInsertNewCube = false;
                    break;
                } else {
                    foreach ($smallCubes as $smallCube) {
                        // and here we keep all small cubes already in the "on" but NOT in the new cube
                        if ($cube-> containsCube($smallCube) && !$newCube->containsCube($smallCube)) {
                            $onCubes[] = $smallCube;
                        }
                    }
                }
            }
        }
        if ($shouldInsertNewCube) {
            $onCubes[] = $newCube;
        }
    }

    $res = 0;

    foreach ( $onCubes as $cube ) {
        $res += $cube->volume();
    }
    return $res;
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
    $day22b = day22b($cubes, $instr);
    print ($day22b . PHP_EOL);
}

day22('../data/day22.txt');
