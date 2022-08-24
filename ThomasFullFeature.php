<?php

class ThompsonSempl
{
    public function __construct(
        array $observations,
        float $a = 1,
        float $b = 1
    ) {
        $this->observations = $observations;

        if ($a <= 0 || $b <= 0) {
            throw new \InvalidArgumentException("Alpha and beta must be greater than 0.");
        }

        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @param boolean $sort
     * @return array
     */
    public function predict(bool $sort = false): array
    {
        foreach ($this->observations as $player => $features) {
            if ($features[1] > $features[0]) {
                $features[1] = $features[0];
            }
            
            $alpha = $features[1] + $this->a;
            $beta = ($features[0] - $features[1] + $this->b);
            $out[$player] = $alpha / ($alpha + $beta);
        }

        !$sort ?: arsort($out);

        return $out;
    }

    /**
     * @param float $a
     * @param float $b
     * @return float
     */
    public function beta(float $a, float $b)
    {
        $ag = $this->draw($a, 1);
        $bg = $this->draw($b, 1);

        return ($ag / ($ag + $bg));
    }

    /**
     * @param float $alpha
     * @param float $beta
     * @return float
     */
    private function draw(float $alpha, float $beta)
    {
        if ($alpha > 1) {
            $ainv = sqrt(2.0 * $alpha - 1.0);
            $bbb = $alpha - log(4.0);
            $ccc = $alpha + $ainv;

            while (true) {
                $u1 = rand() / getrandmax();
                if (!((1e-7 < $u1) && ($u1 < 0.9999999))) {
                    continue;
                }
                $u2 = 1.0 - (rand() / getrandmax());
                $v = log($u1 / (1.0 - $u1)) / $ainv;
                $x = $alpha * exp($v);
                $z = $u1 * $u1 * $u2;
                $r = $bbb + $ccc * $v - $x;
                $SG_MAGICCONST = 1 + log(4.5);
                if ($r + $SG_MAGICCONST - 4.5 * $z >= 0.0 || $r >= log($z)) {
                    return $x * $beta;
                }
            }
        } elseif ($alpha == 1.0) {
            $u = rand() / getrandmax();
            while ($u <= 1e-7) {
                $u = rand() / getrandmax();
            }
            return -log($u) * $beta;
        } else { // 0 < alpha < 1
            // Uses ALGORITHM GS of Statistical Computing - Kennedy & Gentle
            while (true) {
                $u3 = rand() / getrandmax();
                $b = (M_E + $alpha) / M_E;
                $p = $b * $u3;
                if ($p <= 1.0) {
                    $x = pow($p, (1.0 / $alpha));
                } else {
                    $x = log(($b - $p) / $alpha);
                }
                $u4 = rand() / getrandmax();
                if ($p > 1.0) {
                    if ($u4 <= pow($x, ($alpha - 1.0))) {
                        break;
                    }
                } elseif ($u4 <= exp(-$x)) {
                    break;
                }
            }
            return $x * $beta;
        }
    }
}
