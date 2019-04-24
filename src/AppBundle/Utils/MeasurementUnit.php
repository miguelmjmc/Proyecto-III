<?php

namespace AppBundle\Utils;

class MeasurementUnit
{
    public static function units()
    {
        return array(
            'Unit' => 1,
            'kilogram' => 2,
            'Gram' => 3,
            'Liter' => 4,
            'Mililiter' => 5,
        );
    }

    public static function resolve($quantity, $measurementUnit)
    {
        if (3 === $measurementUnit || 5 === $measurementUnit) {
            return $quantity / 1000;
        }

        return $quantity;
    }
}