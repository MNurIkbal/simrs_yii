<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\helpers;

class HandlingValueHelper {
    public static function nullValue($value) {
        return empty($value) ? '-' : $value;
    }

    public static function dateValue($value) {
        return empty($value) ? '-' : date('d M Y', strtotime($value));
    }

    public static function dateTimeValue($value) {
        return empty($value) ? '-' : date('d M Y H:i:s', strtotime($value));
    }

    // search for non-empty data in $arr_value to return/display
    // if data is empty then return '-'
    public static function compareValue($arr_value) {
        foreach($arr_value as $key => $val) {
            if(!empty($val)) return $val;
        }
        return '-';
    }
}