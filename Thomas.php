<?php

require_once "vendor/autoload.php";

use gburtini\Distributions\Beta;



class ThompsonSempl
{

    private $thomas = [];

    public function __construct(
        array $players = ['A1', 'A2', 'A3', 'A4'],
        array|int $observations = 200,
        float $a = 1,
        float $b = 1
    ) {
        $this->players = $players;
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

        $this->data = $this->generateStatic($this->players, $observations);
    }


    public function predict()
    {
        $this->thomas = [
            'rewards' => array_fill_keys($this->players, 0),
            'penalties' => array_fill_keys($this->players, 0),
            'total_reward' => 0,
            'selected_records' => []
        ];

        for ($n = 0; $n < $this->observations; $n++) {
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
            $this->thomas['selected_records'][] = $bandit;

            $this->thomas['rewards'][$bandit] += $this->data[$bandit][$n];
            $this->thomas['penalties'][$bandit] += (1 - $this->data[$bandit][$n]);

            $this->thomas['total_reward'] += $this->data[$bandit][$n];
        }

        return $this->thomas;
    }

    /**
     * @param array $players
     * @param int $count
     * @return array
     */
    public function generateStatic(array $players, int $count): array
    {
        if (is_array($count)) {
        }
        foreach ($players as $value) {
            $out[$value] = $this->innerRand($count);
        }

        return $out;
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

$ob = new ThompsonSempl();

print_r($ob->predict());

// function randarr($N, $min = 0, $max = 1)
// {
//     return array_map(
//         function () use ($min, $max) {
//             return rand($min, $max);
//         },
//         array_pad([], $N, 0)
//     );
// }

// for ($i = 0; $i < 5; $i++) {
//     $data[$i] = randarr(201);
// }

// $observ = 200;
// $machines = 5;
// $select_machine = [];

// $rewards = array_fill(0, $machines, 0);
// $penalties = array_fill(0, $machines, 0);
// $total_reward = 0;
// $click = array_fill(0, $machines, 0);
// srand(1);

// for ($n = 0; $n < $observ; $n++) {
//     $bandit = 0;
//     $beta_max = 0;

//     for ($i = 0; $i < $machines; $i++) {

//         $bb = new Beta($rewards[$i] + 1, $penalties[$i] + 1);
//         $beta_d = $bb->rand();

//         if ($beta_d > $beta_max) {
//             $beta_max = $beta_d;
//             $bandit = $i;
//         }
//         $click[$i] += $data[$i][$n];
//     }

//     $select_machine[] = $bandit;

//     $reward = $data[$bandit][$n];

//     if ($reward == 1)
//         $rewards[$bandit] = $rewards[$bandit] + 1;
//     else
//         $penalties[$bandit] = $penalties[$bandit] + 1;

//     $total_reward = $total_reward + $reward;
// }

// // результаты


// print_r($rewards);
