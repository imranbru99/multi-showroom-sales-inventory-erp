<?php
namespace App\Helper;

class ArrayHelper{

    public static function RemoveArrayitems($hayStack, $needle)
    {
        
        foreach($needle as $n){
            unset($hayStack[$n]);
        }

        return $hayStack;

    }


}