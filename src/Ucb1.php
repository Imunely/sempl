<?php

namespace Bandit;


class Ucb1 implements Bandit
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
     * weight = Sample_Mean + koef √(logN / n)
     * Go to https://hsto.org/getpro/habr/upload_files/04d/206/497/04d206497897931be487004430dde8e9.png
     * 
     * @param bool $sort
     * @return array
     */
    public function predict(bool $sort = false): array
    {
        if (($N = array_sum(array_column($this->samples, 0))) <= 1) throw new \Exception("N must be greater than 1.");

        foreach ($this->samples as $rec => $fs) {
            $fs[0] = $this->clearZero($fs[0]);
            $out[$rec] = ($fs[1] / $fs[0]) + $this->koef * sqrt(log($N) / $fs[0]);
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
