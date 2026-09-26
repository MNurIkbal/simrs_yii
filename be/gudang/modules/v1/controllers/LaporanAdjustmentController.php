<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\Services\InternalService;
use app\modules\v1\models\LaporanAdjustmentObatAlkesView;

class LaporanAdjustmentController extends DocoActiveController
{
    public $modelClass = '';
    public $namespace = 'app\modules\v1\actions\LaporanAdjustment';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-data' => $this->namespace . '\GetDataAction',
            'filters' => $this->namespace . '\FiltersAction',
            'export-excel' => $this->namespace . '\ExportExcelAction',
            'drop-file' => $this->namespace . '\DropFileAction',
        ];
    }

    public function getDataLaporan($params) {
        $model = new LaporanAdjustmentObatAlkesView;
        $query = $model::find();
        if (isset($params['advanced-filter'])) {
            $advancedFilter = $params['advanced-filter'];
            if(!empty($advancedFilter['tgl_adjusmen'])) {
                $explode = explode(" - ", $advancedFilter['tgl_adjusmen']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_adjusmen']);
                $query->andWhere(['between', 'tgl_adjusmen', $start, $end]);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function dateFilter($query, $request)
    {
        $advancedFilter = $request->get('advanced-filter');
        $dateKey = ['tgl_adjusmen'];
        foreach ($advancedFilter as $key => $value) {
            if (in_array($key, $dateKey)) {
                $explode = explode(" - ", $advancedFilter[$key]);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }

    public function actionSyncExportExcel() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getDataLaporan($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanAdjustmentObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanAdjustmentObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanAdjustmentObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Adjustment Obat Alkes.xlsx';

        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }
}
