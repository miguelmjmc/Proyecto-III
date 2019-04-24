<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Client;
use AppBundle\Entity\Credit;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductBrand;
use AppBundle\Entity\ProductCategory;
use AppBundle\Entity\User;
use AppBundle\Form\CompanyProfileType;

class HistoryResolver
{
    public static function decodeOperationType($operationType)
    {
        $value = null;

        switch ($operationType) {
            case '1':
                $value = 'Registrar';
                break;

            case '2':
                $value = 'Actualizar';
                break;

            case '3':
                $value = 'Eliminar';
                break;

            default:
                $value = 'Error';
        }

        return $value;
    }

    public static function encodeTargetEntity($className)
    {
        $value = null;

        switch ($className) {
            case Product::class:
                $value = 1;
                break;

            case ProductBrand::class:
                $value = 2;
                break;

            case ProductCategory::class:
                $value = 3;
                break;

            case Client::class:
                $value = 4;
                break;

            case Credit::class:
                $value = 5;
                break;

            case Payment::class:
                $value = 6;
                break;

            case CompanyProfileType::class:
                $value = 7;
                break;

            case User::class:
                $value = 8;
                break;
        }

        return $value;
    }

    public static function decodeTargetEntity($code)
    {
        $value = null;

        switch ($code) {
            case 1:
                $value = 'Producto';
                break;

            case 2:
                $value = 'Marca';
                break;

            case 3:
                $value = 'Categoria';
                break;

            case 4:
                $value = 'Cliente';
                break;

            case 5:
                $value = 'Credito';
                break;

            case 6:
                $value = 'Pago';
                break;

            case 7:
                $value = 'Perfil de la Empresa';
                break;

            case 8:
                $value = 'Usuario';
                break;
        }

        return $value;
    }
}