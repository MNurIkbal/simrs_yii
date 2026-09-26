<?php

/**
 * @author Randy Vianda Putra
 * @todo Allow all Radiologi
 * @copyright 09 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\ConfigTrait;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KelompokPemeriksaanRad;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\PemeriksaanRad;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\PerujukView;
use Doco\models\Instalasi;
use Doco\models\Ruangan;
use Doco\Services\InternalService;
use Doco\components\DocoMessages;
use Doco\components\DocoHelpers;
use Doco\models\Notifikasi;
use Doco\Notifications\RadiologiNotification;


class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\CaraBayar';
    /**
     * Untuk Kebutuhan Integerasi GE
     * @var array
     */
    public $messageBroker = [
        'sync-integerasi' => [
            'services' => [
                'Ris' => [
                    'RisBroker' => [
                        'payload' => ['pendaftaran_id', 'pasienmasukpenunjang_id']
                    ]
                ],
                'InaBroker' => [
                    'RisBroker' => [
                        'payload' => ['pendaftaran_id', 'pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id']
                    ]
                ]
            ]
        ]
    ];

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

    public function actionDataPegawai()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pegawai_m');

        $model = new Pegawai;
        $query = $model::find()
            ->select([
                'pegawai_m.pegawai_id',
                'pegawai_m.nomorindukpegawai',
                'pegawai_m.gelardepan',
                'pegawai_m.gelarbelakang',
                'pegawai_m.jeniskelamin',
                'pegawai_m.tempatlahir_pegawai',
                'pegawai_m.tgl_lahirpegawai',
                'pegawai_m.agama',
                'pegawai_m.alamat_pegawai',
                'pegawai_m.nama_pegawai',
                'pegawai_m.is_active',
            ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }


    public function actionGetKelompokPemeriksaanRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new KelompokPemeriksaanRad;
        $query = $model::find();
        if (isset($get['nama_kelompok'])) {
            $query->andWhere(['ILIKE', 'LOWER(nama_kelompok)', strtolower($get['nama_kelompok'])]);
        }
        return $query->asArray()->all();
    }

    public function actionGetJenisPemeriksaanRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new JenisPemeriksaanRad;
        $query = $model::find();
        if (isset($get['kelompokpemeriksaanrad_id'])) {
            $query->andWhere('kelompokpemeriksaanrad_id = ' . $get['kelompokpemeriksaanrad_id']);
        }
        return $query->asArray()->all();
    }
    public function actionGetPemeriksaanRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new PemeriksaanRad;
        $query = $model::find()->select([
            'pemeriksaanrad_m.daftartindakan_id',
            'daftartindakan_m.daftartindakan_nama'
        ])->joinWith([
            'daftarTindakan' => function ($query) {
                $query->select([
                    'daftartindakan_id'
                ]);
            }
        ]);
        if (!empty($get['jenispemeriksaanrad_id'])) {
            $query->andWhere('pemeriksaanrad_m.jenispemeriksaanrad_id = ' . $get['jenispemeriksaanrad_id']);
        }
        return $query->andWhere(['pemeriksaanrad_m.is_active' => true])->asArray()->all();
    }
    public function actionGetDokterRad()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new DokterView;
        $query = $model::find();
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $type = Yii::$app->request->get('type', null);
        $limit = Yii::$app->request->get('limit', 10);
        if (!empty($type) && $type == 'selectScroll') {
            $query->select([
                'pegawai_id as id',
                'nama_pegawai as text',
                'ruangan_id',
            ])
                ->andWhere([
                    'ruangan_id' => Yii::$app->jwt->ruangan_id
                ])
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit);
            if (!empty($term)) {
                $query->andWhere([
                    'like',
                    'LOWER(nama_pegawai)',
                    strtolower($term)
                ]);
            }
        } else {
            if (isset($get['nama_pegawai'])) {
                $query->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($get['nama_pegawai'])]);
            }
            if (isset($get['ruangan_id'])) {
                $query->andWhere('ruangan_id = ' . $get['ruangan_id']);
            }
        }
        return $query->asArray()->all();
    }

    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;
            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan'));
            $data_pegawai = $find_pegawai->asArray()->all();

            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
            ];
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

    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa1 FROM lookup_m WHERE lookup_type = 'status_periksa_penunjang'";
        $result = Lookup::findBySql($sql);

        return $result;
    }

    private function getPegawaiRuangan($id_ruangan)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;
    }

    private function getPenjamin()
    {
        $penjamin = Penjamin::find()->select([
            "penjamin_id",
            "penjamin_nama",
        ]);

        return $penjamin;
    }

    public function actionGetPasien($id)
    {
        try {
            $params = [];
            $model = PasienMasukPenunjangT::findOne($id);
            // $find = $this->getPasienDetail($id)->asArray()->one();
            return [
                'data' => $model
            ];
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

    // private function getPasienDetail($id = null)
    // {
    //     $condition = [];
    //     $sql = "
    //             SELECT
    //                 pendaftaran_id,
    //                 antrian_id,
    //                 pendaftaran_t.pegawai_id AS pegawai_id,
    //                 pasien_m.no_rekam_medik AS no_rekam_medik,
    //                 pendaftaran_t.no_pendaftaran AS no_pendaftaran,
    //                 pasien_m.nama_pasien AS nama_pasien,
    //                 pasien_m.pasien_id AS pasien_id,
    //                 pegawai_m.nama_pegawai AS nama_dokter,
    //                 pasien_m.tanggal_lahir,
    //                 gender.lookup_name AS jenis_kelamin,
    //                 pendaftaran_t.tgl_pendaftaran,
    //                 carabayar_m.carabayar_nama,
    //                 penjamin_m.penjamin_nama,
    //                 kelaspelayanan_m.kelaspelayanan_nama,
    //                 ruangan_m.ruangan_nama,
    //                 jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    //                 status_periksa.lookup_name AS status_periksa,
    //                 pendaftaran_t.ruangan_id
    //             FROM
    //                 pendaftaran_t
    //             JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
    //             LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
    //             JOIN lookup_m AS gender ON pasien_m.jeniskelamin::INTEGER = gender.lookup_id AND gender.lookup_type = 'jenis_kelamin'
    //             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
    //             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
    //             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
    //                             JOIN ruangan_m ON ruangan_m.ruangan_id = pendaftaran_t.ruangan_id
    //                             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
    //             JOIN lookup_m AS status_periksa ON pendaftaran_t.status_periksa::INTEGER = status_periksa.lookup_id AND status_periksa.lookup_type = 'status_periksa'

    //             WHERE
    //                 pendaftaran_t.is_deleted = FALSE AND pendaftaran_t.instalasi_id = 5
    //         ";

    //     if ($id){
    //         $sql .= " AND pendaftaran_id = :pendaftaran_id";
    //         $condition[':pendaftaran_id'] = $id;
    //     }

    //     $result = Pendaftaran::findBySql($sql, $condition);

    //     return $result;
    // }

    /**
     * Return data master such as : instalasi, ruangan
     * 
     * @param String term
     * @param String page
     * @param String limit
     * @param String type
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDropdown()
    {
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10);
        $type = Yii::$app->request->get('type', null);
        $dependentId = Yii::$app->request->get('dependentId', []);
        switch ($type) {
            case 'instalasi':
                $query = Instalasi::find()
                    ->select([
                        'instalasi_id as id',
                        'instalasi_nama as text'
                    ]);
                $searchFieldName = 'instalasi_nama';
                break;
            case 'ruangan':
                $query = Ruangan::find()
                    ->select([
                        'ruangan_id as id',
                        'ruangan_nama as text',
                        'instalasi_id'
                    ]);
                $searchFieldName = 'ruangan_nama';
                break;
            case 'ruanganrujukandari':
                $searchFieldName = 'ruangan_nama';
                $searchFieldNameOther = 'namaperujuk';
                $query = Ruangan::find()
                    ->select([
                        'ruangan_id as id',
                        'ruangan_nama as text',
                    ]);
                $query->andWhere([
                    'ilike',
                    'lower(' . $searchFieldName . ')',
                    strtolower($term)
                ]);

                // $queryPerujuk = PerujukView::find()
                //      ->select([
                //         'perujuk_id as id',
                //         'namaperujuk as text',
                //     ]);
                // $queryPerujuk->andWhere([
                //     'ilike',
                //     'lower(' . $searchFieldNameOther . ')',
                //     strtolower($term)
                // ]);
                // $query->union($queryPerujuk);
                $term = false;
                break;
            default:
                return $this->responseJson(400, 'Tipe tidak ditemukan.');
                break;
        }

        if (!empty($dependentId)) {
            $query->andWhere($dependentId);
        }
        if (!empty($term)) {
            $query->andWhere([
                'like',
                'lower(' . $searchFieldName . ')',
                strtolower($term)
            ]);
        }
        return array_merge([
            ['id' => '', 'text' => 'Semua']
        ], $query->offset(($page - 1) * $limit)->limit($limit + 1)->asArray()->all());
    }

    public function actionSyncIntegerasi($pendaftaran_id = null, $penunjangId = null, $pasienkirimkeunitlain_id = null)
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionReadNotif()
    {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (!empty($notifikasi_id)) {
            Notifikasi::updateAll(['is_read' => true], compact('notifikasi_id'));
            RadiologiNotification::updateTotalUnread();
            return $this->responseJson(200, 'Status Notifikasi berhasil diperbarui');
        } else {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosong');
        }
    }

    public function actionInitBucketNotification()
    {
        RadiologiNotification::updateTotalUnread();
        return $this->responseJson(200, 'Notifikasi radiologi terinisiasi.');
    }
}
