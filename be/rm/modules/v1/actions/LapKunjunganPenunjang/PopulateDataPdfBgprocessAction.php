<?php

namespace app\modules\v1\actions\LapKunjunganPenunjang;

use Yii;
use Doco\components\DocoHelpers;
use app\modules\v1\actions\LapKunjunganPenunjang\GetDataAction;
use yii\helpers\ArrayHelper;

class PopulateDataPdfBgprocessAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $advancedFilter = $request->get('advanced-filter');
        $footerData = GetDataAction::generateFooterTotalRow();
        $query = GetDataAction::getQueryDataFromReq($advancedFilter);
        $datas = $query->asArray()->all();
        $tglMasukPenunjang = ArrayHelper::getValue($advancedFilter, 'tglmasukpenunjang');
        $startTglMasukPenunjang = date('j-M-Y');
        $endTglMasukPenunjang = date('j-M-Y');
        if ($tglMasukPenunjang) {
            $tglMasukPenunjangRange = DocoHelpers::parsingRangeDate($tglMasukPenunjang);
            $startTglMasukPenunjang = $tglMasukPenunjangRange['startDate'];
            if ($startTglMasukPenunjang) $startTglMasukPenunjang = date('j-M-Y', strtotime($startTglMasukPenunjang));
            $endTglMasukPenunjang = $tglMasukPenunjangRange['endDate'];
            if ($endTglMasukPenunjang) $endTglMasukPenunjang = date('j-M-Y', strtotime($endTglMasukPenunjang));
        }
        $periode = "$startTglMasukPenunjang Sampai Dengan $endTglMasukPenunjang";
        $attributes = [
            '#periode#' => $periode,
            '#datatable#' => $this->controller->renderPartial('cetak_pdf_bgprocess', [
                'datas' => $datas,
                'footerData' => $footerData
            ])
        ];
        $result = [
            'attributes' => $attributes,
            'kode_doc' => 'rm-lap-kunjungan-penunjang'
        ];
        return $result;
    }
}
