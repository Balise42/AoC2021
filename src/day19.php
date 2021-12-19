<?php

use Ds\Hashable;
use Ds\Set;

function multMatrices(array $m1, array $m2) : array
{
    $size = count($m1);
    $m = [];
    for ($i = 0; $i < $size; $i++) {
        $m[$i] = [];
        for ($j = 0; $j < $size; $j++) {
            $m[$i][$j] = 0;
            for ($k = 0; $k < $size; $k++) {
                $m[$i][$j] += $m1[$i][$k] * $m2[$k][$j];
            }
        }
    }
    return $m;
}

global $rotations;

function getRots() : array {
    global $rotations;
    if ($rotations !== null) {
        return $rotations;
    }
    $baseRots = [
        // identity
        [[1, 0, 0], [0, 1, 0], [0, 0, 1]],

        //rots around X
        [[1, 0, 0], [0, 0, 1], [0, -1, 0]],
        [[1, 0, 0], [0, -1, 0], [0, 0, -1]],
        [[1, 0, 0], [0, 0, -1], [0, 1, 0]],

        //rots around Y
        [[0, 0, -1], [0, 1, 0], [1, 0, 0]],
        [[-1, 0, 0], [0, 1, 0], [0, 0, -1]],
        [[0, 0, 1], [0, 1, 0], [-1, 0, 0]],

        //rots around Z
        [[0, -1, 0],[1, 0, 0],[0, 0, 1]],
        [[-1, 0, 0],[0, -1, 0],[0, 0, 1]],
        [[0, 1, 0],[-1, 0, 0],[0, 0, 1]]
    ];

    $rots = $baseRots;
    for ( $i = 1; $i <= 3; $i++) {
        for ( $j = 4; $j<=6; $j++) {
            $r1 = multMatrices($baseRots[$i], $baseRots[$j]);
            $r2 = multMatrices($baseRots[$i], $baseRots[$j + 3]);
            if (array_search( $r1, $rots ) === false ) {
                $rots[] = $r1;
            }
            if (array_search( $r2, $rots ) === false ) {
                $rots[] = $r2;
            }
        }
    }
    $rotations = $rots;
    return $rots;
}

class Point3D implements Hashable {
    public int $x;
    public int $y;
    public int $z;

    public static function create( int $x, int $y, int $z ) : Point3D {
        $p = new Point3D();
        $p->x = $x;
        $p->y = $y;
        $p->z = $z;
        return $p;
    }

    public function transform( array $matrix ) : Point3D {
        $p = new Point3D;
        $p->x = $this->x * $matrix[0][0] + $this->y * $matrix[0][1] + $this->z * $matrix[0][2] + $matrix[0][3];
        $p->y = $this->x * $matrix[1][0] + $this->y * $matrix[1][1] + $this->z * $matrix[1][2] + $matrix[1][3];
        $p->z = $this->x * $matrix[2][0] + $this->y * $matrix[2][1] + $this->z * $matrix[2][2] + $matrix[2][3];
        return $p;
    }

    public function getPossibleTransformations( Point3D $p ) : array {
        $rots = getRots();

        $transf = [];
        foreach ( $rots as $rot ) {
            $t = $rot;
            $t[0][3] = $p->x - $t[0][0] * $this->x - $t[0][1] * $this->y - $t[0][2] * $this->z;
            $t[1][3] = $p->y - $t[1][0] * $this->x - $t[1][1] * $this->y - $t[1][2] * $this->z;
            $t[2][3] = $p->z - $t[2][0] * $this->x - $t[2][1] * $this->y - $t[2][2] * $this->z;
            $transf[] = $t;
        }

        return $transf;
    }

    public function __toString() : string {
        return '[' . $this->x . ', ' . $this->y . ', ' . $this->z . ']';
    }

    public function equals($obj): bool
    {
        return $this->x == $obj->x && $this->y == $obj->y && $this->z == $obj->z;
    }

    public function hash()
    {
        return 47 + 11 * $this->x + 13*$this->y + 23 * $this->z;
    }
}

function getOverlappingTransform( array $b1, array $b2 ) : ?array {
    /** @var Point3D $p1 */
    foreach ( $b1 as $i=> $p1 ) {
        if ( $i > count($b1) - 12) {
            continue;
        }
        /** @var Point3D $p2 */
      foreach ( $b2 as $p2 ) {
            $transfs = $p1->getPossibleTransformations( $p2 );
            foreach ( $transfs as $transf ) {
                $b1transf = array_map( fn($value) => $value->transform($transf), $b1 );
                if (count (array_intersect( $b1transf, $b2 ) ) >= 12 ) {
                    return $transf;
                }
            }
        }
    }
    return null;
}

function getDistances( array $b ) {
    $dists = [];
    for ($i = 0; $i < count($b); $i++ ) {
        for ($j = $i+1; $j < count($b); $j++ ) {
            $dists[] = ($b[$i]->x - $b[$j]->x) * ($b[$i]->x - $b[$j]->x)
                + ($b[$i]->y - $b[$j]->y) * ($b[$i]->y - $b[$j]->y)
                + ($b[$i]->z - $b[$j]->z) * ($b[$i]->z - $b[$j]->z);
        }
    }
    return $dists;
}

function couldMatch( array $b1, array $b2 ) {
    $dists1 = getDistances($b1);
    $dists2 = getDistances($b2);
    return count(array_intersect( $dists1, $dists2) )>= 12;
}

function getNormalizedBeacons( array $beacons, int $i, $transforms, array $path ) {
    if ( isset($transforms[$i][0]) ) {
        return array_map( fn($value) => $value->transform($transforms[$i][0]), $beacons );
    } else {
        foreach ( $transforms[$i] as $j => $transform) {
            if ( array_search( $j, $path ) === false ) {
                $newPath = $path;
                $newPath[] = $j;
                $norm = getNormalizedBeacons(array_map( fn($value) => $value->transform($transforms[$i][$j]), $beacons), $j, $transforms, $newPath );
                if (count($norm) > 0) {
                    return $norm;
                }
            }
        }
    }
    return [];
}

global $transforms;

function day19a(array $beacons) : int {
    /*$p1 = Point3D::create(0, 0, 0);
    $transf = $p1->getPossibleTransformations($p1);

    foreach ($transf as $t) {
        print ( Point3D::create(1, 2, 3)->transform($t) . PHP_EOL);
    }*/

    /*$p1 = Point3D::create(-618,-824,-621);
    $p2 = Point3D::create(686,422,578);
    $transf = $p1->getPossibleTransformations( $p2 )

    foreach ($transf as $t) {
        print ( $p1->transform($t) . PHP_EOL);
    }*/

    global $transforms;
    $transforms = [];
    for ( $i = 0; $i < count($beacons); $i++) {
        $transforms[] = [];
    }

    for ( $i = 0; $i < count($beacons); $i++) {
        for ( $j = $i+1; $j < count($beacons); $j++ ) {
            if ($i !== $j && couldMatch($beacons[$i], $beacons[$j])) {
                $mat = getOverlappingTransform($beacons[$i], $beacons[$j]);
                if ($mat !== null) {
                    print $i . ' ' . $j . PHP_EOL;
                    $transforms[$i][$j] = $mat;
                    $mat2 = getOverlappingTransform($beacons[$j], $beacons[$i]);
                    if ($mat2 !== null) {
                        $transforms[$j][$i] = $mat2;
                    }
                }
            }
        }
    }

    $allBeacons = new Set();
    $allBeacons->add( ...$beacons[0]);
    for ( $i = 1; $i < count($beacons); $i++) {
        $path = [];
        $allBeacons->add( ...getNormalizedBeacons( $beacons[$i], $i, $transforms, $path ));
    }

    return count($allBeacons);
}

function day19b(array $beacons) : int {
    global $transforms;
    $orig = Point3D::create(0, 0, 0);
    $scanners = [];
    for ( $i = 0; $i < count($beacons); $i++) {
        $path = [];
        $scanners[] = getNormalizedBeacons( [$orig], $i, $transforms, $path)[0];
    }
    $maxMan = 0;
    for ( $i = 0; $i < count($scanners); $i++) {
        for ( $j = $i + 1; $j < count($scanners); $j++) {
            $dist = abs( $scanners[$i]->x - $scanners[$j]->x)
                + abs( $scanners[$i]->y - $scanners[$j]->y)
                + abs( $scanners[$i]->z - $scanners[$j]->z);
            if ($dist > $maxMan) {
                $maxMan = $dist;
            }
        }
    }
    return $maxMan;
}

function day19(string $filename): void
{
    $strcontent = file_get_contents($filename);

    $beacons = [];
    $lines = explode("\n", $strcontent);
    foreach ( $lines as $line ) {
        if ( strlen($line) > 0 ) {
            if ( $line[0] === '-' && $line[1] === '-') {
                $beacons[] = [];
            } else {
                $coords = explode(',', $line);
                $x = intval($coords[0]);
                $y = intval($coords[1]);
                $z = intval($coords[2]);
                $beacons[ count($beacons) - 1][] = Point3D::create($x, $y, $z);
            }
        }
    }

    $day19a = day19a($beacons);
    $day19b = day19b($beacons);

    print ($day19a . PHP_EOL);
    print ($day19b . PHP_EOL);
}

day19('../data/day19.txt');