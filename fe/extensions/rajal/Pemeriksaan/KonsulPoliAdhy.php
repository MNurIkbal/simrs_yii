<?php
/**
 * 
 * @author : iqbal.rukmana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\rajal\Pemeriksaan;

use Yii;

use app\components\DocoHelpers;
use app\extensions\rajal\models\ModelKonsulPoliAdhy;

class KonsulPoliAdhy extends \app\components\DocoBaseProcessExtension
{

    protected function processFlow($controller)
    {
        return (new ModelKonsulPoliAdhy);
    }
}