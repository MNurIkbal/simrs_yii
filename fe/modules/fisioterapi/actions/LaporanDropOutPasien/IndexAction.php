<?php

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use Yii;
use app\components\DHtml;
use app\components\DocoHelpers;

class IndexAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers;
        $title = $helper->coalesce(DHtml::getTitleMenu(), 'Laporan Drop Out Pasien');
        $dataView = [
            'title' => $title,
        ];
        return Yii::$app->controller->render('index', compact('dataView'));
    }
}
