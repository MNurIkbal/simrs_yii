<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;

class LabelSimpanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
      return Yii::t('fe', 'Simpan');
    }
}