<?php


function getNumPathsPartA ( array $path, array $graph ) : int {
    $last = $path[array_key_last( $path )];
    if ( $last === 'end') {
        return 1;
    }
    $res = 0;
    foreach ( $graph[$last] as $neigh ) {
        if ( $neigh === 'start' ) {
            continue;
        }
        if ( preg_match('/[A-Z]+/', $neigh ) ) {
            $newPath = $path;
            $newPath[] = $neigh;
            $res += getNumPathsPartA( $newPath, $graph );
        } else if ( array_search($neigh, $path) === false ) {
            $newPath = $path;
            $newPath[] = $neigh;
            $res += getNumPathsPartA( $newPath, $graph );
        }
    }
    return $res;
}

function getNumPathsPartB ( array $path, array $graph, bool $double ) : int {
    $last = $path[array_key_last( $path )];
    if ( $last === 'end') {
        return 1;
    }
    $res = 0;
    foreach ( $graph[$last] as $neigh ) {
        if ( $neigh === 'start' ) {
            continue;
        }
        if ( preg_match('/[A-Z]+/', $neigh ) ) {
            $newPath = $path;
            $newPath[] = $neigh;
            $res += getNumPathsPartB( $newPath, $graph, $double );
        } else if ( !$double ) {
            $newPath = $path;
            $newPath[] = $neigh;
            $newDouble = ( array_search($neigh, $path) !== false);
            $res += getNumPathsPartB( $newPath, $graph, $newDouble );
        } else if ( array_search($neigh, $path) === false ) {
            $newPath = $path;
            $newPath[] = $neigh;
            $res += getNumPathsPartB( $newPath, $graph, $double );
        }
    }
    return $res;
}


function day12a( array $graph ) : int {
    return getNumPathsPartA( ['start'], $graph );
}


function day12b( array $graph ) : int {
    return getNumPathsPartB( ['start'], $graph, false );
}

function day12( string $filename ) : void {
    $strcontent = file_get_contents( $filename );
    $lines = explode( "\n",  $strcontent);

    $graph = [];
    foreach ( $lines as $line ) {
        if ( strlen( $line ) > 0 ) {
            $vertices = explode('-', $line);
            if ( !isset($graph[$vertices[0]])) {
                $graph[$vertices[0]] = [];
            }
            if ( !isset($graph[$vertices[1]])) {
                $graph[$vertices[1]] = [];
            }
            $graph[$vertices[0]][] = $vertices[1];
            $graph[$vertices[1]][] = $vertices[0];
        }
    }

    $day12a = day12a( $graph );
    $day12b = day12b( $graph );
    print ( $day12a . PHP_EOL);
    print ( $day12b . PHP_EOL);
}

day12( '../data/day12.txt');
