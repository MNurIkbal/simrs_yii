<?php
/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */
namespace app\extensions\pendaftaran\SetModels;

use app\extensions\pendaftaran\models\TipePasienStyp;

class ModelTipePasienStyp extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $model = new TipePasienStyp;
        return $model;
    }
}