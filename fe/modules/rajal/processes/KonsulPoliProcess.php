<?php
/**
 * 
 * @author : iqbal.rukmana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\rajal\processes;

use Yii;
use app\components\DocoHelpers;
use app\modules\rajal\models\KonsulpoliForm;

class KonsulPoliProcess extends \app\components\DocoBaseProcessExtension
{

    protected function processFlow($controller)
    {
        return (new KonsulpoliForm);
    }
}