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
        if (!empty($tab)){
            foreach ($tab as $valeur){
                if ($valeur !== null){
                    return false;
                    }
                }
            }
        return true;
    }
}