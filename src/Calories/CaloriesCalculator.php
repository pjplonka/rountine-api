<?php

namespace App\Calories;
class CaloriesCalculator
{
    public function calculate(int $proteins, $carbs, $fat): int
    {
        return ($proteins * 4) + ($carbs * 4) + ($fat * 9);
    }
}
