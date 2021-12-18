<?php

use JetBrains\PhpStorm\Pure;

class Snailfish {
    public $left = null;
    public $right = null;
    public $parent;
    public $value = -1;
    public $depth = 0;

    public static function createSnailfish( string $line, int $depth ) : Snailfish {
        $s = new Snailfish();
        $s->depth = $depth;
        $chars = str_split( $line );
        $innerIndexes = [];
        $isFirst = true;
        for ( $i = 1; $i < strlen($line) - 1; $i++ ) {
            if ( $chars[$i] === '[' ) {
                $innerIndexes[] = $i;
            } elseif ( $chars[$i] == ']') {
                $j = array_pop($innerIndexes);
                if ( count($innerIndexes) === 0) {
                    if ( $isFirst ) {
                        $s->left = static::createSnailfish( substr( $line, $j, $i - $j + 1), $depth + 1 );
                        $s->left->parent = $s;
                        $isFirst = false;
                    } else {
                        $s->right = static::createSnailfish( substr( $line, $j, $i - $j + 1), $depth + 1 );
                        $s->right->parent = $s;
                        $s->right->depth = $s->depth + 1;
                    }
                }
            } elseif (count($innerIndexes) > 0 || $chars[$i] === ',') {
                continue;
            } else {
                if ( $isFirst ) {
                    $s->left = new Snailfish();
                    $s->left->value = intval($chars[$i]);
                    $s->left->parent = $s;
                    $s->left->depth = $s->depth + 1;
                    $isFirst = false;
                } else {
                    $s->right = new Snailfish();
                    $s->right->value = intval($chars[$i]);
                    $s->right->parent = $s;
                    $s->right->depth = $s->depth + 1;
                }
            }
        }
        return $s;
    }

    #[Pure] public function __toString() : string {
        if ($this->value !== -1 ) {
            return strval($this->value);
        }
        return '[' . strval($this->left) . ',' . strval($this->right) . ']';
    }

    public function distributeToLeft() {
        $num = $this->left->value;
        if ( $num === -1 ) {
            throw new Exception("shouldn't happen");
        }

        $curr = $this;
        $parent = $curr->parent;
        while ( $parent !== null && $parent->left == $curr ) {
            $curr = $parent;
            $parent = $curr->parent;
        }
        if ( $parent !== null) {
            $curr = $parent->left;
            while ( $curr->value === -1 ) {
                $curr = $curr->right;
            }
            $curr->value += $num;
        }
    }

    public function distributetoRight() {
        $num = $this->right->value;
        if ( $num === -1 ) {
            throw new Exception("shouldn't happen");
        }
        $curr = $this;
        $parent = $curr->parent;
        while ( $parent !== null && $parent->right === $curr ) {
            $curr = $parent;
            $parent = $curr->parent;
        }
        if ( $parent !== null) {
            $curr = $parent->right;
            while ( $curr->value === -1 ) {
                $curr = $curr->left;
            }
            $curr->value += $num;
        }
    }

    public function explode() : bool {
        if ( $this->depth === 4 && ( $this->left !== null ) ) {
            $this->distributeToLeft();
            $this->distributeToRight();
            $this->left = null;
            $this->right = null;
            $this->value = 0;
            return true;
        }
        if ( $this->left !== null) {
            $exploded = $this->left->explode();
            if ($exploded) {
                return true;
            }
        }
        if ( $this->right !== null ) {
            return $this->right->explode();
        }
        return false;
    }

    public function split() : bool {
        if ( $this->value >= 10 ) {
            $val = $this->value;
            $this->value = -1;
            $this->left = new Snailfish();
            $this->left->value = intdiv( $val, 2 );
            $this->left->parent = $this;
            $this->left->isLeft = true;
            $this->left->depth = $this->depth + 1;
            $this->right = new Snailfish();
            $this->right->value = $val - $this->left->value;
            $this->right->parent = $this;
            $this->right->isLeft = true;
            $this->right->depth = $this->depth + 1;
            return true;
        }
        if ( $this->left !== null) {
            $split = $this->left->split();
            if ($split) {
                return true;
            }
        }
        if ( $this->right !== null ) {
            return $this->right->split();
        }
        return false;
    }

    public function reduce() {
        while (true) {
            $exploded = $this->explode();
            if ( $exploded ) {
                continue;
            }
            $split = $this->split();
            if ( !$split ) {
                break;
            }
        }
    }

    public function magnitude() : int {
        if ( $this->value !== -1 ) {
            return $this->value;
        }
        return 3 * $this->left->magnitude() + 2 * $this->right->magnitude();
    }

    public function increaseDepth() {
        $this->depth++;
        if ( $this->left !== null ) {
            $this->left->increaseDepth();
            $this->right->increaseDepth();
        }
    }

    public function copy() : Snailfish {
        $s = new Snailfish();
        $s->value = $this->value;
        $s->depth = $this->depth;
        if ( $this->left !== null ) {
            $s->left = $this->left->copy();
            $s->left->parent = $s;
            $s->right = $this->right->copy();
            $s->right->parent = $s;
        }
        return $s;
    }

    public static function add( Snailfish $a, Snailfish $b) : Snailfish {
        $res = new Snailfish();
        $res->left = $a->copy();
        $res->left->increaseDepth();
        $res->left->parent = $res;
        $res->right = $b->copy();
        $res->right->increaseDepth();
        $res->right->parent = $res;
        $res->reduce();
        return $res;
    }
}

function day18a( array $snailfishes ) {
    $sum = $snailfishes[0];
    for ( $i = 1; $i < count($snailfishes); $i++) {
        $sum = Snailfish::add( $sum, $snailfishes[$i] );
    }
    return $sum->magnitude();
}

function day18b( array $snailfishes ) {
    $maxMag = 0;
    for ( $i = 0; $i < count($snailfishes); $i++) {
        for ( $j = 0; $j < count($snailfishes); $j++) {
            if ( $i !== $j ) {
                $testMag = Snailfish::add( $snailfishes[$i], $snailfishes[$j] )->magnitude();
                if ($testMag > $maxMag) {
                    $maxMag = $testMag;
                }
            }
        }
    }
    return $maxMag;
}


function day18(string $filename): void
{
    $strcontent = file_get_contents($filename);

    $snailfishes = [];
    $lines = explode("\n", $strcontent);
    foreach ( $lines as $line ) {
        if ( strlen($line) > 0 ) {
            $fish = Snailfish::createSnailfish( $line, 0 );
            $snailfishes[] = $fish;
        }
    }

    $day18a = day18a($snailfishes);
    $day18b = day18b($snailfishes);

    print ($day18a . PHP_EOL);
    print ($day18b . PHP_EOL);
}

day18('../data/day18.txt');
