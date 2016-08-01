<?php

namespace App\Services\Macros;

/**
 * Class Hopper
 * @package App\Services\Macros
 */
trait HopperDropdowns
{
    /**
     * @param  $name
     * @param  null    $range
     * @param  null    $selected
     * @param  array   $options
     * @return mixed
     */
    public function versionRange($name, $range = null, $selected = null, $options = array())
    {
        if(!is_array($range)){
            $range = $this->add_leading_zeros(range(0, 99));
        }

        return $this->select($name, $range, $selected, $options);
    }
    
    
    public function add_leading_zeros($array) {
        $newarray = [];
        foreach ($array as $key => $value)
            $newarray[str_pad($value, strlen(max($array)), '0', STR_PAD_LEFT)] = str_pad($value, strlen(max($array)), '0', STR_PAD_LEFT);

        return $newarray;
    }
}