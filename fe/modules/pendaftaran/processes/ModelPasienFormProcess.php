<?php
/**
 * 
 * @author : Ikhwanu Arriyadh T 
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use app\modules\pendaftaran\models\PasienForm;

class ModelPasienFormProcess extends \app\components\DocoBaseProcessExtension
{

    protected function processFlow($controller)
    {
        $model = new PasienForm();
        return $model;
    }
}