<?php

class Submarine {
    /** @var int  */
    public $x;
    /** @var int */
    public $z;

    public function __construct() {
        $this->x = 0;
        $this->z = 0;
    }

    public function forward( int $offset ) {
        $this->x = $this->x + $offset;
    }

    public function down( int $offset ) {
        $this->z = $this->z + $offset;
    }

    public function up( int $offset ) {
        $this->z = $this->z - $offset;
    }

    public function execute( array $lines ) {
        $commands = [];
        $offsets = [];
        foreach ( $lines as $line ) {
            $toks = explode(" ", $line);
            if ( sizeof( $toks ) == 2) {
                $commands[] = $toks[0];
                $offsets[] = intval($toks[1]);
            }
        }
        for ( $i = 0; $i < sizeof( $commands ); $i++) {
            $command = $commands[$i];
            $this->$command( $offsets[$i] );
        }
    }
}

class SubmarineB extends Submarine {
    /** @var int */
    public $aim;

    public function __construct() {
        parent::__construct();
        $this->aim = 0;
    }

    public function forward( int $offset ) {
        $this->x = $this->x + $offset;
        $this->z = $this->z + $this->aim * $offset;
    }

    public function down( int $offset ) {
        $this->aim = $this->aim + $offset;
    }

    public function up( int $offset ) {
        $this->aim = $this->aim - $offset;
    }
}

function day02( string $filename ) : void {
    $strcontent = file_get_contents($filename );
    $lines = explode("\n", $strcontent);
    $day02a = day02a( $lines );
    print ( $day02a . PHP_EOL);
    $day02b = day02b( $lines );
    print ( $day02b . PHP_EOL);
}

function day02a( array $lines ) : int {
    $sub = new Submarine();
    $sub->execute( $lines );
    return $sub->x * $sub->z;
}

function day02b( array $lines ) : int {
    $sub = new SubmarineB();
    $sub->execute( $lines );
    return $sub->x * $sub->z;
}

day02( '../data/day02.txt' );