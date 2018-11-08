<?php

namespace App\Helpers;
use PragmaRX\Countries\Package\Countries;

class MyCountry
{
    public static function all(){
        $countries = Countries::all();
        $result = collect([]);
        foreach ($countries as $country) {
            if ($country->cca2 === 'KZ') {
                $country->callingCode = [7];
            }
            $result->push($country);
        }

        return $result;
    }
}