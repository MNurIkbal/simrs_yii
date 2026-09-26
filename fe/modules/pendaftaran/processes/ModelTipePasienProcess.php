<?php
/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */
namespace app\modules\pendaftaran\processes;

use app\modules\pendaftaran\models\TipePasienForm;

class ModelTipePasienProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $model = new TipePasienForm;
        return $model;
    }
}