<?php

/**
 * @author : Dede (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\penatajasa\processes;

use Yii;

class FormPenatajasaProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        return 'partial/tindakan/__form';
    }
}