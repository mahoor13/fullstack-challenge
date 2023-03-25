<?php

namespace App\Traits;

trait UnitConversionTrait
{
    /**
     *  Converts Fahrenheit temperature to Celsius
     *
     * @param float $fahrenheit
     * @return float
     */
    protected function fahrenheitToCelsius(float $fahrenheit): float
    {
        $celsius = ($fahrenheit - 32) * 5/9;
        return round($celsius, 2);
    }

    /**
     *  Converts Celsius temperature to Fahrenheit
     *
     * @param float $celisius
     * @return float
     */
    protected function celsiusToFahrenheit(float $celsius): float
    {
        $fahrenheit = ($celsius * 9/5) + 32;
        return round($fahrenheit, 2);
    }

    /**
     *  Converts wind degree to direction
     *
     * @param int $degree
     * @return string
     */
    protected function windDirection(int $degree): string
    {
        $directions = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
        $index = round($degree / 45);
        return $directions[$index];
    }
}
