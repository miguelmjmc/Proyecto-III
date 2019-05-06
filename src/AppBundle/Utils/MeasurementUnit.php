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
        );
    }

    public static function resolve($quantity, $measurementUnit)
    {
        if (3 === $measurementUnit) {
            return $quantity / 1000;
        }

        return $quantity;
    }
}