<?php

namespace Bandit;


class UcbTuned implements Bandit
{


    public function __construct(
        array $samples,
        float $koef = 0.3,
        int $start_count_views = 1
    ) {
        $this->samples = $samples;
        if ($koef <= 0) {
            throw new \Exception("Koef must be greater than 0.");
        }
        $this->koef = $koef;
        $this->start_count = $start_count_views;
    }


    /**
     * weight = Sample_Mean + koef*C
     * 
     * C = √( (logN / n) x min(1/4, V(n)) )
     * 
     *   where V(n) is an upper confidence bound on the variance of the bandit, i.e.
     * 
     * V(n) = Σ(x_i² / n) - (Σ (x_i / n))² + √(2log(N) / n)
     * 
     * Go to https://slidetodoc.com/presentation_image_h/149b7ee91f11953abf7d109965b94b29/image-52.jpg
     * 
     * @param bool $sort
     * @return array
     */
    public function predict(bool $sort = false): array
    {
        if (($N = array_sum(array_column($this->samples, 0))) <= 1) throw new \Exception("N must be greater than 1.");

        foreach ($this->samples as $rec => $fs) {

            $fs[0] = $this->clearZero($fs[0]);

            $sample_mean = $fs[1] / $fs[0];

            $variance_bound = $fs[1] ** 2 / $fs[0] - $sample_mean ** 2;

            $variance_bound += sqrt(2 * log($N) / $fs[0]);

            $c = sqrt(min([$variance_bound, 0.25]) * log($N) / $fs[0]);

            $out[$rec] = $sample_mean + $this->koef * $c;
        }

        !$sort ?: arsort($out);

        return $out;
    }

    /**
     * @param integer $fs
     * @return integer
     */
    private function clearZero(int $fs): int
    {
        return $fs ? $fs : $this->start_count;
    }
}
