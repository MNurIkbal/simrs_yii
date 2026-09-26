<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\ConfigTrait;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LokasiRakRekamMedik;
use app\modules\v1\models\WarnaDokRekamMedik;
use app\modules\v1\models\DokRekamMedis;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\LaporanThruputFn;
use yii\helpers\ArrayHelper;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\exceptions\ValidationException;
use app\modules\v1\models\DokumenV;
use app\modules\v1\models\DokumenUpload;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\services\Contracts\DiagnosaInterface;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public $diagnosaService;

    public function __construct($id, $module, $config = [],DiagnosaInterface $diagnosaService)
    {
        $this->diagnosaService = $diagnosaService;
        parent::__construct($id, $module, $config);
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['sync-sensus-pasien-ranap'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['sync-sensus-pasien-ranap'],
        ];

        return $behaviors;
    }

    public function actionPackDokRm()
    {
        $norak = $this->getNorak();
        $warnadok = $this->getWarnaDok();

        return ['data-norak' => $norak, 'data-warnadok' => $warnadok];
    }
    public function getNorak()
    {
        $request = Yii::$app->request;

        $model = new LokasiRakRekamMedik;
        $query = $model::find()
            ->where(['lokasirak_m.is_deleted' => false]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }
    public function getWarnaDok()
    {
        $request = Yii::$app->request;

        $model = new WarnaDokRekamMedik;
        $query = $model::find()
            ->where(['warnadokrekammedik_m.is_deleted' => false]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }
    public function actionGetNorm()
    {
        $request = Yii::$app->request;
        $model = new DokRekamMedis;
        $query = $model::find()->joinWith(['pasien' => function ($query) {
            $query->from('pasien_m');
        }]);
        // $query->select(['no_rekam_medik']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionGetRuangan()
    {
        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();
        // return $_GET['advanced-filter'];
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        return $queryRuangan->asArray()->all();
    }

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $word = $request->get('term');
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        if ($word) {
            $queryDokter->andWhere([
                'ILIKE', 'LOWER(nama_pegawai)', strtolower($word)
            ]);
        }
        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        return $queryDokter->limit(10)->asArray()->all();
    }

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key => $className) {
            $class = "app\modules\\v1\models\\" . $className;
            $model = new $class;
            $q = $model->find();
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
            // $results[$key] = $q->all();
        }
        return $results;
    }

    public function actionGetApi()
    {
        try {
            // get all master by request
            $listRequestMaster = [
                'instalasi' => 'Instalasi',
                'ruangan' => 'Ruangan',
            ];
            $master = $this->getListMaster($listRequestMaster);

            $result = [
                'master' => $master,
            ];

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


    public function actionGetInstalasiBy($id)
    {
        // return $id;
        $sql = 'SELECT t.*
                FROM
                    instalasi_m t
                JOIN ruangan_m r ON r.instalasi_id = t.instalasi_id
                WHERE r.ruangan_id = ' . $id . '
            ';

        $result = Instalasi::findBySql($sql)->all();

        return $result;
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => $id]);

        return $data->all();
    }

    public function actionGetDataNoRak()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);

        $sql = "select lokasirak_id, lokasirak_nama from lokasirak_m where lokasirak_nama LIKE '%{$term}%'
            and is_deleted='false'
            group by lokasirak_id, lokasirak_nama
            order by lokasirak_id asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }
    public function actionGetDataNoRekamMedik()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);

        $sql = "select dokrekammedis_m.dokrekammedis_id, pasien_m.no_rekam_medik from dokrekammedis_m
            inner join pasien_m on dokrekammedis_m.pasien_id=pasien_m.pasien_id
            where pasien_m.no_rekam_medik LIKE '%{$term}%'
            and dokrekammedis_m.is_deleted='false'
            group by dokrekammedis_m.dokrekammedis_id, pasien_m.no_rekam_medik
            order by pasien_m.no_rekam_medik asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetDataNoRekamMedikPasien()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);

        $sql = "select pasien_m.pasien_id,pasien_m.no_rekam_medik from pasien_m
            where pasien_m.no_rekam_medik LIKE '%{$term}%'
            and pasien_m.is_deleted='false'
            order by pasien_m.no_rekam_medik asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetAllRm()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);

        $Pasien = Pasien::find()
            ->select([
                'no_rekam_medik',
                'nama_pasien'
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $Pasien->andWhere(['like', 'LOWER(no_rekam_medik)', $term]);
        }

        return $Pasien->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionGetAllPengiriman()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);

        $sql = "select kirimdokrm_id, no_kirimdokrm 
            from kirimdokrm_t 
            where UPPER( no_kirimdokrm ) LIKE '%{$keyword}%'
            group by kirimdokrm_id, no_kirimdokrm
            order by kirimdokrm_id asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetRakData()
    {
        $request = Yii::$app->request;
        $sql = "select lokasirak_id, lokasirak_nama, lokasirak_namalainnya 
            from lokasirak_m 
            where is_deleted=false and is_active=true
            group by lokasirak_id, lokasirak_nama, lokasirak_namalainnya
            order by lokasirak_id asc limit 50
        ";
        $data['rak_data'] = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetSubrak()
    {
        $request = Yii::$app->request;
        $lokasirak_id = $request->get('lokasirak_id');

        $sql = "select subrak_id, subrak_nama 
            from subrak_m 
            where is_deleted=false and is_active=true and lokasirak_id={$lokasirak_id}
            group by subrak_id, subrak_nama
            order by subrak_id asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGetInstalasiPasien()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = new Instalasi;
        $query = $model::find()->select([
            'instalasi_m.instalasi_id',
            'instalasi_m.instalasi_nama'
        ])->joinWith([
            'ruangan' => function ($query) {
                $query->select([
                    'ruangan_m.ruangan_id'
                ]);
            }
        ]);

        if (isset($get['id'])) {
            $query->where(['ruangan_m.ruangan_id' => $get['id']]);
        }

        return $query->all();
    }

    public function actionGetRuanganPasien()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = new Ruangan;
        $query = $model::find()->joinWith([
            'instalasi'
        ]);

        if (isset($get['id'])) {
            $query->where(['instalasi_m.instalasi_id' => $get['id']]);
        }

        return $query->all();
    }

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = new Penjamin;
        $query = $model::find();
        if (isset($get['carabayar_id'])) {
            $query->where('carabayar_id = :carabayar_id', ['carabayar_id' => $get['carabayar_id']]);
        }

        if (isset($get['id'])) {
            $query->where('carabayar_id = :carabayar_id', ['carabayar_id' => $get['id']]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetCarabayar($id)
    {
        $sql = 'SELECT t.*
            FROM
                carabayar_m t
            JOIN penjamin_m r ON r.carabayar_id = t.carabayar_id
            WHERE r.penjamin_id = ' . $id . '
        ';

        $result = CaraBayar::findBySql($sql)->all();

        return $result;
    }

    public function actionGetLookUp($params)
    {
        $model = Lookup::find()->select([
            'lookup_id',
            'lookup_name'
        ])
            ->where([
                'lookup_type' => $params
            ])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->all();

        return $model;
    }

    public function actionGetJenisPenyakit()
    {
        $request = Yii::$app->request;
        $word = $request->get('term');
        if ($word) {
            return JenisKasusPenyakit::find()->where([
                'ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($word)
            ])->limit(10)->all();
        }
        return [];
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $term = $request->get('type');
        $word = $request->get('term');
        if ($term && $word) {
            return InfoDiagnosa::find()->where([
                'ILIKE', 'LOWER(tabularlist_versi)', strtolower($term)
            ])->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($word)]
            ])->limit(10)->all();
        }
        return [];
    }

    public function actionListPenjamin($carabayar_id = null)
    {
        $data = Penjamin::find();
        if ($carabayar_id) {
            $data->where(
                [
                    'is_deleted' => 'f',
                    'is_active' => 't',
                    'carabayar_id' => $carabayar_id
                ]
            );
        }
        $data->orderBy('penjamin_id');

        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');
        return $items;
    }

    public function actionSyncSensusPasienRanap()
    {
        $connection = Yii::$app->db;
        $connection->createCommand("
            DELETE FROM sensuspasienranap_r WHERE tgl_sensus::DATE = CURRENT_DATE;
        ")->queryOne();
        $connection->createCommand("
            SELECT * FROM f_cronsensuspasienranap();
        ")->queryOne();

        return [
            'message' => 'Sync Rekap Sensus Pasien Ranap Berhasil.'
        ];
    }

    public function actionThruput()
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        return LaporanThruputFn::getData($start_date, $end_date);
    }

    public function actionUploadDokumenPasien()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        try {
            $request = Yii::$app->request;
            $model = new DokumenUpload;
            $post = $request->post();
            $model->attributes = $post;
            
            if ($model->validate()) {
                if ($post) {
                    if ($model->save()) {
                        $transaction->commit();
                        $responseMessage =  ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'DokumenUpload');
                        $responseMessage =  ['data' => $errors, 'status' => 422];
                    }
                    return $responseMessage;
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDataDokumen($pendaftaran_id, $pasien_id = null)
    {
        $dokumen = $pendaftaran = [];
        $data = DokumenV::find()->where(['is_active' => true])->orderBy(['nama_dokumen' => SORT_ASC]);
        $items = ArrayHelper::map($data->all(), 'dokumen_id', 'nama_dokumen');
        $is_eklaim = ArrayHelper::map($data->all(), 'dokumen_id', 'is_eklaim');
        if(empty($pasien_id)){
            $dokumen = DokumenUpload::find()->where(['pendaftaran_id' => $pendaftaran_id]);
            $dokumen = $dokumen->asArray()->all();
            $sql = 'SELECT no_pendaftaran, no_rekam_medik, gcb.lookup_id AS groupcarabayar_id
                FROM
                    pendaftaran_t t
                JOIN pasien_m p ON p.pasien_id = t.pasien_id
                LEFT JOIN carabayar_m cb ON cb.carabayar_id = t.carabayar_id
                LEFT JOIN lookup_m gcb ON gcb.lookup_id = cb.groupcarabayar_id
                WHERE t.pendaftaran_id = :pendaftaran_id';

            $pendaftaran = Yii::$app->db->createCommand($sql)
             ->bindValue(':pendaftaran_id', $pendaftaran_id)
            ->queryOne();
        } else {
            $sql = 'SELECT no_rekam_medik
                FROM pasien_m t WHERE t.pasien_id = :pasien_id';
            $pendaftaran = Yii::$app->db->createCommand($sql)->bindValue(':pasien_id', $pasien_id)->queryOne();
        }
        return [
            'dokumen' => $dokumen,
            'dokumen_eklaim' => $is_eklaim,
            'items'   => $items,
            'pendaftaran' => $pendaftaran
        ];
    }

    public function actionGetDokumenList()
    {
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 1);
        $order = Yii::$app->request->get('order', null);
        $filter = Yii::$app->request->get('advanced-filter', []);
        $tabType = Yii::$app->request->get('tabType', null);
        $modulAccessed = Yii::$app->request->get('modulAccessed', null);
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id');
        $pasien_id = Yii::$app->request->get('pasien_id');
        $norm = Yii::$app->request->get('norm');
        
        if(!empty($norm) && empty($pasien_id)){
            $modelPasien = new Pasien;
            $pasien_id = $modelPasien::find()
            ->select(['pasien_id'])
            ->where(['no_rekam_medik' => $norm])
            ->asArray()->one();
        }

        $model = new DokumenUpload;
   
        if(!empty($pendaftaran_id)){
            $previous_id = Pendaftaran::find()->select([
                'prev_pendaftaran_id'
            ])->where([
                'pendaftaran_id' => $pendaftaran_id
            ])->asArray()->one();
    
            
            $query = $model->find()
                ->select([
                    'dokumenupload_t.*',
                    'dokumen_m.dokumen_id', 
                    new \yii\db\Expression('COALESCE(dokumen_m.nama_dokumen, dokumenupload_t.nama_dokumen_freetext) AS nama_dokumen'),
                    'dokumen_m.nama_dokumen_lainnya'
                ])
                ->leftJoin('dokumen_m', 'dokumen_m.dokumen_id = dokumenupload_t.dokumen_id');
            $query->where(['pendaftaran_id' => $pendaftaran_id]);         
            if(isset($previous_id['prev_pendaftaran_id'])){
                $query->Where(['in', 'pendaftaran_id',[$pendaftaran_id,$previous_id['prev_pendaftaran_id']]]);
            }
        } else {
            $query = $model->find()
                ->select([
                    'dokumenupload_t.*',
                    'ruangan_m.ruangan_nama', 
                    'pegawai_m.nama_pegawai', 
                    'dokumen_m.dokumen_id', 
                    new \yii\db\Expression('COALESCE(dokumen_m.nama_dokumen, dokumenupload_t.nama_dokumen_freetext) AS nama_dokumen'),
                    'dokumen_m.nama_dokumen_lainnya'
                ])
                ->leftJoin('dokumen_m', 'dokumen_m.dokumen_id = dokumenupload_t.dokumen_id')
                ->leftJoin('ruangan_m', 'ruangan_m.ruangan_id = dokumenupload_t.ruangan_id')
                ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = dokumenupload_t.dokter_id');
            $query->where(['pasien_id' => $pasien_id]);         
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $totalRecord = $query->count();

        if (!empty($order)) {
            $explodeOrder = explode(" ", $order);
            $orderKey = $explodeOrder[0];
            $orderType = strtolower($explodeOrder[1]);
        }

        if(!empty($pendaftaran_id)) {
            if (isset($orderKey) && isset($orderType)) {
                $query->orderBy([
                    'dokumenupload_t.created_date' => 'DESC',
                ]);
            }
        } else {
            $query->orderBy(
                'dokumenupload_t.doc_date DESC'
            );
        }

        $record = $query->asArray()->all();
        if ($page > 0) $record = $query->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();
        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $record,
        ];

    }

    public function actionDeleteUpload($id)
    {
        try {
            $data = DokumenUpload::find()->where([
                'dokumenupload_id' => $id
            ])->one();

            $result = (new DokumenUpload)->delete($id);

            return [
                'data' => !empty($data->filename) ? $data->filename : null,
                'path' => !empty($data->path) ? $data->path : null
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDetailDokumen()
    {
        return DokumenUpload::find()->select([
            'dokumenupload_t.path',
            'dokumenupload_t.filename',
            new \yii\db\Expression('COALESCE(dokumen_m.nama_dokumen, dokumenupload_t.nama_dokumen_freetext) AS nama_dokumen'),
            'dokumenupload_t.dokumenupload_id'
        ])
            ->leftJoin('dokumen_m', 'dokumen_m.dokumen_id = dokumenupload_t.dokumen_id')
            ->where([
                'dokumenupload_t.dokumenupload_id' => Yii::$app->request->get('dokumenupload_id')
            ])->asArray()->one();
    }

    public function actionGetDiagnosa()
    {
        return $this->diagnosaService->getDiagnosa();
    }
    
    public function actionGetInfoPasien()
    {
        $request = Yii::$app->request;
        $select = Yii::$app->request->get('select', []);
        if ($request->get('no_rekam_medik', '')) {
            $pasien = Pasien::find();
            
            if (!empty($select) && is_array($select)) {
                $pasien = $pasien->select($select);
            }
            
            $pasien = $pasien->where(['no_rekam_medik' => $request->get('no_rekam_medik')])->one();
            
            if ($pasien) {
                return DocoHelpers::response($pasien);
            } else {
                return (new DocoHelpers)->callback(DocoMessages::KEY_DYNAMIC_STATUS, [
                    'title' => 'Proses Gagal',
                    'text' => 'Data Pasien Tidak Ditemukan',
                ], 404);
            } 
        } else {
            return (new DocoHelpers)->callback(DocoMessages::KEY_DYNAMIC_STATUS, [
                'title' => 'No RM Tidak boleh kosong',
                'text' => 'No RM Tidak boleh kosong',
            ], 422);
        }
    }
}
