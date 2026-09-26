<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\KegiatanOperasi;
use app\modules\v1\models\Operasi;
use app\modules\v1\models\GolonganOperasi;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\LaporanDetailOperasiView;

class LaporanDetailOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanDetailOperasiView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $query = $this->getData();
            $query = isset($query['data']) ? $query['data'] : [];
            $result = [];
            if(!empty($query)) {
                $result = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,
                ]);
            }
            return $result;
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

    private function getData($type = 1)
    {
        $query = $allData = [];
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $rangeDate = $golonganOperasi = $tindakanOperasi = $kegiatanOperasi = $dokterOperator = '-';
        $columns = $groupBy = [];
        if (isset($_GET['advanced-filter'])) {  
            $model = new LaporanDetailOperasiView;
            if($type == 1) {
                $groupBy = ['golongan_operasi', 'tindakan_operasi', 'kegiatan_operasi', 'dokter_operator'];
                $columns = [
                    'golongan_operasi',
                    'tindakan_operasi',
                    'kegiatan_operasi',
                    'dokter_operator',
                    'SUM(qty) AS qty'
                ];
            }
            else {
                $groupBy = ['golongan_operasi', 'tindakan_operasi', 'kegiatan_operasi'];
                $columns = [
                    'golongan_operasi',
                    'tindakan_operasi',
                    'kegiatan_operasi',
                    'SUM(qty) AS qty'
                ];
            }
            
            $query = $model::find()->select($columns);
            if (isset($_GET['advanced-filter']['tgl_operasi'])) {
                $date = DocoHelpers::parsingRangeDate($_GET['advanced-filter']['tgl_operasi']);
                $start = isset($date['startDate']) ? $date['startDate'] : $start;
                $end = isset($date['endDate']) ? $date['endDate'] : $end;
                $rangeDate = isset($date['rangeDate']) ? $date['rangeDate'] : '-';
                $query->andWhere(['between', 'tgl_operasi', $start, $end]);
                unset($_GET['advanced-filter']['tgl_operasi']);
            }   
            if (isset($_GET['advanced-filter']['golonganoperasi_id'])) {
                $golonganoperasi_id = $_GET['advanced-filter']['golonganoperasi_id'];
                $query->andWhere(['TRIM(golongan_operasi)' => $golonganoperasi_id]);
                $golonganOperasi = implode(', ', $golonganoperasi_id);
                unset($_GET['advanced-filter']['golonganoperasi_id']);
            }
            if (isset($_GET['advanced-filter']['tindakan_operasi_id'])) {
                $tindakan_operasi_id = $_GET['advanced-filter']['tindakan_operasi_id'];
                $query->andWhere(['TRIM(tindakan_operasi)' => $tindakan_operasi_id]);
                $tindakanOperasi = implode(', ', $tindakan_operasi_id);
                unset($_GET['advanced-filter']['tindakan_operasi_id']);
            }
            if (isset($_GET['advanced-filter']['kegiatanoperasi_id'])) {
                $kegiatanoperasi_id = $_GET['advanced-filter']['kegiatanoperasi_id'];
                $query->andWhere(['TRIM(kegiatan_operasi)' => $kegiatanoperasi_id]);
                $kegiatanOperasi = implode(', ', $kegiatanoperasi_id);
                unset($_GET['advanced-filter']['kegiatanoperasi_id']);
            }
            if (isset($_GET['advanced-filter']['dokter_operator_id'])) {
                $dokter_operator_id = $_GET['advanced-filter']['dokter_operator_id'];
                $query->andWhere(['TRIM(dokter_operator)' => $dokter_operator_id]);
                $dokterOperator = implode(', ', $dokter_operator_id);
                unset($_GET['advanced-filter']['dokter_operator_id']);
            }

            $query->groupBy($groupBy);
            $allData = $query->asArray()->all();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
        }
        
        $header = [
            'Tanggal Operasi' => $rangeDate,
            'Golongan Operasi' => $golonganOperasi,
            'Tindakan Operasi' => $tindakanOperasi,
            'Kegiatan Operasi' => $kegiatanOperasi,
            'Dokter Operator' => $dokterOperator,
        ];
        return [
            'data' => $query,
            'allData' => $allData,
            'countData' => count($allData),
            'header' => $header,
            'columns' => $columns,
            'type' => isset($_GET['type']) ? $_GET['type'] : 1
        ];
    }

    public function actionExportExcel() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $typeOperasi = isset($get['typeOperasi']) ? $get['typeOperasi'] : null;
        $type = isset($get['type']) ? $get['type'] : 1;

        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        /** set header excel */
        
        $data = $this->getData($type);
        $countData = isset($data['countData']) ? $data['countData'] : 0;
        $totalPerPage = ceil($countData/20);
        $uri_bedah = Yii::$app->docoRest->getBaseUri('bedahsentral');
        $params = [
            'sendToUrl' => 'laporan-detail-operasi/drop-file',
            'getDataUrl' => 'laporan-detail-operasi/get-data-laporan?type='.$type,
            'base_uri' => $uri_bedah,
        ];

        $getAttributeExcel = $this->getAttributeExcel();
        $headerExcel = isset($data['header']) ? $data['header'] : [];
        $header = $this->getheader();
        $footer = isset($getAttributeExcel['footer']) ? $getAttributeExcel['footer'] : [];
        $options = isset($getAttributeExcel['options']) ? $getAttributeExcel['options'] : [];

        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'headerExcel' => $headerExcel,
                    'header' => $header,
                    'footer' => $footer,
                    'options' => $options,
                    'title' => 'Laporan '.$typeOperasi,
                    'customData' => true,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getAttributeExcel()
    {
        return [
            'footer' => [],
            'options' => [
                'skipIncrement' => true
            ],
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
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

    public function actionGetDataLaporan($type = 1)
    {
        $data = $this->getData($type);
        $data = isset($data['allData']) ? $data['allData'] : [];
        $result = [];
        foreach ($data as $key => $val) {
            $tindakan_operasi = isset($val['tindakan_operasi']) ? $val['tindakan_operasi'] : '';
            $golongan_operasi = isset($val['golongan_operasi']) ? $val['golongan_operasi'] : '';
            if(!empty($tindakan_operasi)) {
               $result[$golongan_operasi][$tindakan_operasi][] = $val;
            }
        }
        $row = [];
        $newRow = [];
        $grandTotal = 0;
        foreach ($result as $tindakanOperasi => $dataTindakan) {
            $total = 0;
            foreach ($dataTindakan as $key => $value) {
                $urutData = 0;
                $totalTindakan = 0;
                foreach ($value as $keys => $datas) {
                    $qty = isset($datas['qty']) ? $datas['qty'] : 0;
                    $totalTindakan += $qty;
                    $total += $qty;
                    $golongan_operasi = isset($datas['golongan_operasi']) ? $datas['golongan_operasi'] : '';
                    $tindakan_operasi = isset($datas['tindakan_operasi']) ? $datas['tindakan_operasi'] : '';
                    $kegiatan_operasi = isset($datas['kegiatan_operasi']) ? $datas['kegiatan_operasi'] : '';
                    $dokter_operator = isset($datas['dokter_operator']) ? $datas['dokter_operator'] : '';
                    if($type == 2) {
                        $newRow = [
                            'golongan_operasi' => $golongan_operasi,
                            'tindakan_operasi' => $tindakan_operasi,
                            'kegiatan_operasi' => $kegiatan_operasi,
                            'qty' => $qty,
                        ];
                    }
                    else {
                        $newRow = [
                            'golongan_operasi' => $golongan_operasi,
                            'tindakan_operasi' => $tindakan_operasi,
                            'kegiatan_operasi' => $kegiatan_operasi,
                            'dokter_operator' => $dokter_operator,
                            'qty' => $qty,
                        ];
                    }
                    
                    $row[] = $newRow;
                    $urutData++;
                }
                $labelTindakan = 'Sub Total '.$key;
                if($type == 2) {
                    $row[] = [
                        'golongan_operasi' => $labelTindakan,
                        'tindakan_operasi' => '',
                        'kegiatan_operasi' => '',
                        'qty' => $totalTindakan,
                    ];
                }
                else {
                    $row[] = [
                        'golongan_operasi' => $labelTindakan,
                        'tindakan_operasi' => '',
                        'kegiatan_operasi' => '',
                        'dokter_operator' => '',
                        'qty' => $totalTindakan,
                    ];
                }
            }
            $labelGolongan = 'Sub Total '.$tindakanOperasi;
            if($type == 2) {
                $row[] = [
                    'golongan_operasi' => $labelGolongan,
                    'tindakan_operasi' => '',
                    'kegiatan_operasi' => '',
                    'qty' => $total,
                ];
            }
            else {
                $row[] = [
                    'golongan_operasi' => $labelGolongan,
                    'tindakan_operasi' => '',
                    'kegiatan_operasi' => '',
                    'dokter_operator' => '',
                    'qty' => $total,
                ];
            }
            $grandTotal += $total;
        }
        if($type == 2) {
            $row[] = [
                'golongan_operasi' => 'Grand Total',
                'tindakan_operasi' => '',
                'kegiatan_operasi' => '',
                'qty' => $grandTotal,
            ];
        }
        else {
            $row[] = [
                'golongan_operasi' => 'Grand Total',
                'tindakan_operasi' => '',
                'kegiatan_operasi' => '',
                'dokter_operator' => '',
                'qty' => $grandTotal,
            ];
        }
        return $row;
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/' . $no_request . '.xlsx';
        if(DocoHelpers::downloadFileExcel($fileName)) {
            if(is_dir($dir)) {
                rmdir($dir);
            }
        }
    }

    private function getheader()
    {
        $data = $this->getData();
        $type = isset($data['type']) ? $data['type'] : 1;
        $columns = isset($data['columns']) ? $data['columns'] : [];
        if($type == 2) {
            if(isset($columns[3])) {
                unset($columns[3]);
            }
        }
        $column = [];
        foreach($columns as $value){
            $title = ucwords(str_replace('_',  ' ', $value));
            if($value == 'SUM(qty) AS qty') {
                $value = 'qty';
                $title = 'Qty Tindakan';
            }
            $column[] = [
                'title' => $title,
                'data' => $value,
            ];
        }
        return $column;
    }

    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? strtolower($payload['term']) : null;
        $limit = $limit + 1;
        $offset = ($page - 1) * $limit;
        $instalasiBedah = DocoConstants::INST_ID_BEDAH;
        $result = [];
        $where = "";
        if($type == 'golongan_operasi') {
            if(!empty($term)) {
                $where = " AND LOWER(golonganoperasi_nama) LIKE '%$term%' ";
            }
            $result = Yii::$app->db->createCommand("SELECT 
                TRIM(golonganoperasi_nama) AS id, TRIM(golonganoperasi_nama) AS text
                FROM golonganoperasi_m
                WHERE is_deleted = FALSE AND is_active = TRUE
                {$where}
                GROUP BY TRIM(golonganoperasi_nama), TRIM(golonganoperasi_nama) 
                ORDER BY TRIM(golonganoperasi_nama) LIMIT {$limit} OFFSET {$offset}")->queryAll();
        }
        elseif($type == 'tindakan_operasi') {
            if(!empty($term)) {
                $where = " AND LOWER(operasi_nama) LIKE '%$term%' ";
            }
            
            $result = Yii::$app->db->createCommand("SELECT 
                TRIM(operasi_nama) AS id, TRIM(operasi_nama) AS text
                FROM operasi_m
                WHERE is_deleted = FALSE AND is_active = TRUE
                {$where}
                GROUP BY TRIM(operasi_nama), TRIM(operasi_nama) 
                ORDER BY TRIM(operasi_nama) LIMIT {$limit} OFFSET {$offset}")->queryAll();
        }
        elseif($type == 'kegiatan_operasi') {
            if(!empty($term)) {
                $where = " AND LOWER(kegiatanoperasi_nama) LIKE '%$term%' ";
            }
            $result = Yii::$app->db->createCommand("SELECT 
                TRIM(kegiatanoperasi_nama) AS id, TRIM(kegiatanoperasi_nama) AS text
                FROM kegiatanoperasi_m
                WHERE is_deleted = FALSE AND is_active = TRUE
                {$where}
                GROUP BY TRIM(kegiatanoperasi_nama), TRIM(kegiatanoperasi_nama) 
                ORDER BY TRIM(kegiatanoperasi_nama) LIMIT {$limit} OFFSET {$offset}")->queryAll();
        }
        else {
            if(!empty($term)) {
                $where = " AND LOWER(nama_pegawai) LIKE '%$term%' ";
            }
            $result = Yii::$app->db->createCommand("SELECT 
                TRIM(nama_pegawai) AS id, TRIM(nama_pegawai) AS text
                FROM pegawai_v
                WHERE pegawai_is_active = TRUE AND instalasi_id = {$instalasiBedah}
                {$where}
                GROUP BY TRIM(nama_pegawai), TRIM(nama_pegawai) 
                ORDER BY TRIM(nama_pegawai) LIMIT {$limit} OFFSET {$offset}")->queryAll();
        }

        return $result;
    }
}