<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-08-07 10:27:19
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:49:24
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\BatalPeriksaPenunjang;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\RencanaOperasi;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use Doco\Services\InternalService;

class InfPasienOperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienOperasiView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["export-excel-bgprocess"] = ["POST", "GET"];
        $verbs["download-file"] = ["GET"];
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
        $data = $this->getData();
        return new ActiveDataProvider([
            'query' => $data,
        ]);
    }
    public function actionView($id)
    {
        $result = [];
        $posisi = '';
        try {
            $query = InfoPasienOperasiView::find()->with([
                'detailOperasi',
                'intraPosisi' => function ($query) {
                    $query->select(['inpostoperasi_id', 'additional_data']);
                },
            ])->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();

            $rencanaOperasi = RencanaOperasi::find()->select([
                'catatan_klinis',
                'pemakaian_implant',
                'sewa_alat_rs',
                'sewa_vendor',
                'jenis_operasi_cyto',
                'jenis_operasi_elektif',
                'jenis_operasi_odc',
            ])->where([
                'pendaftaran_id' => $query['pendaftaran_id'],
            ])->asArray()->one();

            $result = $query;
            $jsonAdd = isset($result['intraPosisi']) ? json_decode($result['intraPosisi']['additional_data'], true) : [];
            $posisi = isset($jsonAdd['pos']) ? $jsonAdd['pos'] : '1';
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
            $result = [];
            $rencanaOperas = [];
        } catch (\Exception $e) {
            return $e->getMessage();
            $result = [];
            $rencanaOperas = [];
        }
        return [
            'data' => $result,
            'detail' => isset($result['detailOperasi']) ? $result['detailOperasi'] : [],
            'data_rencanaOperasi' => $rencanaOperasi,
            'posisi' => $posisi,
            'last-operasi' => Yii::$app->params['last-operasi'],
        ];
    }
    public function actionBatalPasien()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new BatalPeriksaPenunjang;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->validate()) {
                    if ($model->save()) {
                        $query = "UPDATE pasienmasukpenunjang_t SET status_periksa = '" . DocoConstants::BTL_PERIKSA_LAB . "' WHERE pasienmasukpenunjang_id = '" . $model->pasienmasukpenunjang_id . "'";
                        $update = $connection->createCommand($query)->execute();
                        if ($update) {
                            $find = BatalPeriksaPenunjang::find()->where(['pasienmasukpenunjang_id' => $model->pasienmasukpenunjang_id])->one();
                            $transaction->commit();
                            return ['message' => 'Data Berhasil di simpan', 'no_batalperiksa' => $find->no_batalperiksa];
                        } else {
                            $transaction->rollBack();
                            throw new \Exception("Terjadi kesalahan");
                        }
                    }
                } else {
                    $transaction->rollBack();
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e,
            ];
        }
    }
    public function getData()
    {
        $model = new InfoPasienOperasiView;
        $query = $model::find(true);
        $start = $startOperasi = date('Y-m-d 00:00:00');
        $end = $endOperasi = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
            }
            if (isset($_GET['advanced-filter']['tgl_operasi'])) {
                if (!empty($_GET['advanced-filter']['tgl_operasi'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_operasi']);
                    if (count($explode) == 2) {
                        $startOperasi = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $endOperasi = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_operasi']); // Unset Advanced Filter  date range
                    $query->andWhere(['between', 'infopasienoperasi_v.tgl_operasi', $startOperasi, $endOperasi]);
                }
            }
        }
        
        if(!empty(Yii::$app->jwt->ruangan_id)){
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $query->andWhere(['infopasienoperasi_v.ruangan_id' => $ruangan_id]);
        }

        $query->andWhere(['between', 'infopasienoperasi_v.tgl_rujukan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }
    public function actionMulaiOperasi()
    {
        $model = new InpostOperasi;
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->attributes = $post;
        $model->additional_data = json_encode(['pos' => 1]);
        try {
            $find = InpostOperasi::find()->where(['pasienmasukpenunjang_id' => $model->pasienmasukpenunjang_id])->one();
            if ($find) {
                $result = ['inpostoperasi_id' => $find->inpostoperasi_id, 'last-operasi' => Yii::$app->params['last-operasi']];
                $result = array_merge($result, $this->actionView($model->pasienmasukpenunjang_id));
                return $result;
            }

            if ($model->validate() && $model->save()) {
                $result = ['inpostoperasi_id' => $model->inpostoperasi_id, 'last-operasi' => Yii::$app->params['last-operasi']];
                $result = array_merge($result, $this->actionView($model->pasienmasukpenunjang_id));
                return $result;
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422,
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e,
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e,
            ];
        }
    }

    /**
     * @function : Set Bedah dari pendafataran tanpa jadwal
     */
    public function actionSaveBedah()
    {
        try {
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $transaction = $connection->beginTransaction();
            if ($request->post()) {
                $data_kunjungan = $request->post();
                $model = new PasienMasukPenunjangT;
                $model->pendaftaran_id = $data_kunjungan['pendaftaran_id'];
                $model->pasien_id = $data_kunjungan['pasien_id'];
                $model->pegawai_id = $data_kunjungan['pegawai_id'];
                $model->kelaspelayanan_id = $data_kunjungan['kelaspelayanan_id'];
                $model->jeniskasuspenyakit_id = $data_kunjungan['jeniskasuspenyakit_id'];
                $model->instalasiasal_id = $data_kunjungan['instalasiasal_id'];
                $model->ruangan_id = $data_kunjungan['ruangan_id'];
                $model->ruanganasal_id = $data_kunjungan['ruanganasal_id'];
                $model->kunjungan = $data_kunjungan['kunjungan'];
                $model->status_periksa = DocoConstants::ST_P_PEN_BLM_OPRS;
                $model->is_bayar = false;
                $model->tglmasukpenunjang = $data_kunjungan['tglmasukpenunjang'];
                $model->additional_data = isset($data_kunjungan['additional_data']) ? $data_kunjungan['additional_data'] : '';
                if ($model->save(true)) {
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'status' => 200,
                        'statusCode' => 200,
                    ];
                } else {
                    throw new \yii\base\ErrorException(json_encode($model->getErrors()), 500);
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $types = $request->get('types', []);
        $status_periksa = $result = $resultData = [];
        if (!is_array($types)) {
            $types = [$types];
        }

        $id = $request->get('id', null);
        if ($id) {
            $status_periksa = InfoPasienOperasiView::find()->select(['status_periksa'])->where(['pasienmasukpenunjang_id' => $id])->one();
            $status_periksa = ['status_periksa' => $status_periksa['status_periksa']];
        }
        if (!empty($types)) {
            $term = $request->get('term', null);
            $page = $request->get('page', 1);
            $additionalPayload = $request->get('additionalPayload', []);
            $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
            foreach ($types as $eachType) {
                $selectAll = false;
                $isInfinityScroll = false;
                $result = null;
                switch ($eachType) {
                    case 'instalasi':
                        $isInfinityScroll = true;
                        $result = Instalasi::find()
                            ->select(['instalasi_id as id', 'instalasi_nama as text'])->where(['is_deleted' => false, 'is_active' => true]);

                        if (!empty($term)) {
                            $result->andWhere(['like', 'LOWER(instalasi_nama)', $term]);
                        }
                        $result->orderBy(['instalasi_nama' => SORT_ASC]);
                        break;

                    case 'ruangan':
                        $isInfinityScroll = true;
                        $result = [];
                        if (isset($additionalPayload['instalasi_id'])) {
                            $result = Ruangan::find()
                                ->select(['ruangan_id as id', 'ruangan_nama as text'])->where(['is_deleted' => false, 'is_active' => true]);
                            $result->andWhere(['instalasi_id' => $additionalPayload['instalasi_id']]);
                            if (!empty($term)) {
                                $result->andWhere(['like', 'LOWER(ruangan_nama)', $term]);
                            }
                            $result->orderBy(['ruangan_nama' => SORT_ASC]);
                        }
                        break;

                    case 'status_operasi':
                        $selectAll = true;
                        $status_operasi = DocoHelpers::getLookUpByInstalasi('status_periksa_penunjang', 12);
                        if (!empty($status_operasi)) {
                            $result = [];
                            foreach ($status_operasi as $value) {
                                $result[] = [
                                    'id' => $value['lookup_id'],
                                    'text' => $value['lookup_name'],
                                ];
                            }
                        }

                    default:
                        # code...
                        break;
                }
                if (!empty($result)) {
                    if ($isInfinityScroll) {
                        $result->limit(($limit + 1))->offset($limit * ($page - 1));
                    }
                    if (!$selectAll) {
                        $resultData[$eachType] = $result->asArray()->all();
                    } else {
                        $resultData[$eachType] = $result;
                    }
                } else {
                    $resultData[$eachType] = [];
                }
            }
        }
        return array_merge($status_periksa, $resultData);
    }

    public function actionGetDiagnosa(){
        $request = Yii::$app->request;
        $kirimUnitLainId = $request->get('id');
        $kirimUnitLainId = DocoHelpers::decrypt($kirimUnitLainId);
        try {
            if(!empty($kirimUnitLainId)){
                $query = PasienKirimUnitlain::find()->where(['pasienkirimkeunitlain_id' => $kirimUnitLainId])->one();
                return $query;
            }else{
                return [];
            }
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionExportExcelBgprocess() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $headerExcel = [];
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        $start = date('d-M-Y');
        $end = date('d-M-Y');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                if (count($explode) == 2) {
                    $start = date('d-M-Y', strtotime($explode[0]));
                    $end = date('d-M-Y', strtotime($explode[1]));
                }
            }
        }
        // Set filter per ruangan
        if(!empty(Yii::$app->jwt->ruangan_id)){
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $get['advanced-filter']['ruangan_id'] = $ruangan_id;
        }
        
        $headerExcel = [
            "Tanggal Rujukan" => $start . ' - ' . $end
        ];
        
        $model = new InfoPasienOperasiView;
        $header = $this->setHeaderExcel();
        $data = $this->getData();
        $countData = count($data->asArray()->all());
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => false,
            "customHeader" => [],
        ];
        
        $uri_bedah = Yii::$app->docoRest->getBaseUri('bedahsentral');
        $params = [
            'sendToUrl' => 'inf-pasien-operasi/drop-file',
            'getDataUrl' => 'inf-pasien-operasi/get-data-excel',
            'base_uri' => $uri_bedah,
        ];

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
                    'title' => 'Informasi Pasien Operasi',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'header' => $header,
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

    public function actionGetDataExcel()
    {
        try {
            $data = $this->getData()->AsArray()->All();
            $result = [];
            foreach($data as $key => $value){                
                $value['tgl_rujukan'] = !empty($value['tgl_rujukan']) && !is_null($value['tgl_rujukan']) ? date('d-M-Y H:i:s', strtotime($value['tgl_rujukan'])) : '-';
                $value['tgl_operasi'] = !empty($value['tgl_operasi']) && !is_null($value['tgl_operasi']) ? date('d-M-Y H:i:s', strtotime($value['tgl_operasi'])) : '-';
                $result[$key] = $value;
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
        $fileName = $dir.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    private function setHeaderExcel(){
        $column = [
            [
                'title' => 'Tanggal Rujukan',
                'data' => 'tgl_rujukan',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Nomor Operasi',
                'data' => 'no_masukpenunjang',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Tanggal Operasi',
                'data' => 'tgl_operasi',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'No Pendaftaran',
                'data' => 'no_pendaftaran',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'No Rekam Medik',
                'data' => 'no_rekam_medik',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Nama Pasien',
                'data' => 'nama_pasien',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Instalasi',
                'data' => 'asalrujukan_nama',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Ruangan Perujuk',
                'data' => 'ruangan_nama',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Cara Bayar',
                'data' => 'carabayar_nama',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Penjamin',
                'data' => 'penjamin_nama',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Status',
                'data' => 'status',
                'searchable' => false,
                'visible' => true,
            ],
        ];
        
        return $column;
    }
}
