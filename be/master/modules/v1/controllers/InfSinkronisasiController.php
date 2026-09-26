<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use app\modules\v1\models\SinkronisasiK;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\Instalasi;

class InfSinkronisasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\SinkronisasiK';
    static protected $_url = 'on/sinkronisasi/syncmaster';

    const INS_PENUNJANG = [4, 5, 12];
    const INS_RJ = 1;
    const INS_RD = 2;
    const INS_RI = 3;
    const PENUNJANG = 'PMED';
    const RAJAL = 'RJAL';
    const RANAP = 'RINP';
    protected $defaultProcessLimit = 5;

    public $messageBroker = [
        'sync-pegawai-satusehat' => [
            'services' => [
                'Satusehat' => [
                    'SyncPegawaiSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        'sync-ruangan-satusehat' => [
            'services' => [
                'Satusehat' => [
                    'SyncRuanganSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        'sync-instalasi-satusehat' => [
            'services' => [
                'Satusehat' => [
                    'SyncInstalasiSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
        'sync-pasien-satusehat' => [
            'services' => [
                'Satusehat' => [
                    'SyncPasienSatusehat' => [
                        'query_params' => ['limit_process'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ]
    ];


    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => [
                'sync-ruangan', 
                'sync-kamar-ruangan', 
                'sync-kamar-tempat-tidur', 
                'sync-pegawai-satusehat',
                'sync-ruangan-satusehat',
                'sync-instalasi-satusehat',
                'sync-pasien-satusehat'
            ],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => [
                'sync-ruangan', 
                'sync-kamar-ruangan', 
                'sync-kamar-tempat-tidur', 
                'sync-pegawai-satusehat',
                'sync-ruangan-satusehat',
                'sync-instalasi-satusehat',
                'sync-pasien-satusehat'
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new SinkronisasiK;
            $query = $model::find()
            ->select(['sinkronisasi_id', 'sinkronisasi_daftar','terakhir_update', 'url'])
            ->where(['is_active' => true, 'is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    /**
     * sync master ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSyncRuangan()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['route'] = 'app/get-data-bagian';
        $params['route_sync'] = 'master/v1/inf-sinkronisasi/save-ruangan';
        $params['route_callback'] = 'master/v1/inf-sinkronisasi/callback-sync-ruangan';

        try {
            $restSerconn->post(self::$_url, [
                'body' => json_encode($params),
                'timeout' => 1, // Response timeout
            ]);
            return [
                'message' => 'Proses Sync Ruangan Berhasil',
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Proses Sync Ruangan Berhasil',
            ];
        }
    }

    /**
     * save master ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSaveRuangan()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $ruangan = [];
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $additional_data = (isset($data['KDBAGIAN'])) ? $data['KDBAGIAN'] : null;
                $instalasi_singkatan = (isset($data['KDDEP'])) ? $data['KDDEP'] : null;

                if($instalasi_singkatan == self::PENUNJANG) { //Case Instalasi Penunjang
                    foreach(self::INS_PENUNJANG as $value) {
                        $instalasi_id = $value;

                        $ruangan = [
                            'ruangan_nama' => (isset($data['NMBAGIAN'])) ? $data['NMBAGIAN'] : '',
                            'ruangan_namalainnya' => (isset($data['NMBAGIAN'])) ? $data['NMBAGIAN'] : '',
                            'additional_data' => $additional_data,
                            'is_active' => ($data['AKTIFBAG'] == 'Y') ? true : false,
                            'instalasi_id' => $instalasi_id,
                        ];
        
                        $syncRuangan = Ruangan::find()
                        ->where(['additional_data' => $additional_data])
                        ->andWhere(['instalasi_id' => $instalasi_id])
                        ->one();
        
                        if(count($syncRuangan) < 1) {
                            $syncRuangan = new Ruangan;
                            $syncRuangan->ruangan_nama = $ruangan['ruangan_nama'];
                            $syncRuangan->ruangan_namalainnya = $ruangan['ruangan_namalainnya'];
                            $syncRuangan->additional_data = $ruangan['additional_data'];
                            $syncRuangan->is_active = $ruangan['is_active'];
                            $syncRuangan->instalasi_id = $ruangan['instalasi_id'];
                            $syncRuangan->is_modul = false;
                        } else {
                            $syncRuangan->ruangan_nama = $ruangan['ruangan_nama'];
                            $syncRuangan->ruangan_namalainnya = $ruangan['ruangan_namalainnya'];
                            $syncRuangan->additional_data = $ruangan['additional_data'];
                            $syncRuangan->is_active = $ruangan['is_active'];
                            $syncRuangan->instalasi_id = $ruangan['instalasi_id'];
                            $syncRuangan->is_modul = false;
                        }
                        
                        if($syncRuangan->save()){
                            
                        } else {
                            $transaction->rollBack();
                            return $syncRuangan;
                        }
                    }
                    $transaction->commit();
                    return true;
                } else {
                    if ($instalasi_singkatan == null) {
                        $transaction->rollBack();
                        return false;
                    }

                    switch($instalasi_singkatan){
                        case self::RAJAL:
                            $instalasi_id = self::INS_RJ;
                            break;
                        case self::RANAP:
                            $instalasi_id = self::INS_RI;
                            break;
                        default:
                            $instalasi_id = null;
                            break;
                    }
    
                    if($instalasi_id == null) {
                        $transaction->rollBack();
                        return false;
                    }
    
                    $ruangan = [
                        'ruangan_nama' => (isset($data['NMBAGIAN'])) ? $data['NMBAGIAN'] : '',
                        'ruangan_namalainnya' => (isset($data['NMBAGIAN'])) ? $data['NMBAGIAN'] : '',
                        'additional_data' => $additional_data,
                        'is_active' => ($data['AKTIFBAG'] == 'Y') ? true : false,
                        'instalasi_id' => $instalasi_id,
                    ];
    
                    $syncRuangan = Ruangan::find()
                    ->where(['additional_data' => $additional_data])
                    ->andWhere(['instalasi_id' => $instalasi_id])
                    ->one();
    
                    if(count($syncRuangan) < 1) {
                        $syncRuangan = new Ruangan;
                        $syncRuangan->ruangan_nama = $ruangan['ruangan_nama'];
                        $syncRuangan->ruangan_namalainnya = $ruangan['ruangan_namalainnya'];
                        $syncRuangan->additional_data = $ruangan['additional_data'];
                        $syncRuangan->is_active = $ruangan['is_active'];
                        $syncRuangan->instalasi_id = $ruangan['instalasi_id'];
                        $syncRuangan->is_modul = false;
                    } else {
                        $syncRuangan->ruangan_nama = $ruangan['ruangan_nama'];
                        $syncRuangan->ruangan_namalainnya = $ruangan['ruangan_namalainnya'];
                        $syncRuangan->additional_data = $ruangan['additional_data'];
                        $syncRuangan->is_active = $ruangan['is_active'];
                        $syncRuangan->instalasi_id = $ruangan['instalasi_id'];
                        $syncRuangan->is_modul = false;
                    }
                    
                    if($syncRuangan->save()){
                        $transaction->commit();
                        return true;
                    } else {
                        $transaction->rollBack();
                        return $syncRuangan;
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * save sync status ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionCallbackSyncRuangan()
    {
        $request = Yii::$app->request;
        $message = $request->post('message', null);
        $status = $request->post('status');
        $id = 1;
        
        $syncModel = self::getDataSinkron()
        ->where(['sinkronisasi_id' => $id])
        ->one();

        $syncModel->terakhir_update = date('Y-m-d H:i:s');
        $syncModel->is_sync = $status;
        $syncModel->additional_data = (!empty($message)) ? json_encode($message) : null;
        $syncModel->save();

        return $syncModel;
    }

    /**
     * sync master kamar ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSyncKamarRuangan()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['route'] = 'app/get-data-ruangan';
        $params['route_sync'] = 'master/v1/inf-sinkronisasi/save-kamar-ruangan';
        $params['route_callback'] = 'master/v1/inf-sinkronisasi/callback-sync-kamar-ruangan';

        try {
            $restSerconn->post(self::$_url, [
                'body' => json_encode($params),
                'timeout' => 1, // Response timeout
            ]);
            return [
                'message' => 'Proses Sync Kamar Ruangan Berhasil',
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Proses Sync Kamar Ruangan Berhasil',
            ];
        }
    }

    /**
     * save master kamar ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSaveKamarRuangan()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $kamar = [];
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $additional_data = (isset($data['KDRUANGRI'])) ? $data['KDRUANGRI'] : null;
                $ruangan_singkatan = (isset($data['KDBAGIAN'])) ? $data['KDBAGIAN'] : null;
                $kelas_additionaldata = (isset($data['KDKELAS'])) ? $data['KDKELAS'] : null;

                $ruangan = Ruangan::find()
                    ->select(['ruangan_id'])
                    ->where(['additional_data' => $ruangan_singkatan])
                    ->one();
                $ruangan_id = (isset($ruangan->ruangan_id)) ? $ruangan->ruangan_id : null;

                if($ruangan_id == null){
                    $transaction->rollBack();
                    return false;
                }

                $kelaspelayanan = KelasPelayanan::find()
                    ->select(['kelaspelayanan_id'])
                    ->where(['additional_data' => $kelas_additionaldata])
                    ->one();
                $kelaspelayanan_id = (isset($kelaspelayanan->kelaspelayanan_id)) ? $kelaspelayanan->kelaspelayanan_id : null;

                $kamar = [
                    'ruangan_id' => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'kamarruangan_nokamar' => (isset($data['NAMAKAMAR'])) ? $data['NAMAKAMAR'] : '-',
                    'additional_data' => $additional_data,
                    'kamarruangan_jenis' => 431, // Campur
                    'jeniskasuspenyakit_id' => 23, // Umum
                    'jumlah_tt' => (isset($data['JMLBED'])) ? $data['JMLBED'] : 0,
                    'is_active' => ($data['AKTIFKAMAR'] == 'Y') ? true : false,
                ];

                $syncKamarRuangan = KamarRuangan::find()
                    ->where(['additional_data' => $additional_data])
                    ->andWhere(['ruangan_id' => $ruangan_id])
                    ->one();

                if(count($syncKamarRuangan) < 1) {
                    $syncKamarRuangan = new KamarRuangan;
                    $syncKamarRuangan->ruangan_id = $kamar['ruangan_id'];
                    $syncKamarRuangan->kelaspelayanan_id = $kamar['kelaspelayanan_id'];
                    $syncKamarRuangan->kamarruangan_nokamar = $kamar['kamarruangan_nokamar'];
                    $syncKamarRuangan->additional_data = $kamar['additional_data'];
                    $syncKamarRuangan->kamarruangan_jenis = $kamar['kamarruangan_jenis'];
                    $syncKamarRuangan->jeniskasuspenyakit_id = $kamar['jeniskasuspenyakit_id'];
                    $syncKamarRuangan->jumlah_tt = $kamar['jumlah_tt'];
                    $syncKamarRuangan->is_active = $kamar['is_active'];
                } else {
                    $syncKamarRuangan->ruangan_id = $kamar['ruangan_id'];
                    $syncKamarRuangan->kelaspelayanan_id = $kamar['kelaspelayanan_id'];
                    $syncKamarRuangan->kamarruangan_nokamar = $kamar['kamarruangan_nokamar'];
                    $syncKamarRuangan->additional_data = $kamar['additional_data'];
                    $syncKamarRuangan->kamarruangan_jenis = $kamar['kamarruangan_jenis'];
                    $syncKamarRuangan->jeniskasuspenyakit_id = $kamar['jeniskasuspenyakit_id'];
                    $syncKamarRuangan->jumlah_tt = $kamar['jumlah_tt'];
                    $syncKamarRuangan->is_active = $kamar['is_active'];
                }

                if($syncKamarRuangan->save()){
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return $syncKamarRuangan;
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * callback sync status kamar ruangan st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionCallbackSyncKamarRuangan()
    {
        $request = Yii::$app->request;
        $message = $request->post('message', null);
        $status = $request->post('status');
        $id = 5;
        
        $syncModel = self::getDataSinkron()
        ->where(['sinkronisasi_id' => $id])
        ->one();

        $syncModel->terakhir_update = date('Y-m-d H:i:s');
        $syncModel->is_sync = $status;
        $syncModel->additional_data = (!empty($message)) ? json_encode($message) : null;
        $syncModel->save();

        return $syncModel;
    }

    /**
     * sync master kamar tempat tidur st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSyncKamarTempatTidur()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['route'] = 'app/get-data-bed';
        $params['route_sync'] = 'master/v1/inf-sinkronisasi/save-kamar-tempat-tidur';
        $params['route_callback'] = 'master/v1/inf-sinkronisasi/callback-sync-kamar-tempat-tidur';

        try {
            $restSerconn->post(self::$_url, [
                'body' => json_encode($params),
                'timeout' => 1, // Response timeout
            ]);
            return [
                'message' => 'Proses Sync Kamar Tempat Tidur Berhasil',
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Proses Sync Kamar Tempat Tidur Berhasil',
            ];
        }
    }

    /**
     * sync master kamar tempat tidur st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionSaveKamarTempatTidur()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $tempattidur = [];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $no_tempattidur = (isset($data['NOBED'])) ? $data['NOBED'] : null;
                $kamar_additionaldata = (isset($data['KDRUANGRI'])) ? $data['KDRUANGRI'] : null;

                $kamarruangan = KamarRuangan::find()
                    ->select(['kamarruangan_id'])
                    ->where(['additional_data' => $kamar_additionaldata])
                    ->one();
                $kamarruangan_id = (isset($kamarruangan->kamarruangan_id)) ? $kamarruangan->kamarruangan_id : null;

                if($kamarruangan_id == null){
                    $transaction->rollBack();
                    return false;
                }

                $tempattidur = [
                    'kamarruangan_id' => $kamarruangan_id,
                    'no_tempattidur' => $no_tempattidur,
                    'status_isi' => ($data['STATUSBED'] == 'K') ? false : true,
                    'kettempattidur_id' => ($data['STATUSBED'] == 'K') ? 8 : 15,
                    'is_deleted' => false,
                    'is_active' => ($data['AKTIFBED'] == 'Y') ? true : false,
                ];

                $syncTempatTidur = KamarTempatTidur::find()
                    ->where(['no_tempattidur' => $no_tempattidur])
                    ->andWhere(['kamarruangan_id' => $kamarruangan_id])
                    ->one();

                if(count($syncTempatTidur) < 1) {
                    $syncTempatTidur = new KamarTempatTidur;
                    $syncTempatTidur->kamarruangan_id = $tempattidur['kamarruangan_id'];
                    $syncTempatTidur->no_tempattidur = $tempattidur['no_tempattidur'];
                    $syncTempatTidur->status_isi = $tempattidur['status_isi'];
                    $syncTempatTidur->kettempattidur_id = $tempattidur['kettempattidur_id'];
                    $syncTempatTidur->is_deleted = $tempattidur['is_deleted'];
                    $syncTempatTidur->is_active = $tempattidur['is_active'];
                } else {
                    $syncTempatTidur->kamarruangan_id = $tempattidur['kamarruangan_id'];
                    $syncTempatTidur->no_tempattidur = $tempattidur['no_tempattidur'];
                    $syncTempatTidur->status_isi = $tempattidur['status_isi'];
                    $syncTempatTidur->kettempattidur_id = $tempattidur['kettempattidur_id'];
                    $syncTempatTidur->is_deleted = $tempattidur['is_deleted'];
                    $syncTempatTidur->is_active = $tempattidur['is_active'];
                }

                if($syncTempatTidur->save()){
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return $syncTempatTidur;
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * callback sync status kamar tempat tidur st yusup
     * @return array
     * @author : Fajar (fajar.supriadi@docotel.com)
     */
    public function actionCallbackSyncKamarTempatTidur()
    {
        $request = Yii::$app->request;
        $message = $request->post('message', null);
        $status = $request->post('status');
        $id = 6;
        
        $syncModel = self::getDataSinkron()
        ->where(['sinkronisasi_id' => $id])
        ->one();

        $syncModel->terakhir_update = date('Y-m-d H:i:s');
        $syncModel->is_sync = $status;
        $syncModel->additional_data = (!empty($message)) ? json_encode($message) : null;
        $syncModel->save();

        return $syncModel;
    }

    /**
     * sync master kelas ucup
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionSyncKelas()
    {
        $request = Yii::$app->request->post();
        $id = $request['id'];
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['route'] = 'app/get-data-kelas';

        $request = $restSerconn->post(self::$_url, [
            'body' => json_encode($params)
        ]);
        $response = json_decode($request->getBody(), true);

        if(!empty($response['Results'][0]['data'])) {
            $kelas = KelasPelayanan::find()
            ->where(['NOT',[ 'kelaspelayanan_kode' => null]])
            ->all();
            // return $kelas;

            foreach($response['Results']['0']['data'] as $key => $value) {

            }
            return $response['Results'][0];
        }
    }

    /**
     * sync master dokter
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionSyncDokter()
    {
        $request = Yii::$app->request->post();
        $connection = Yii::$app->db;
        $id = $request['id'];
        $insert = $update = false;
        $tmpDokter = $dokter = $updateDokter = $compareDokter = [];
        $syncModel = self::getDataSinkron()
        ->where(['sinkronisasi_id' => $id])
        ->one();


        $transaction = $connection->beginTransaction();
        try {
            $restSerconn = Yii::$app->serconn->guzzle();
            $headers = Yii::$app->request->headers;
            $params['authorization'] = $headers['authorization'];
            $params['x-owner'] = $headers['x-owner'];
            $params['route'] = 'app/get-list-pegawai';
    
            $request = $restSerconn->post(self::$_url, [
                'body' => json_encode($params)
            ]);

            $response = json_decode($request->getBody(), true);
            if(!empty($response['Results'][0]['data'])) {
                $tmpDokter = self::getDataPegawai()
                ->select(['dokter_id'])
                ->where(['kelompokpegawai_id' => 1])
                ->andWhere(['NOT', ['dokter_id' => null]])
                ->all();
    
                if (!empty($tmpDokter)) {
                    foreach ($tmpDokter as $key => $value) {
                        $compareDokter[] = $value['dokter_id'];
                    }
                }

                foreach($response['Results']['0']['data']['listdokter'] as $key => $value) {
                    $kodeDokter = (isset($value['kode'])) ? $value['kode'] : $value['KODE'];
                    $namaDokter =  (isset($value['nama'])) ? $value['nama'] : $value['NAMA'];
                    if (!in_array($kodeDokter, $compareDokter)) {
                        $dokter[] = [
                            'dokter_id' => $kodeDokter,
                            'nama_pegawai' => (!empty($namaDokter)) ? $namaDokter : '-',
                            'alamat_pegawai' => (isset($value['alamat'])) ? $value['alamat'] : $value['ALAMAT'],
                            'notelp_pegawai' => (isset($value['no_telp'])) ? $value['no_telp'] : $value['NO_TELP'],
                            'nomobile_pegawai' => (isset($value['no_hp'])) ? $value['no_hp'] : $value['NO_HP'],
                            'jeniskelamin' => DocoConstants::J_K_LAINNYA,
                            'kelompokpegawai_id' => (isset($value['kelompok_pegawai'])) ? $value['kelompok_pegawai'] : $value['KELOMPOK_PEGAWAI']
                        ];
                    }
                    if (in_array($kodeDokter, $compareDokter)) {
                        $updateDokter[] = [
                            'dokter_id' => $kodeDokter,
                            'nama_pegawai' => (!empty($namaDokter)) ? $namaDokter : '-',
                            'alamat_pegawai' => (isset($value['alamat'])) ? $value['alamat'] : $value['ALAMAT'],
                            'notelp_pegawai' => (isset($value['no_telp'])) ? $value['no_telp'] : $value['NO_TELP'],
                            'nomobile_pegawai' => (isset($value['no_hp'])) ? $value['no_hp'] : $value['NO_HP'],
                            'jeniskelamin' => DocoConstants::J_K_LAINNYA,
                            'kelompokpegawai_id' => (isset($value['kelompok_pegawai'])) ? $value['kelompok_pegawai'] : $value['KELOMPOK_PEGAWAI']
                        ];
                    }
                }

                if (!empty($dokter)) {
                    $insert = Pegawai::batchInsert($dokter, false);
                }

                if (!empty($updateDokter)) {
                    foreach ($updateDokter as $key => $value) {
                        $update = self::getDataPegawai()
                        ->select([
                            'dokter_id', 'nama_pegawai', 'alamat_pegawai', 'notelp_pegawai', 'nomobile_pegawai'
                        ])->where([
                            'kelompokpegawai_id' => 1,
                            'dokter_id' => $value['dokter_id']
                        ])->One();
                        $update->nama_pegawai = $value['nama_pegawai'];
                        $update->alamat_pegawai = $value['alamat_pegawai'];
                        $update->notelp_pegawai = $value['notelp_pegawai'];
                        $update->nomobile_pegawai = $value['nomobile_pegawai'];
                        $update->update();
                    }
                }
                
                if ($insert || $update) {
                    $syncModel->terakhir_update = date('Y-m-d H:i:s');
                    $syncModel->save();
                    $transaction->commit();
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM);
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * sync cara bayar ucup
     * groupcarabayar_id masih kosong
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function actionSyncCaraBayar()
    {
        $request = Yii::$app->request->post();
        $connection = Yii::$app->db;
        $id = $request['id'];
        $insert = $update = false;
        $tmpCaraBayar = $carabayar = $updateCarabayar = $compareCaraBayar = [];
        $syncModel = self::getDataSinkron()
        ->where(['sinkronisasi_id' => $id])
        ->one();
        $transaction = $connection->beginTransaction();
        try {
            $restSerconn = Yii::$app->serconn->guzzle();
            $headers = Yii::$app->request->headers;
            $params['authorization'] = $headers['authorization'];
            $params['x-owner'] = $headers['x-owner'];
            $params['route'] = 'app/get-data-cara-bayar';
    
            $request = $restSerconn->post(self::$_url, [
                'body' => json_encode($params)
            ]);

            $response = json_decode($request->getBody(), true);

            if(!empty($response['Results']['0']['data']) && empty($response['Results']['0']['error'])) {
                $tmpCaraBayar = self::getCaraBayar()
                ->select(['carabayar_kode'])
                ->andWhere(['NOT', ['carabayar_kode' => null]])
                ->all();

                if (!empty($tmpCaraBayar)) {
                    foreach ($tmpCaraBayar as $key => $value) {
                        $compareCaraBayar[] = $value['carabayar_kode'];
                    }
                }

                foreach($response['Results']['0']['data'] as $key => $value) {
                    $kodeCB = (isset($value['carabayar_kode'])) ? $value['carabayar_kode'] : $value['CARABAYAR_KODE'];
                    if (!in_array($kodeCB, $compareCaraBayar)) {
                        $carabayar[] = [
                            'carabayar_kode' => $kodeCB,
                            'carabayar_nama' => (isset($value['carabayar_nama'])) ? $value['carabayar_nama'] : $value['CARABAYAR_NAMA'],
                            'metode_pembayaran' => 403,
                            'is_subsidiasuransi' => false,
                            'is_subsidipemerintah' => false,
                            'is_subsidirs' => false,
                        ];
                    }
                    if (in_array($kodeCB, $compareCaraBayar)) {
                        $updateCarabayar[] = [
                            'carabayar_kode' => $kodeCB,
                            'carabayar_nama' => (isset($value['carabayar_nama'])) ? $value['carabayar_nama'] : $value['CARABAYAR_NAMA'],
                            'metode_pembayaran' => 403,
                            'is_subsidiasuransi' => false,
                            'is_subsidipemerintah' => false,
                            'is_subsidirs' => false,
                        ];
                    }
                }

                if (!empty($carabayar)) {
                    $insert = CaraBayar::batchInsert($carabayar, false);
                }

                if (!empty($updateCarabayar)) {
                    foreach ($updateCarabayar as $key => $value) {
                        $update = self::getCaraBayar()
                        ->select([
                            'carabayar_kode', 'carabayar_nama'
                        ])->where([
                            'carabayar_kode' => $value['carabayar_kode']
                        ])->One();
                        $update->carabayar_nama = $value['carabayar_nama'];
                        $update->carabayar_kode = $value['carabayar_kode'];
                        $update->update();
                    }
                }
                
                if ($insert || $update) {
                    $syncModel->terakhir_update = date('Y-m-d H:i:s');
                    $syncModel->save();
                    $transaction->commit();
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM);
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => $response['Results']['0']['error']
                ]);
            }
            return $response;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSyncPegawaiSatusehat() {
        $request = Yii::$app->request;
        /** di gunakan hanya untuk sinkronisasi app / bukan dari postman */
        if (empty($request->get()) || $request->get('limit_process')) {
            return ['message' => 'Sync Pegawai Satu Sehat Sedang diproses'];
        }

        $randString = DocoHelpers::generateRandomString();
        return [
            'message' => 'Sync Pegawai Satu Sehat Sedang diproses',
            'unique_str' => $randString,
            'defaultProcessLimit' => $this->defaultProcessLimit,
            'totalPerPage' => $request->get('countData', 0),
            'countData' => $request->get('countData', 0),
        ];
    }

    public function actionSyncRuanganSatusehat() {
        $request = Yii::$app->request;
        /** di gunakan hanya untuk sinkronisasi app / bukan dari postman */
        if (empty($request->get()) || $request->get('limit_process')) {
            return ['message' => 'Sync Ruangan Satu Sehat Sedang diproses'];
        }
        $randString = DocoHelpers::generateRandomString();
        return [
            'message' => 'Sync Ruangan Satu Sehat Sedang diproses',
            'unique_str' => $randString,
            'defaultProcessLimit' => $this->defaultProcessLimit,
            'totalPerPage' => $request->get('countData', 0),
            'countData' => $request->get('countData', 0),
        ];
    }

    public function actionSyncInstalasiSatusehat() {
        $request = Yii::$app->request;
        /** di gunakan hanya untuk sinkronisasi app / bukan dari postman */
        if (empty($request->get()) || $request->get('limit_process')) {
            return ['message' => 'Sync Instalasi Satu Sehat Sedang diproses'];
        }
        $randString = DocoHelpers::generateRandomString();
        return [
            'message' => 'Sync Instalasi Satu Sehat Sedang diproses',
            'unique_str' => $randString,
            'defaultProcessLimit' => $this->defaultProcessLimit,
            'totalPerPage' => $request->get('countData', 0),
            'countData' => $request->get('countData', 0),
        ];
    }

    public function actionSyncPasienSatusehat() {
        $request = Yii::$app->request;
        /** di gunakan hanya untuk sinkronisasi app / bukan dari postman */
        if (empty($request->get()) || $request->get('limit_process')) {
            return ['message' => 'Sync Pasien Satu Sehat Sedang diproses'];
        }
        $randString = DocoHelpers::generateRandomString();
        return [
            'message' => 'Sync Pasien Satu Sehat Sedang diproses',
            'unique_str' => $randString,
            'defaultProcessLimit' => $this->defaultProcessLimit,
            'totalPerPage' => $request->get('countData', 0),
            'countData' => $request->get('countData', 0),
        ];
    }

    private static function getDataSinkron()
    {
        return SinkronisasiK::find();
    }

    private static function getDataPegawai()
    {
        return Pegawai::find();
    }

    private static function getCaraBayar()
    {
        return CaraBayar::find();
    }

    // public function actionSyncInstalasiSatusehat() {
    //     return ['message' => 'Sync Instalasi Satu Sehat Sedang diproses'];
    // }
}
