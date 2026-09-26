<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanpersalinanV;
use app\modules\v1\models\LookupkeperawatanM;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LapPersalinanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\laporanpersalinan_v';
    const TGL_LAHIR = 'tgl_lahir_bayi';
    const ADV_FILTER = 'advanced-filter';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["cau"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $getRequest = $request->get();
            
            $model   = self::modelPersalinan(true);
            $query   = self::modelPersalinan();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
            
            if (array_key_exists(self::ADV_FILTER, $getRequest)) {
                if (array_key_exists(self::TGL_LAHIR, $getRequest[self::ADV_FILTER])) {
                    $explode = explode(' - ', $getRequest[self::ADV_FILTER][self::TGL_LAHIR]);
                    
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
    
                    unset($_GET[self::ADV_FILTER][self::TGL_LAHIR]);
                }
            }
    
            $query->andWhere(['between', self::TGL_LAHIR, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private static function modelPersalinan($new = false)
    {
        if($new) {
            return new LaporanpersalinanV;
        } else {
            return LaporanpersalinanV::find();
        }
    }

    public function actionGenerateApi()
    {
        $lookPersalinan = LookupkeperawatanM::find()
            ->select(['lookupkeperawatan_id as id', 'lookup_value'])
            ->where(['lookup_type' => 'jenis_persalinan'])
            ->asArray()
            ->all();

        return [
            'jenisPersalinan' =>  ArrayHelper::map($lookPersalinan, 'id', 'lookup_value'),
        ];
    }

    public function actionExportExcel()
    {
        $title = 'Laporan Persalinan';
        $data = $totalJenis = $header = [];
        $request = Yii::$app->request;
        $getRequest = $request->get();
        $totalJenis = $this->actionGetTotalJenisPersalinan(false);
        
        $model   = self::modelPersalinan(true);
        $query   = self::modelPersalinan();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (array_key_exists(self::ADV_FILTER, $getRequest)) {
            if (array_key_exists(self::TGL_LAHIR, $getRequest[self::ADV_FILTER])) {
                $explode = explode(' - ', $getRequest[self::ADV_FILTER][self::TGL_LAHIR]);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET[self::ADV_FILTER][self::TGL_LAHIR]);
            }

        }

        $query->andWhere(['between', self::TGL_LAHIR, $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
        $tmpData = $dataProvider->getModels();
        $no = 1;
        if(!empty($tmpData)) {
            foreach ($tmpData as $key => $value) {

                $tmp[1] = $no;
                $tmp[2] = $value['nama_bayi'];
                $tmp[3] = date('d/m/Y H:i', strtotime($value[self::TGL_LAHIR]));
                $tmp[4] = $value['no_rekam_medik_bayi'];
                $tmp[5] = $value['berat_badan'];
                $tmp[6] = $value['nama_ibu'];
                $tmp[7] = $value['no_rekam_medik_ibu'];
                $tmp[8] = $value['jenis_persalinan_nama'];
                
                $data[] = $tmp;
                $no++;
            }
        }

        $header['Periode Tanggal Lahir'] = date('d-m-Y', strtotime($start))." - ".  date('d-m-Y', strtotime($end));

        if(!empty($totalJenis)) {
            foreach($totalJenis as $k => $v) {
                $header['Total Jenis Pemeriksaan '.$v['jenis_persalinan_nama']] = $v['jumlah']; 
            }
        }

        $firstRow = [
            [
                'label' => 'No',
            ],
            [
                'label' => 'Nama Bayi',
            ],
            [
                'label' => 'Tanggal Lahir',
            ],
            [
                'label' => 'No Rekam Medik Bayi',
            ],
            [
                'label' => 'Berat (gram)',
            ],
            [
                'label' => 'Nama Ibu',
            ],
            [
                'label' => 'No Rekam Medik Ibu',
            ],
            [
                'label' => 'Jenis Persalinan',
            ],
        ];

        $custHeader = [
            $firstRow,
        ];

        $filePath = DocoHelpers::exportExcel($title, $data, $header ,array(
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ), [], [], true);
        $filePath->save('php://output');
        die();
    }

    public function actionGetTotalJenisPersalinan($isUnset = true)
    {
        $request = Yii::$app->request;
        $conditionQuery = "";
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        if(isset($_GET[self::ADV_FILTER])) {
            if(isset($_GET[self::ADV_FILTER][self::TGL_LAHIR])) {
                $explode = explode(" - ", $_GET[self::ADV_FILTER][self::TGL_LAHIR]);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                if($isUnset) {
                    unset($_GET[self::ADV_FILTER][self::TGL_LAHIR]);
                }
            }

            if(isset($_GET[self::ADV_FILTER]['no_rekam_medik_bayi'])){
                $conditionQuery .= " AND no_rekam_medik_bayi = '".$_GET[self::ADV_FILTER]['no_rekam_medik_bayi']."' ";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['no_rekam_medik_bayi']);
                }
            }

            if(isset($_GET[self::ADV_FILTER]['no_rekam_medik_ibu'])){
                $conditionQuery .= " AND no_rekam_medik_ibu = '".$_GET[self::ADV_FILTER]['no_rekam_medik_ibu']."' ";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['no_rekam_medik_ibu']);
                }
            }

            if(isset($_GET[self::ADV_FILTER]['nama_bayi'])){
                $conditionQuery .= " AND nama_bayi ILIKE '%".$_GET[self::ADV_FILTER]['nama_bayi']."%'";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['nama_bayi']);
                }
            }

            if(isset($_GET[self::ADV_FILTER]['nama_ibu'])){
                $conditionQuery .= " AND nama_ibu ILIKE '%".$_GET[self::ADV_FILTER]['nama_ibu']."%'";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['nama_ibu']);
                }
            }
            
            if(isset($_GET[self::ADV_FILTER]['dokter_dpjp_id'])){
                $conditionQuery .= " AND dokter_dpjp_id = '".$_GET[self::ADV_FILTER]['dokter_dpjp_id']."' ";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['dokter_dpjp_id']);
                }
            }

            if(isset($_GET[self::ADV_FILTER]['jenis_persalinan_id'])){
                $conditionQuery .= " AND jenis_persalinan_id = ".$_GET[self::ADV_FILTER]['jenis_persalinan_id']."";
                if($isUnset){
                    unset($_GET[self::ADV_FILTER]['jenis_persalinan_id']);
                }
            }
        }

        $sqlJenisPersalinan = "
        SELECT sum(tmp) as jumlah,
            CASE WHEN 
                jenis_persalinan_nama IS NULL THEN 'Tidak Diketahui'
            ELSE 
                jenis_persalinan_nama 
            END AS jenis_persalinan_nama
        FROM (
                SELECT 1 as tmp, jenis_persalinan_nama 
                FROM laporanpersalinan_v 
                WHERE 
                    tgl_lahir_bayi::date between :dateStart and :dateEnd {$conditionQuery}) a
            GROUP BY jenis_persalinan_nama";
        $tmpJenisPersalinan = Yii::$app->db->createCommand($sqlJenisPersalinan)
        ->bindValue(':dateStart', $start)
        ->bindValue(':dateEnd', $end)
        ->queryAll();
        
        return $tmpJenisPersalinan;
    }

    private function getObjectData($getRequest)
    {
        $model   = self::modelPersalinan(true);
        $query   = self::modelPersalinan();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (array_key_exists(self::ADV_FILTER, $getRequest)) {
            if (array_key_exists(self::TGL_LAHIR, $getRequest[self::ADV_FILTER])) {
                $explode = explode(' - ', $getRequest[self::ADV_FILTER][self::TGL_LAHIR]);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET[self::ADV_FILTER][self::TGL_LAHIR]);
            }

        }

        $query->andWhere(['between', self::TGL_LAHIR, $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);

        // $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        // $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
        // $tmpData = $dataProvider->getModels();

        // return $tmpData;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);

        if (isset($getData['per-page'])) unset($getData['per-page']);


        $totalJenis = [];
        $totalJenis = $this->actionGetTotalJenisPersalinan(false);

        $fetchLimit = 50;
        $timeLimit = 3;
        $countData = $this->getObjectData($getData)->count();
        $randString = DocoHelpers::generateRandomString();
        $totalPerPage = ceil($countData/$fetchLimit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPersalinanExcel' => [
                    'filter' => $getData,
                    'unique_str' => $randString,
                    'owner' => $xOwner,
                    'token' => $auth
                ]
            ]
        ],true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportExcelLaporanPersalinan' => [
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'owner' => $xOwner,
                    'token' => $auth,
                    'totalJenis' => $totalJenis,
                ]
            ]
        ],true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadExcelLaporanPersalinan' => [
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'owner' => $xOwner,
                    'token' => $auth
                ]
            ]
        ],true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData
        ];
    }


    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $nameFile = $path .'/'. $model->file;
            if($files->saveAs($nameFile)) {
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Persalinan.xlsx';
        if (file_exists($fileName)) {
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