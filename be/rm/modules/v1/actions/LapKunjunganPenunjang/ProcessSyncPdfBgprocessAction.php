<?php

namespace app\modules\v1\actions\LapKunjunganPenunjang;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use app\modules\v1\actions\LapKunjunganPenunjang\GetDataAction;

class ProcessSyncPdfBgprocessAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $advancedFilters = $request->get('advanced-filter');
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        $footerData = GetDataAction::generateFooterTotalRow();
        $limit = 1;
        $datas = GetDataAction::getQueryDataFromReq($advancedFilters);
        $countData = $datas->count();
        $randString = ArrayHelper::getValue($getData, 'randString');
        $totalPerPage = ceil($countData / $limit);
        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\LapKunjunganPenunjangPdf\LapKunjunganPenunjangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'footerData' => $footerData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\LapKunjunganPenunjangPdf\ExportLapKunjunganPenunjangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'footerData' => $footerData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'Rm\LapKunjunganPenunjangPdf\UploadLapKunjunganPenunjangPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'footerData' => $footerData,
                ]
            ]
        ], true);
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }
}
