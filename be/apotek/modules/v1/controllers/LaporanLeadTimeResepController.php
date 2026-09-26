<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use GuzzleHttp\Exception\RequestException;
use Doco\actions\GetDataAction;
use Doco\Services\InternalService;
use Doco\components\DocoHelpers;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\LaporanLeadTimeResepView;

class LaporanLeadTimeResepController extends DocoActiveController {
    public $modelClass = '';
    
    public $messageBroker = [
        'generate-data-serconn' => [
            'services' => [
                'Sirs' => [
                    'LaporanLeadTimeResepDatatable' => [
                        'query_params' => ['advance_filter', 'unique_str'],
                    ]
                ],
            ]
        ],
    ];

    public function actions() {
        $path = 'app\modules\v1\actions\LaporanLeadTimeResep';
        return [
            'get-data'      => $path . '\GetDataAction',
            'export-excel'  => $path . '\ExportExcelAction'
        ];
    }

    public function dateFilter($query, $request) {
        $advanced_filter = $request->get('advance_filter');
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');
        if(isset($advanced_filter) && isset($advanced_filter['tgl_resep'])) {
            $explode = explode(" - ", $advanced_filter['tgl_resep']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }

        $query->andWhere(['>=', 'tgl_resep', $start]);
        $query->andWhere(['<=', 'tgl_resep', $end]);
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        $countData = $this->getDataLaporanExcel()->count();
        $fetchLimit = 50;
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanLeadTimeResep' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelLaporanLeadTimeResep' => [
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
                'UploadExcelLaporanLeadTimeResep' => [
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

    public function actionDownloadFile() {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Lead Time Resep.xlsx';
        DocoHelpers::downloadFileExcel($fileName, $dir);
    }

    public function getDataLaporanExcel() {
        $model = new LaporanLeadTimeResepView;
        $query = $model::find(true);
        $request = Yii::$app->request;
        $advance_filter = $request->get('advance_filter');
        $this->dateFilter($query, $request);
        
        if (ArrayHelper::getValue($advance_filter, 'ruangan_id', '') != '') {
            $term = ArrayHelper::getValue($advance_filter, 'ruangan_id', '');
            $query->andWhere(['ruangan_id' => $term]);
        }
        
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile() {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }
    
    public function actionGenerateDataSerconn()
    {
        $request = Yii::$app->request->get();
        $data['status'] = 'update';
        $randString = isset($request['unique_str']) ? $request['unique_str'] : null;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'laporan-lead-time-resep-datatable:'.$randString,
            'message' => json_encode($data),
        ]);
        return [
            'randString' => $randString
        ];       
    }
}
