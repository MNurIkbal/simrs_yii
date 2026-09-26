<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-12 11:15:19
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-06-28 14:46:03
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\KetersediaanObatView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\UploadForm;
use yii\db\Expression;
use Doco\Services\InternalService;
use yii\web\UploadedFile;

class InfStokObatAlkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoStokObatAlkesView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        return Yii::$app->docoPlugin->execute('info_stok');
        // $model = new InfoStokObatAlkesView; *replaced
        $model = new KetersediaanObatView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $date = date('Y-m-d');
        $filterNama = '';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                $date = $_GET['advanced-filter']['periodestok_nama'];
                unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                $filterNama = $_GET['advanced-filter']['obatalkes_nama'];
            }
        }

        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];
        $page = (int) Yii::$app->request->get('page',1);
        $perPage = (int) Yii::$app->request->get('per-page',10);
        $limit = $perPage;
        $offset = ($page-1)*$limit;
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
//        $queryCount = '
//            SELECT count(*)
//            FROM stokobatalkes_r
//             JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
//             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
//             JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
//             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
//             LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
//             LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
//             LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
//             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
//             JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
//            WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false AND stokobatalkes_r.is_periode = true AND ruangan_m.ruangan_nama ILIKE :ruangan_nama';
//
//        if($filterNama != ''){
//            $queryCount .= " AND obatalkes_m.obatalkes_nama ILIKE '%$filterNama%' ";
//        }
//        $queryCount = Yii::$app->db->createCommand($queryCount);
//        Yii::error($queryCount->sql);
        Yii::error($query->CreateCommand()->sql);
//        $queryCount->bindParam(':ruangan_nama',$_GET['advanced-filter']['ruangan_nama'],\PDO::PARAM_STR);
//        $count = $queryCount->queryScalar();
        $count = $query->count();
        $pageCount = ceil($count / $limit);
        return [
            'data' => $query->limit($limit)->offset($offset)->asArray()->all(),
            "_meta" => [
                "totalCount" =>  $count,
                "pageCount" =>  $pageCount,
                "currentPage" =>  $page,
                "perPage" => $limit
            ]
        ];
    }

    public function actionDataObat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataObat();
        $result->select(['obatalkes_id','obatalkes_namalain','ruangan_id','instalasi_id']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['ILIKE','obatalkes_namalain',$term]);
        }
        if(!empty($post['ruangan_id'])){
            $term = $post['ruangan_id'];
            $result->andWhere(['=','ruangan_id',$term]);
        }
        if(!empty($post['instalasi_id'])){
            $term = $post['instalasi_id'];
            $result->andWhere(['=','instalasi_id',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataObat()
    {
        $data = InfoStokObatAlkesView::find();
        return $data;
    }

    public function actionExportExcel()
    {
        // $model = new InfoStokObatAlkesView; *replaced
        $model = new KetersediaanObatView;
        $query = $model::find(true);

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                $date = $_GET['advanced-filter']['periodestok_nama'];
                unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruanganId = $_GET['advanced-filter']['ruangan_id'];
                $listRuangan = explode(",", $ruanganId);
                $query->andWhere(['IN', 'ruangan_id', $listRuangan]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if(isset($_GET['advanced-filter']['obatalkes_kode'])) {
                $filterKodeObatalkes = $_GET['advanced-filter']['obatalkes_kode'];
                $query->andWhere(['ILIKE', 'obatalkes_kode', trim($filterKodeObatalkes)]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $data_baru = [];
        $namaInstalasi = $namaRuangan = null;
        foreach ($data as $key => $value) {
            $namaRuangan = $value['ruangan_nama'];
            $namaInstalasi = $value['instalasi_nama'];
            $data_baru[] = [
                'Nama Ruangan' => $value['ruangan_nama'],
                'Kode Obat Alkes' => $value['obatalkes_kode'],
                'Kode Obat' => $value['obatalkes_nama'],
                'Ven' => $value['ven_name'],
                'Stok minimal' => $value['min_stok'],
                'Stok maksimal' => $value['max_stok'],
                'Stok dipesan' => $value['qty_dipesan'],
                'Stok tersedia' => $value['qty_tersedia'],
                'Stok total' => $value['qty_stok'],
            ];
        }

        $header = [
            'Tanggal Unduh' => date('d-M-Y H:i:s'),
        ];

        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Stok' => 'Diunduh Oleh :' . $ruangan->ruangan_nama,
            ]
        ];

        if(count($data_baru)<=0){
            $footer = [];
        }
        $filePath = DocoHelpers::exportExcel("Informasi Stok dan Ketersediaan Obat Alkes", $data_baru, $header, [],$footer,[],true);

        $filePath->save('php://output');
        die;
    }

    public function getDataExcel()
    {
        $model = new KetersediaanObatView;
        $query = $model::find(true);

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                $date = $_GET['advanced-filter']['periodestok_nama'];
                unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruanganId = $_GET['advanced-filter']['ruangan_id'];
                $listRuangan = explode(",", $ruanganId);
                $query->andWhere(['IN', 'ruangan_id', $listRuangan]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if(isset($_GET['advanced-filter']['obatalkes_kode'])) {
                $filterKodeObatalkes = $_GET['advanced-filter']['obatalkes_kode'];
                $query->andWhere(['ILIKE', 'obatalkes_kode', trim($filterKodeObatalkes)]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'informasiStokObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportInformasiStokObatAlkes' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'footer' => [],
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadInformasiStokObatAlkesExcel' => [
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/informasi-stok-dan-ketersedian-obat-alkes.xlsx';

        DocoHelpers::downloadFileExcel($fileName, $dir);
    }

    public function actionGenerateTotalStok()
    {
        $model = new KetersediaanObatView;
        $query = $model::find()->select([
            new Expression("SUM(qty_tersedia) AS  total_qty_tersedia"),
            new Expression("SUM(qty_stok) AS  total_qty_stok")
        ]);
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                $filterNama = $_GET['advanced-filter']['obatalkes_nama'];
                $query->andWhere(['ILIKE', 'obatalkes_nama', trim($filterNama)]);
            }
            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruanganId = $_GET['advanced-filter']['ruangan_id'];
                $listRuangan = explode(",", $ruanganId);
                $query->andWhere(['IN', 'ruangan_id', $listRuangan]);
            }
            if(isset($_GET['advanced-filter']['obatalkes_kode'])) {
                $filterKodeObatalkes = $_GET['advanced-filter']['obatalkes_kode'];
                $query->andWhere(['ILIKE', 'obatalkes_kode', trim($filterKodeObatalkes)]);
            }
        }
        $data = $query->asArray()->one();

        return $this->responseJson(200, 'success', $data);
    }


}

