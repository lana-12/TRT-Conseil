<?php

namespace App\Service;

/**
 * return false if array ==""
 * return true if array =="...." 
 */
class ArrayEmptyService
{
    public function arrayEmpty($array): bool
    {
        if (!empty($array)){
            foreach ($array as $valeur){
                if ($valeur !== null){
                    return false;
                    }
                }
            }
        return true;
    }
}