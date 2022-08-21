<?php
ini_set('memory_limit', -1);

$start = microtime(true);
$memory = memory_get_usage();


class ThompsonSempl
{
    public function __construct(
        array $observations,
        float $a = 1,
        float $b = 1
    ) {
        $this->observations = $observations;

        if ($a < 1 || $b < 1) {
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
            $out[$player] = round($this->beta(
                $features[1] + $this->a,
                $features[0] - $features[1] + $this->b
            ), 4);
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
        }
    }
}

for ($i = 0; $i < 100000; $i++) {
    $out[] = [rand(10000, 1000000), rand(0, 9999)];
}

$th = new ThompsonSempl($out);

$th->predict(true);

$memory = memory_get_usage() - $memory;
$time = microtime(true) - $start;

$i = 0;
while (floor($memory / 1024) > 0) {
    $i++;
    $memory /= 1024;
}

$name = array('байт', 'КБ', 'МБ');
$memory = round($memory, 2) . ' ' . $name[$i];

echo round($time, 4) . ' сек. / ' . $memory;
