<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\laboratorium\processes;

use Yii;

class FormEditPemeriksaanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $path = 'components/pasien-lab/form-edit-pemeriksaan';
        return $path;
    }
}