<?php


require_once "vendor/autoload.php";

use gburtini\Distributions\Beta;

class ThompsonSempl
{

    private $thomas = [];

    public function __construct(
        array $observations,
        float $a = 1,
        float $b = 1
    ) 
    {

        $this->players = array_keys($observations);
        $this->observations = $observations;
        $this->a = $a;
        $this->b = $b;

        /**
         * Здесь мы генерируем случаные значения {0;1} для игроков A1, A2, ..., An
         * [
         *   A1=>[1,0,1,...], 
         *   A2=>[0,0,1,...], 
         *   и тд
         * ]
         * В вашем случае это заголовки записей и их клики = 1 (0 = отсуствие клика) при каждом просмотре
         */

        $this->max = $this->generateStatic($this->players, $observations)['max'];
    }


    public function predict(): void
    {
        $this->thomas = [
            'rewards' => array_fill_keys($this->players, 0),
            'penalties' => array_fill_keys($this->players, 0),
            'total_reward' => 0,
            'selected_player' => []
        ];

        for ($n = 0; $n < $this->max; $n++) {
            $bandit = 0;
            $beta_max = 0;
            foreach ($this->players as $key => $value) {
                $bb = new Beta(
                    ($this->thomas['rewards'][$value] ?? 0) + $this->a,
                    ($this->thomas['penalties'][$value] ?? 0) + $this->b
                );
                $beta_d = $bb->rand();

                if ($beta_d >= $beta_max) {
                    $beta_max = $beta_d;
                    $bandit = $value;
                }
            }
            $this->thomas['selected_player'][] = $bandit;

            $this->thomas['rewards'][$bandit] += $this->observations[$bandit][$n] ?? 0;
            $this->thomas['penalties'][$bandit] += (1 - ($this->observations[$bandit][$n] ?? 0));

            $this->thomas['total_reward'] += $this->observations[$bandit][$n] ?? 0;
        }
    }

    public function getRevards()
    {
        return $this->thomas['rewards'];
    }

    /**
     * @param array $players
     * @param int $count
     * @return array
     */
    public function generateStatic(array $players, array $count): array
    {
        if (is_array($count))
            foreach ($players as $value) {
                $max[] = count($count[$value]);
                $out[$value] = $count[$value];
            }


        return ['max' => max($max), 'obs' => $out];
    }

    /**
     * @param integer $count
     * @param integer $min
     * @param integer $max
     * @return array
     */
    public function innerRand(int $count, int $min = 0, int $max = 1)
    {
        return array_map(
            function () use ($min, $max) {
                return rand($min, $max);
            },
            array_pad([], $count, 0)
        );
    }
}

$obs = [
    'A1'=>[1000,300],
    'A2'=>[300,100],
    'A3'=>[10000, 4]
];

function innerRand(int $count, int $min = 0, int $max = 1)
{
    return array_map(
        function () use ($min, $max) {
            return rand($min, $max);
        },
        array_pad([], $count, 0)
    );
}
for ($i = 0; $i < 10000; $i++) {
    $out[$i] = innerRand(500);
}
// $obs = [
//     'A1' => [1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 1, 0, 1, 0, 1,],
//     'A2' => [1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
//     'A3' => [1],
//     'A4' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
// ];

// $thomas = new ThompsonSempl($out);

// $thomas->predict();

//$thomas->getRevards();

/** 
 * Array
 *(
 *    [A1] => 7
 *    [A2] => 1
 *    [A3] => 2
 *    [A4] => 13
 *)
 */
