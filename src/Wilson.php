<?php

/**
 * https://habr.com/ru/company/darudar/blog/143188/
 * 
 * Что такое $z? Константа, определяющая величину доверительного интервала;
 * Cтатистически доверительный уровень: 
 * установите его в 0.95, чтобы с 95% вероятностью рассчитывать на правильность нижней границы, 
 * в 0.975, чтобы иметь 97.5% вероятности. 
 * Число z в этой функции никогда не меняется. 
 * (Используйте 1.0 = 85%, 1.6 = 95%). 
 * В задаче многорукого бандита используется верхняя граница доверительного интервала.
 */

namespace Bandit;


class Wilson implements Bandit
{

    public function  __construct(
        array $samples,
        float $z = 1.6,
        int $start_count_views = 1
    ) {
        $this->samples = $samples;
        if ($z <= 0) {
            throw new \Exception("Koef must be greater than 0.");
        }
        $this->z = $z;
        $this->start_count = $start_count_views;
    }

    public function predict(bool $sort = false)
    {
        foreach ($this->samples as $rec => $fs) {
            $out[$rec] = $this->wilson_score($fs[1],  $this->clearZero($fs[0]));
        }

        !$sort ?: arsort($out);

        return $out;
    }


    /**
     * Go to https://habrastorage.org/r/w1560/getpro/habr/post_images/e10/05b/433/e1005b43386793db2c1facc142a89b7c.png
     * 
     *
     * @param integer $clicks
     * @param integer $wiews
     * @return float
     */
    private function wilson_score(int $clicks, int $wiews)
    {
        if (!$clicks) return (float) -$wiews;
        // $z = 1.64485; //1.0 = 85%, 1.6 = 95%
        $phat = $clicks / $wiews;

        return ($phat + $this->z ** 2 / (2 * $wiews) - $this->z * sqrt(($phat * (1 - $phat) + $this->z ** 2 / (4 * $wiews)) / $wiews)) / (1 + $this->z ** 2 / $wiews);
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
