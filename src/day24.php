<?php

class AST {
    public $oplit;
    public ?AST $left = null;
    public ?AST $right = null;
    public int $min;
    public int $max;
    public static $ops = [
        'mul' => " * ",
        'add' => ' + ',
        'div' => ' / ',
        'mod' => ' % ',
        'eql' => ' === '
        ];

    public function __toString(): string
    {
        $res = '';
        if ( $this->left !== null ) {
            $res .= '(' . $this->left->__toString() . self::$ops[$this->oplit] . $this->right->__toString() . ')';
        } else {
            $res = $res . $this->oplit;
        }
        return $res;
    }

    public function copy() : AST {
        $ast = new AST();
        $ast->oplit = $this->oplit;
        if ( $this->left !== null ) {
            $ast->left = $this->left->copy();
        }
        if ( $this->right !== null ) {
            $ast->right = $this->right->copy();
        }
        $ast->min = $this->min;
        $ast->max = $this->max;
        return $ast;
    }

    public function minmax() {
        if ( is_numeric($this->oplit) ) {
            $this->min = intval($this->oplit);
            $this->max = intval($this->oplit);
        } elseif ( $this->oplit[0] === '$' && $this->oplit[1] === 'D') {
            $this->min = 1;
            $this->max = 9;
        } elseif ( $this->oplit == 'add' ) {
            if (is_numeric($this->left->oplit) && is_numeric($this->right->oplit) ) {
                $this->oplit = $this->left->oplit + $this->right->oplit;
                $this->left = null;
                $this->right = null;
                $this->min = $this->oplit;
                $this->max = $this->oplit;
            } elseif ( $this->right->oplit === 0) {
                $this->oplit = $this->left->oplit;
                $this->min = $this->left->min;
                $this->max = $this->left->max;
                if ($this->left->left !== null) {
                    $this->right = $this->left->right->copy();
                    $this->left = $this->left->left->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } elseif ($this->left->oplit === 0) {
                $this->oplit = $this->right->oplit;
                $this->min = $this->right->min;
                $this->max = $this->right->max;
                if ($this->right->left !== null) {
                    $this->left = $this->right->left->copy();
                    $this->right = $this->right->right->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } else {
                $this->min = $this->left->min + $this->right->min;
                $this->max = $this->left->max + $this->right->max;
            }
        } elseif ( $this->oplit === 'mul') {
            if ($this->left->oplit === 0 || $this->right->oplit === 0) {
                $this->oplit = 0;
                $this->left = null;
                $this->right = null;
                $this->min = 0;
                $this->max = 0;
            } elseif (is_numeric($this->left->oplit) && is_numeric($this->right->oplit) ) {
                    $this->oplit = $this->left->oplit * $this->right->oplit;
                    $this->left = null;
                    $this->right = null;
                    $this->min = $this->oplit;
                    $this->max = $this->oplit;
            } elseif ( $this->right->oplit === 1) {
                $this->oplit = $this->left->oplit;
                $this->min = $this->left->min;
                $this->max = $this->left->max;
                if ($this->left->left !== null) {
                    $this->right = $this->left->right->copy();
                    $this->left = $this->left->left->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } elseif ($this->left->oplit === 1) {
                $this->oplit = $this->right->oplit;
                $this->min = $this->right->min;
                $this->max = $this->right->max;
                if ($this->right->left !== null ) {
                    $this->left = $this->right->right->copy();
                    $this->right = $this->right->right->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } else {
                $this->min = $this->left->min * $this->right->min;
                $this->max = $this->left->max * $this->right->max;
            }
        } elseif ( $this->oplit === 'eql') {
            if (is_numeric($this->left->oplit) && is_numeric($this->right->oplit) && $this->left->oplit === $this->right->oplit) {
                $this->oplit = 1;
                $this->min = 1;
                $this->max = 1;
                $this->left = null;
                $this->right = null;
            } elseif ( $this->left->min > $this->right->max || $this->left->max < $this->right->min ) {
                $this->oplit = 0;
                $this->min = 0;
                $this->max = 0;
                $this->left = null;
                $this->right = null;
            } else {
                $this->min = 0;
                $this->max = 1;
            }
        } elseif ($this->oplit === 'div') {
            if (is_numeric($this->left->oplit) && is_numeric($this->right->oplit) ) {
                $this->oplit = $this->left->oplit / $this->right->oplit;
                $this->left = null;
                $this->right = null;
                $this->min = $this->oplit;
                $this->max = $this->oplit;
            } elseif ( $this->right->oplit === 1) {
                $this->oplit = $this->left->oplit;
                $this->min = $this->left->min;
                $this->max = $this->left->max;
                if ($this->left->left !== null) {
                    $this->right = $this->left->right->copy();
                    $this->left = $this->left->left->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } else {
                $this->min = $this->left->min / $this->right->max;
                $this->max = $this->left->max / $this->right->min;
            }
        } elseif ($this->oplit === 'mod') {
            if (is_numeric($this->left->oplit) && is_numeric($this->right->oplit) ) {
                $this->oplit = $this->left->oplit % $this->right->oplit;
                $this->left = null;
                $this->right = null;
                $this->min = $this->oplit;
                $this->max = $this->oplit;
            } else if ( $this->left->max < $this->right->oplit ) {
                $this->oplit = $this->left->oplit;
                $this->min = $this->left->min;
                $this->max = $this->left->max;
                if ($this->left->left !== null ) {
                    $this->right = $this->left->right->copy();
                    $this->left = $this->left->left->copy();
                } else {
                    $this->left = null;
                    $this->right = null;
                }
            } else {
                $this->min = 0;
                $this->max = $this->right->oplit;
            }
        }

        if ($this->min > $this->max) {
            // happens if we multiply by negative
            $t = $this->min;
            $this->min = $this->max;
            $this->max = $t;
        }
        if ( $this->min === $this->max ) {
            $this->oplit = $this->min;
            $this->left = null;
            $this->right = null;
        }
    }
}

function day24a( array $lines ) : AST {
    $w = new AST();
    $w->oplit = 0;
    $x = new AST();
    $x->oplit = 0;
    $y = new AST();
    $y->oplit = 0;
    $z = new AST();
    $z->oplit = 0;
    $w->minmax();
    $x->minmax();
    $y->minmax();
    $z->minmax();

    $d = 0;
    foreach ( $lines as $line ) {
        if (strlen($line) > 0) {
            $toks = explode(' ', $line);
            if ($toks[0] === 'inp') {
                ${$toks[1]}->oplit = '$D' . $d;
                print '$Z' . $d . ' = ' . $z . ';' . PHP_EOL;
                $z->oplit = '$Z' . $d;
                $z->left = null;
                $z->right = null;
                $d++;
            } else {
                $ast = new AST();
                $ast->oplit = $toks[0];
                if ( is_numeric($toks[1])) {
                    $ast->left = new AST();
                    $ast->left->oplit = intval($toks[1]);
                    $ast->left->minmax();
                } else {
                    $ast->left = ${$toks[1]}->copy();
                }
                if ( is_numeric($toks[2])) {
                    $ast->right = new AST();
                    $ast->right->oplit = intval($toks[2]);
                    $ast->right->minmax();
                } else {
                    $ast->right = ${$toks[2]}->copy();
                }
                ${$toks[1]} = $ast;
            }
            ${$toks[1]}->minmax();
            print '';
        }
    }
    return $z;
}

function day24b( array $lines ) : int {
    return 0;
}

function interpretDay24($D0, $D1, $D2, $D3, $D4, $D5, $D6, $D7, $D8, $D9, $D10, $D11, $D12, $D13) {
    $Z1 = ($D0 + 12);
    $Z2 = (($Z1 * 26) + ($D1 + 9));
    $Z3 = (($Z2 * 26) + ($D2 + 8));
    $Z4 = ((intdiv($Z3, 26) * ((25 * (((($Z3 % 26) + -8) === $D3) === 0)) + 1)) + (($D3 + 3) * (((($Z3 % 26) + -8) === $D3) === 0)));
    $Z5 = (($Z4 * 26) + $D4);
    $Z6 = (($Z5 * 26) + ($D5 + 11));
    $Z7 = (($Z6 * 26) + ($D6 + 10));
    $Z8 = (((intdiv($Z7,26)) * ((25 * (((($Z7 % 26) + -11) === $D7) === 0)) + 1)) + (($D7 + 13) * (((($Z7 % 26) + -11) === $D7) === 0)));
    $Z9 = (($Z8 * 26) + ($D8 + 3));
    $Z10 = ((intdiv($Z9,26) * ((25 * (((($Z9 % 26) + -1) === $D9) === 0)) + 1)) + (($D9 + 10) * (((($Z9 % 26) + -1) === $D9) === 0)));
    $Z11 = ((intdiv($Z10,26) * ((25 * (((($Z10 % 26) + -8) === $D10) === 0)) + 1)) + (($D10 + 10) * (((($Z10 % 26) + -8) === $D10) === 0)));
    $Z12 = ((intdiv($Z11,26) * ((25 * (((($Z11 % 26) + -5) === $D11) === 0)) + 1)) + (($D11 + 14) * (((($Z11 % 26) + -5) === $D11) === 0)));
    $Z13 = ((intdiv($Z12,26) * ((25 * (((($Z12 % 26) + -16) === $D12) === 0)) + 1)) + (($D12 + 6) * (((($Z12 % 26) + -16) === $D12) === 0)));
    if ($Z3 < 0 || $Z7 < 0 || $Z9 < 0 || $Z10 < 0 || $Z11 < 0 || $Z12 < 0 || $Z13<0) {
        throw new Exception("nope");
    }
    return ((intdiv($Z13,26) * ((25 * (((($Z13 % 26) + -6) === $D13) === 0)) + 1)) + (($D13 + 5) * (((($Z13 % 26) + -6) === $D13) === 0)));
}

function day24(string $filename ) {
    $strcontent = file_get_contents($filename );
    $lines = explode("\n", $strcontent);
    $day24a = day24a( $lines );
    print ( $day24a . PHP_EOL);

    print interpretDay24(9,9,8,9,9,9,9,9,9,9,9,9,9,9) . PHP_EOL;

    $day24b = day24b( $lines );
    print ( $day24b . PHP_EOL);
}

day24( '../data/day24.txt' );
