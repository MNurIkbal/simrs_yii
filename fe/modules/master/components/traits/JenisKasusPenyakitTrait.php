<?php
namespace app\modules\master\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\master\models\JenisKasusPenyakit;

trait JenisKasusPenyakitTrait 
{
    public function actionIndexJenis()
    {
        return $this->renderPartial('tabs/_jenis_kasus_penyakit',get_defined_vars());
    }
}