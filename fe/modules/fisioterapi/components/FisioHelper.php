<?php

/**
 * @author Alphabet Team <Sirs>
 * FisioHelper (Fisioterapi Helper)
 */

namespace Doco\fisioterapi\components;

use app\components\DocoConstants;
use yii\helpers\ArrayHelper;

class FisioHelper
{
    /**
     * @method isDisabledSetScheduleProgram
     * @param Array $params
     * @return Boolean
     */
    public static function isDisabledSetScheduleProgram($params)
    {
        $disabled = false;
        $status = ArrayHelper::getValue($params, 'status');
        $is_paket = ArrayHelper::getValue($params, 'is_paket');
        $caraBayar = ArrayHelper::getValue($params, 'caraBayar');
        $bayar = ArrayHelper::getValue($params, 'bayar');
        
        if ($status == 'BATAL') {
            $disabled = true;
        } else if ($status == 'CLOSE') {
            $disabled = true;
        } else if ($status == 'EXPIRED') {
            $disabled = true;
        } else if ($status == 'DROP OUT') {
            $disabled = true;
        } else if ($is_paket == true) {
            if ($caraBayar == DocoConstants::CARA_BAYAR_PRIVATE) {
                if ($bayar == 0) {
                    $disabled = true;
                }
            }
        } else {
            $disabled = false;
        }

        return $disabled;
    }
}

?>