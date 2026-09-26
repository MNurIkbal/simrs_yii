<?php
/**
 * 
 * @author : Ikhwanu Arriyadh
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran\SetModels;

use app\extensions\pendaftaran\models\Mhsb\PasienForm;
use Yii;
use yii\helpers\ArrayHelper;

class PasienFormModelMhsb extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $model = new PasienForm();
        return $model;
    }
}