<?php

/*
namespace App\Traits;

use Illuminate\Support\Number;
use Carbon\Carbon;

trait FormatNumber{

	public function formatWithPrecision($number, $comma = true){
		if($comma){
			return Number::format($number, app('company')['number_precision'] ?? 2);
		}else{
			return str_replace(',', '', Number::format($number, app('company')['number_precision']));
		}
	}

	public function formatQuantity($number){
		return str_replace(',', '', Number::format($number, app('company')['quantity_precision']));
	}

	public function spell($number){
		return Number::spell($number);
	}

}
*/

namespace App\Traits;

use Illuminate\Support\Number;
use Carbon\Carbon;

trait FormatNumber
{
    public function formatWithPrecision($number, $comma = true)
    {
        $precision = app('company')['number_precision'] ?? 2; // Default value of 2
        if ($comma) {
            return Number::format($number, $precision);
        } else {
            return str_replace(',', '', Number::format($number, $precision));
        }
    }

    public function formatQuantity($number)
    {
        $quantityPrecision = app('company')['quantity_precision'] ?? 0; // Default value of 0
        return str_replace(',', '', Number::format($number, $quantityPrecision));
    }

    public function spell($number)
    {
        return Number::spell($number);
    }
}
