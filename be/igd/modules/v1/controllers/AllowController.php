<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\db\Expression;
use Doco\components\DocoAccessRule;
use Doco\components\DocoJwtHttpBearerAuth;

use app\modules\v1\models\Instalasi;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\KondisiKeluar;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\JadwalBukaPoliView;
use app\modules\v1\models\PasienPulangRdRjView;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\KasusPenyakitRuanganView;
use app\modules\v1\models\KesimpulanRD;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\KonfigPelayanan;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\KlasifikasiTekananDarah;
use app\modules\v1\models\TarifTotalRs;
use app\modules\v1\models\Cppt;
use app\modules\v1\payload\TarifPayload;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\Triase;
use app\modules\v1\models\LookupTransaksi;
use Doco\Services\Cache;
use yii\helpers\ArrayHelper;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    protected $allowAction = ['*'];
    public $modelClass = '';

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
            'except' => ['bundle-data-triage', 'get-pegawai', 'save-triage'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['bundle-data-triage', 'get-pegawai', 'save-triage'],
        ];

        return $behaviors;
    }

    public function actionListInstalasi()
    {
        try {
            $model = new Instalasi;
            $model = $model->find();

            $data = $model
                ->orderBy('instalasi_id')
                ->asArray()
                ->all();

            $results = [
                'data' => $data,
                'count' => count($data),
            ];
            return $results;
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

    public function QueryInfoPasienRdV()
    {
        $model = new InfoPasienRdV;
        return  $model::find();
    }

    /**
     * @author rizal
     * @since
     * @param
     * @return
     */
    public function actionGetApi()
    {
        $request = Yii::$app->request;
        try {
            $ruangan_id = $request->get('ruangan_id', null);
            $listRequest = [];
            $counter = 0;

            $listRequestLookup = [
                'jenis_kelamin',
                'status_periksa',
            ];
            $lookup = $this->listLookup($listRequestLookup);

            // get all master by request
            $listRequestMaster = [
                'carabayar' => 'CaraBayar',
                'penjamin' => 'Penjamin',
                'ruangan' => ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RD]],
            ];
            $master = $this->getListMaster($listRequestMaster);

            $allStsPrksa = $lookup['status_periksa'];
            $temp = [];
            foreach ($allStsPrksa as $key => $each) {
                $add_data = $each['additional_data'];
                if ($add_data) {
                    $instalasi = json_decode($add_data, true);
                    if (in_array(DocoConstants::INST_ID_RD, $instalasi['instalasi_id'])) {
                        $temp[] = $each;
                    }
                }
            }
            $lookup['status_periksa'] = $temp;
            $result = [
                'lookup' => $lookup,
                'master' => $master,
                'listDokter' => $this->getListDokter()
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

    /**
     * @author Rizal
     * @since
     * @param
     * @return array $results :
     * @desc clone from pendaftaran
     */
    private function listLookup($types)
    {
        $results = [];
        foreach ($types as $key => $type) {
            $lookup = new Lookup;
            $q = $lookup->find()->where(['lookup_type' => $type, 'is_active' => true, 'is_deleted' => false]);
            $results[$type] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $q, true, $type);
        }
        return $results;
    }

    private function getListMaster(array $listRequest)
    {
        $results = [];
        foreach ($listRequest as $key => $request) {
            $class = "app\modules\\v1\models\\" . (is_array($request) ? $request[0] : $request);
            $model = new $class;
            $q = $model->find();
            // $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            if (is_array($request) && $request[1]) {
                $q->andWhere($request[1]);
            }

            // order by
            if (is_array($request) && isset($request[2])) {
                $q->orderBy([$request[2] => SORT_ASC]);
            }
            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
        }
        return $results;
    }

    private function getListDokter()
    {
        $request = Yii::$app->request;

        $model = new DokterView;
        $query = $model::find()
            ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD])
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        if ($q = $request->get('q')) {
            $query = $query->andWhere(['ILIKE', 'nama_pegawai', $q]);
        }
        $results = $query->asArray()->all();

        $return = [];
        foreach ($results as $key => $result) {
            $return[$result['pegawai_id']] = $result;
        }
        return $return;
    }

    /**
     * @author Rizal
     * @since
     * @param
     * @return array $results :
     * @desc ONLY DOKTER IGD
     */
    public function actionGetListDokter()
    {
        return $this->getListDokter();
    }


    public function ListNoPendaftaran($ruangan_id = null)
    {
        $data = DaftarTindakan::find();
        if ($ruangan_id) {
            $data->leftJoin('tindakanruangan_mp', 'tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id');
            $data->where(['tindakanruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('daftartindakan_nama');
        $items = ArrayHelper::map($data->all(), 'daftartindakan_id', 'daftartindakan_nama');

        return $items;
    }

    public function actionGetKunjungan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = InfoPasienRdV::find()
            ->andWhere(['pendaftaran_id' => $get['id']])
            ->asArray()->one();
        return $model;
    }

    public function actionGetListDokterJaga()
    {
        $get = Yii::$app->request->get();
        $model = DokterView::find()
            ->where(['ruangan_id' => $get['ruangan_id']])
            ->asArray()->all();

        return $model;
    }

    public function actionGetJadwalDokter()
    {
        $get = Yii::$app->request->get();

        $model = InfoJadwalDokterView::find();

        if (isset($get['list'])) {
            $model->select(['pegawai_id', 'nama_pegawai']);
            $model->distinct();
        }

        if (isset($get['ruangan_id'])) {
            $model->andWhere(['ruangan_id' => $get['ruangan_id']]);
        }

        if (isset($get['tgl_lanjut_rawat'])) {
            $day = date('N', strtotime($get['tgl_lanjut_rawat']));
            $id_lookup = DocoConstants::$look_hari[$day];
            $model->andWhere(['hari_jadwalbuka' => $id_lookup]);
        }
        return $model->asArray()->all();
    }

    public function actionTest()
    {

        $check = Yii::$app->jwt->user->katakunci_pemakai;
        return Yii::$app->security->validatePassword('123456', $check);
    }

    public function actionGetAllDiagnosa()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);


        $diagnosa = Diagnosa::find()
            ->select([
                'diagnosa_id',
                'diagnosa_nama',
                new Expression("CONCAT(diagnosa_kode,' - ',diagnosa_nama) AS nama_diagnosa")
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $diagnosa->andWhere(['like', 'LOWER(diagnosa_nama)', $term]);
            $diagnosa->orWhere(['like', 'LOWER(diagnosa_kode)', $term]);
        }

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }


    /**
     * @author Rizal
     * @since 2018-07-25 11:37:16
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc
     */
    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = Ruangan::find();
        // $result->select(['ruangan_id','ruangan_nama']);
        if (isset($get['instalasi_id'])) {
            $result->andWhere(['instalasi_id' => $get['instalasi_id']]);
        }
        $result->andWhere([
            'is_deleted' => false,
            'is_active' => true,
        ]);

        return $result->asArray()->orderBy(['ruangan_urutan' => SORT_ASC])->all();
    }

    public function actionListDokterPerujuk() {
        $request = Yii::$app->request;
        $get = $request->get();

        $keyword = $request->get('q', '');
        $page = $request->get('page', 1);
        $attr = $request->get('attr', []);
        $perpage = 10;

        $query = PegawaiView::find()->select(['pegawai_id', 'nama_pegawai'])
            ->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($keyword)])
            ->andWhere(['kelompokpegawai_namalainnya' => DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS]);
        if(!empty($attr)) {
            $query->andWhere($attr);
        }

        $query->offset(($page-1)*$perpage)->limit($perpage);
        $data = ['data' => $query->distinct()->asArray()->all()];
        return $data;
    }

    /**
     * @author rizal
     * @since
     * @param
     * @return
     */
    public function actionGetApiKesimpulan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        try {
            // get all master by request
            $listRequestMaster = [
                'carakeluar' => ['CaraKeluar', '', 'carakeluar_urutan'],
                'gcs' => ['Gcs', '', 'gcs_nilaimin'],
                'metodegcs' => 'MetodeGcs',
                'kondisikeluar' => 'KondisiKeluar'
                // 'ruangan' => ['Ruangan', ['instalasi_id'=>DocoConstants::INST_ID_RD]],
            ];
            $master = $this->getListMaster($listRequestMaster);

            $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
            $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
            $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
            $data_listgcs = [];

            foreach ($master['metodegcs'] as $key => $value) {
                if (!$value['metodegcs_nilai']) {
                    continue;
                }

                if ($value['metodegcs_singkatan'] == $gcsindicator_eye) {
                    $data_listgcs['eye'][] = $value;
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal) {
                    $data_listgcs['verbal'][] = $value;
                } elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik) {
                    $data_listgcs['motorik'][] = $value;
                }
            }

            $pasienPulang = PasienPulang::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            $kesimpulan = KesimpulanRD::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()->one();

            $result = [
                // 'lookup' => $lookup,
                'master' => $master,
                'data_listgcs' => $data_listgcs,
                'pasienPulang' => $pasienPulang,
                'kesimpulan' => $kesimpulan,
                'listDataApotek' => $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A),
                'listDataSigna' => $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()),
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

    /**
     * @author rizal
     * @since
     * @param
     * @return
     */
    public function actionGetApiLaporanIgd()
    {
        $request = Yii::$app->request;
        try {

            // get all lookup by request
            $listRequestLookup = [
                'status_periksa',
                'jenis_kelamin',
            ];
            $lookup = $this->listLookup($listRequestLookup);

            // get all master by request
            $listRequestMaster = [
                'carabayar' => 'CaraBayar',
                'penjamin' => 'Penjamin',
                'ruangan' => ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RD]],
            ];
            $master = $this->getListMaster($listRequestMaster);

            $allStsPrksa = $lookup['status_periksa'];
            $temp = [];
            foreach ($allStsPrksa as $key => $each) {
                $add_data = $each['additional_data'];
                if ($add_data) {
                    $instalasi = json_decode($add_data, true);
                    if (in_array(DocoConstants::INST_ID_RD, $instalasi['instalasi_id'])) {
                        $temp[] = $each;
                    }
                }
            }
            $lookup['status_periksa'] = $temp;

            $modelDokter = DokterView::find()
                ->select('pegawai_id, nama_pegawai, instalasi_id')
                ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD])
                ->groupBy('pegawai_id, nama_pegawai, instalasi_id')
                ->asArray()->all();
            $dokter = $modelDokter ?: [];

            $modelPenyakit = KasusPenyakitRuanganView::find()
                ->select('jeniskasuspenyakit_id, jeniskasuspenyakit_nama, instalasi_id')
                ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD])
                ->groupBy('jeniskasuspenyakit_id, jeniskasuspenyakit_nama, instalasi_id')
                ->asArray()->all();
            $kasuspenyakit = $modelPenyakit ?: [];

            $result = [
                'lookup' => $lookup,
                'master' => $master,
                'dokter' => $dokter,
                'kasuspenyakit' => $kasuspenyakit
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

    /**
     * @author Rizal
     * @since 2018-09-10 16:09:16
     * @param int carakeluar_id
     * @return array list of kondisikeluar
     * @desc
     */
    public function actionGetListKondisikeluar()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = KondisiKeluar::find();
        if (isset($get['carakeluar_id'])) {
            $result->andWhere(['carakeluar_id' => $get['carakeluar_id']]);
        }
        $result->andWhere([
            'is_deleted' => false,
            'is_active' => true,
        ]);

        return $result->asArray()->all();
    }

    /**
     * @author Rizal F. <rizal@docotel.com>
     * @since
     * @param tanggalx
     * @return list jadwal poli
     */
    public function actionGetJadwalPoli()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $day = date('N', strtotime($get['tanggal']));
            $id_lookup = DocoConstants::$look_hari[$day];
            $model = JadwalBukaPoliView::find()
                ->select(['ruangan_id', 'ruangan_nama'])
                ->distinct(['ruangan_id'])
                ->andWhere([
                    'hari' => $id_lookup
                ])
                ->orderBy(['ruangan_nama' => SORT_ASC])
                ->asArray();

            if (!isset($get['instalasi_id'])) {
                $model->andWhere(['instalasi_id' => DocoConstants::INST_ID_RJ]);
            }

            return $model->all();
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


    public function actionGetCarabayar()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $sql = 'SELECT t.*
            FROM
                carabayar_m t
            JOIN penjamin_m r ON r.carabayar_id = t.carabayar_id
        ';

        if ($get['id']) {
            $sql .= ' WHERE r.penjamin_id = ' . $get['id'];
        }

        $result = CaraBayar::findBySql($sql)->all();

        return $result;
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

    public function actionGetJenisPenyakit()
    {
        $request = Yii::$app->request;
        $word = $request->get('term');
        if ($word) {
            return KasusPenyakitRuanganView::find()
                ->select('jeniskasuspenyakit_id, jeniskasuspenyakit_nama, instalasi_id')
                ->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD])
                ->andWhere([
                    'ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($word)
                ])
                ->groupBy('jeniskasuspenyakit_id, jeniskasuspenyakit_nama, instalasi_id')
                ->asArray()->all();
        }
        return [];
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
        return $queryDokter->asArray()->all();
    }


    public function actionGetAllDokter()
    {
        $model = DokterView::find()
            ->select('pegawai_id, nama_pegawai')
            ->orderBy(['nama_pegawai' => SORT_ASC])
            ->groupBy('pegawai_id, nama_pegawai')
            ->asArray()->all();
        return $model;
    }

    public function actionDataKondisiKeluar($carakeluar_id)
    {
        $sql = "select kondisikeluar_id, kondisikeluar_nama, carakeluar_id from kondisikeluar_m where carakeluar_id={$carakeluar_id} and is_deleted = false and is_active = true
            order by kondisikeluar_id asc
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    private function getRuanganInstalasi($instalasi_singkatan = null)
    {
        // Try catch
        try {
            // Sql
            $sql = "
                SELECT DISTINCT
                    *
                FROM
                    ruangan_m
                LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE ruangan_m.is_deleted = FALSE
            ";

            if ($instalasi_singkatan) {
                $sql .= " AND instalasi_m.instalasi_singkatan = '" . $instalasi_singkatan . "'";
            }

            // Result
            $result = Ruangan::findBySql($sql);

            // Return result
            return $result;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Method untuk mendapatkan data klasifikasi tekanan darah
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @update [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
     * @desc cloning dari allow rajal
     */
    public function actionGetDataKlasifikasiTekananDarah()
    {
        try {
            $result = KlasifikasiTekananDarah::find()->all();
        } catch (\yii\db\Exception $e) {
            $result = [];
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNewDiagnosa()
    {
        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];
            $type = $get['type'];
            $page = Yii::$app->request->get('page', 0);
            $limit = Yii::$app->request->get('limit', 5);
            $offset = Yii::$app->request->get('offset', 0);

            $is_perawat = !empty($get['is_perawat']) ? $get['is_perawat'] : 0;
            $kelompok_diagnosa = DocoConstants::$mapp_kel_diagnosa;
            if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                $tabularlist_versi = 'ICD X';
            } else if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA) {
                $tabularlist_versi = 'ICD X';
            } else {
                $tabularlist_versi = 'ICD IX';
            }

            if ($is_perawat) {
                $tabularlist_versi = 'ICD_KEP';
            }

            $model = DiagnosaView::find()->andWhere(['is_deleted' => false, 'is_active' => true]);

            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'diagnosa_nama', $q]);
                $model->orWhere(['ilike', 'diagnosa_kode', $q]);
            }

            if (isset($tabularlist_versi) && $tabularlist_versi != '') {
                $model->andWhere(['tabularlist_versi' => $tabularlist_versi]);
            }

            return $model->offset($offset)->limit($limit)->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetListCaraKeluar($asArray = false, $all = false)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $result = [];
        try {
            $model = new CaraKeluar;
            $query = $model::find();
            if (!$all) {
                // di komen tanggal 06-11-2019 Permintaan Om Juned
                // $query->where(['!=','carakeluar_id',DocoConstants::CARA_KELUAR_RUJUK_RAWAT_INAP]);
            }
            if ($asArray) {
                $result = $query->asArray()->all();
            } else {
                $result = $query->all();
            }
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return $result;
    }
    public function actionGetFilterPasienPulang()
    {
        try {
            $request = Yii::$app->request;

            $getDataRuangan = Ruangan::find()->where(['instalasi_id' => DocoConstants::INST_ID_RD])->asArray()->all();
            $dataRuangan = ArrayHelper::map($getDataRuangan, 'ruangan_nama', 'ruangan_nama'); //ruangan_id

            $getDataJenisKelamin = $this->getLookupByType('jenis_kelamin')->asArray()->all();
            $dataJenisKelamin = ArrayHelper::map($getDataJenisKelamin, 'lookup_name', 'lookup_name'); //lookup_id

            $getDataPenjamin = $this->getOrSetCache(DocoConstants::VAR_CACHE_PENJAMIN, Penjamin::find());
            $dataPenjamin = ArrayHelper::map($getDataPenjamin, 'penjamin_nama', 'penjamin_nama'); //penjamin_id

            $getDataDokter = $this->getPegawaiRuangan(DocoConstants::INST_ID_RD, DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS);
            $dataDokter = ArrayHelper::map($getDataDokter->asArray()->all(), 'nama_pegawai', 'nama_pegawai');

            $getDataCaraKeluar = $this->actionGetListCaraKeluar(true, true);
            $dataCaraKeluar = ArrayHelper::map($getDataCaraKeluar, 'carakeluar_nama', 'carakeluar_nama'); //carakeluar_id

            $getCaraBayar = $this->getCaraBayar();
            $dataCaraBayar = ArrayHelper::map($getCaraBayar, 'carabayar_nama', 'carabayar_nama');


            return [
                'dataRuangan' => $dataRuangan,
                'dataJenisKelamin' => $dataJenisKelamin,
                'dataPenjamin' => $dataPenjamin,
                'dataDokter' => $dataDokter,
                'dataCaraKeluar' => $dataCaraKeluar,
                'dataCaraBayar' => $dataCaraBayar,
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
    public function getLookupByType($type = null)
    {
        try {
            $result = Lookup::find();

            if ($type) {
                $result->where(['lookup_type' => $type]);
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    private function getPegawaiRuangan($ruangan_id = null, $kelompokpegawai = null)
    {
        try {
            $sql = "
                SELECT
                    ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.*
                FROM ruanganpegawai_mp
                JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id
                JOIN kelompokpegawai_m ON kelompokpegawai_m.kelompokpegawai_id = pegawai_m.kelompokpegawai_id
                JOIN ruangan_m on ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m on ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE ruanganpegawai_mp.is_deleted = FALSE
            ";

            if ($ruangan_id) {
                $sql .= " AND instalasi_m.instalasi_id =" . $ruangan_id;
            }

            if ($kelompokpegawai) {
                $sql .= " AND kelompokpegawai_m.kelompokpegawai_namalainnya ='" . $kelompokpegawai . "'";
            }

            $result = RuanganPegawai::findBySql($sql);

            return $result;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    /**
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-10 16:53
     * get data obat
     */
    public function actionGetDataObat()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id', null);
            $obatalkes_id = $request->get('obatalkes_id', null);

            $data = InfoObatAlkesView::find()->where(['obatalkes_id' => $obatalkes_id]);
            $items = $data->asArray()->one();

            $dataStok = StokObatAlkesR::find()->where([
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ]);

            $stok = $dataStok->asArray()->one();

            return [
                'items' => $items,
                'stok' => $stok,
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

    /**
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-10 16:53
     * get data depdrop satuan besar
     */
    public function actionListSatuanBesar()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $data = SatuanKonversiView::find()->where([
                'jenis' => 'obat',
                'obatalkes_id' => $obatalkes_id,
                'is_active' => true,
            ]);
            $items = $data->asArray()->all();

            return $items;
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

    public function actionGetKonversi()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $satuanbesar_id = $request->get('satuanbesar_id', null);
            $ruangan_id = $request->get('ruangan_id', null);
            $satuanKonversi = SatuanKonversiView::find()
                ->where([
                    'jenis' => 'obat',
                    'obatalkes_id' => $obatalkes_id,
                    'satuanbesar_id' => $satuanbesar_id
                ])->asArray()->one();

            $dataObat = StokObatAlkesR::find()->where([
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ])->asArray()->one();

            return [
                'konversi' => $satuanKonversi,
                'obat' => $dataObat,
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

    public function actionGetSatuan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $satuanunit_id = $get['id'];
        $model = SatuanUnit::findOne($satuanunit_id);
        return $model;
    }

    public function actionDoctorList()
    {
        $q = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getDokter = DokterView::find();
        $getDokter->select([
            'distinct(pegawai_id) as id',
            "nama_pegawai as text",
        ]);
        if (!is_null($q)) {
            $getDokter->where([
                'ILIKE', 'nama_pegawai', $q
            ]);
        }
        $getDokter->limit($limit);
        $getDokter->offset((($page - 1) * $limit));
        return $getDokter->asArray()->all();
    }

    public function actionGetTarifTindakan()
    {
        $request = Yii::$app->request;
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
        $namaPemeriksaan = $request->get('daftartindakan_nama');

        $konfig_golongan_tindakan_bedah = LookupTransaksi::find()->select(['additional_value'])->where(['kode_transaksi' => 'golongan_tindakan_bedah'])->asArray()->one();
        $data_konfig_filter_golongan = json_decode($konfig_golongan_tindakan_bedah['additional_value']);

        $payload = new TarifPayload;
        $payload->attributes = $request->get();

        if (!$payload->validate()) {
            return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }

        switch ($payload->instalasi_id) {
            case $this->constans->actionGetId('LAB'):
                $type = 'penunjang';
                $kategori = 'lab';
                break;
            case $this->constans->actionGetId('RAD'):
                $type = 'penunjang';
                $kategori = 'rad';
                break;
            case $this->constans->actionGetId('IBS'):
                $type = 'penunjang';
                $kategori = 'operasi';
                break;
            case $this->constans->actionGetId('FISIOTERAPI'):
                $type = 'penunjang';
                $kategori = 'fisio';
                break;
            default:
                $type = 'pelayanan';
                $kategori = null;
                break;
        }

        $model = (new TarifTotalRs([
            'extParam' => [
                $payload->ruangan_id,
                $payload->penjamin_id,
                $payload->kelaspelayanan_id,
                $type
            ]
        ]));
        $query = $model::find();

        if (!empty($kategori)) {
            $query->andWhere([
                'jenis' => $kategori
            ]);
        }

        if (!empty($namaJenis)) {
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        if (!empty($namaPemeriksaan)) {
            //$query->andWhere(['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)]);
            $filter_golongan = !empty($data_konfig_filter_golongan) && $data_konfig_filter_golongan ? ['OR LIKE', 'LOWER(nama_kelompok)', strtolower($namaPemeriksaan)] : [];

            $query->andFilterWhere(
                [
                    'or',
                    ['OR LIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)],
                    ['OR LIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaPemeriksaan)],
                    ['OR LIKE', 'LOWER(kode)', strtolower($namaPemeriksaan)],
                    $filter_golongan
                ]
            );
        }

        $groupingtindakan_penunjang = $this->constans->actionGetAdditional('groupingtindakan_penunjang', true);
        $result = [
            'data' => $query->asArray()->all(),
            'groupingtindakan_penunjang' => $groupingtindakan_penunjang,
        ];

        return $result;
    }

    public function actionCaraBayarList()
    {
        $query = CaraBayar::find()
            ->select([
                'carabayar_nama',
                'carabayar_kode_warna'
            ])
            ->andWhere(['not', ['is_active' => false]])
            ->orderBy(['carabayar_kode_warna' => SORT_ASC])
            ->asArray()
            ->all();
        return $query;
    }

    // Create soap without diag
    public function actionCreateSoap($ruangan, $pendaftaranId, $pegawaiId, $pasienId, $admisiId = null)
    {
        $strip = '-';
        $valDiag = [];
        $valDiag['text'] = $strip;
        // Try
        try {
            $model = new Cppt;
            $model->scenario = 'soap';
            $model->subject = $strip;
            $model->object = $strip;
            $model->planning = $strip;
            $model->a_diag_utama = $valDiag;
            $model->a_diag_penyerta = $valDiag;
            $model->pendaftaran_id = $pendaftaranId;
            $model->pegawai_id = $pegawaiId;
            $model->pasien_id = $pasienId;
            $model->pasienadmisi_id = $admisiId;
            $model->ruangan_id = $ruangan;

            $model->tgl_cppt = date('Y-m-d H:i:s');
            if ($model->validate()) {
                if ($model->save()) {
                    return $model->cppt_id;
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
            }
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
     * @Author: Aris (aris.m@docotel.com)
     * @Date:   2021-March-31 16:53
     * get data BMI
     */
    public function actionDataBmi()
    {
        $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find());
        return [
            'data-bmi'               => $data_bmi,
        ];
    }

    public function actionGetKamarTempatTidur()
    {
        $q = Yii::$app->request->get('term', null);
        $statusIsi = Yii::$app->request->get('status_isi', null);
        $kamarruangan_jenis = Yii::$app->request->get('kamarruangan_jenis', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $getKamar = KamarRuanganView::find();
        $getKamar->select([
            'kamartempattidur_id as id',
            "CONCAT(kamarruangan_nokamar, ' - ', no_tempattidur) as text",
            'kamarruangan_jenis',
            'ruangan_id'
        ]);
        if (!empty($q)) {
            $getKamar->where([
                'ILIKE', 'kamarruangan_nokamar', $q
            ]);
            $getKamar->orWhere([
                'ILIKE', 'no_tempattidur', $q
            ]);
        }
        if (!empty($statusIsi)) {
            $getKamar->andWhere([
                'status_isi' => $statusIsi
            ]);
        }
        if (!empty($kamarruangan_jenis)) {
            $getKamar->andWhere([
                'kamarruangan_jenis' => $kamarruangan_jenis
            ]);
        }
        $getKamar->limit($limit);
        $getKamar->offset((($page - 1) * $limit));
        return $getKamar->asArray()->all();
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $nip = $request->get('nip', null);
        $query = Pegawai::find()
            ->select([
                'pegawai_id',
                'nomorindukpegawai as nip_pegawai',
                'nama_pegawai',
                'kelompokpegawai_id'
            ])
            ->where([
                'nomorindukpegawai' => $nip
            ]);

        return $query->asArray()->one();
    }

    public function actionBundleDataTriage()
    {
        $bedQuery = KamarRuanganView::find()
            ->select([
                'kamarruangan_v.kamartempattidur_id',
                'no_tempattidur',
                'instalasi_id',
                'status_isi'
            ])
            ->where([
                'instalasi_id' => DocoConstants::VAR_I_RD,
            ])
            ->groupBy('kamarruangan_v.kamartempattidur_id, no_tempattidur, instalasi_id, status_isi')
            ->asArray()
            ->all();


        $EditAbleBedQuery = Triase::find()->select([
            'triase_t.pendaftaran_id',
            'triase_t.kamartempattidur_id',
            'triase_t.tgl_triase',
            'kamarruangan_v.status_isi'
        ])
        ->join('JOIN', 'kamarruangan_v', 'kamarruangan_v.kamartempattidur_id=triase_t.kamartempattidur_id')
        ->where([
            'kamarruangan_v.instalasi_id' => DocoConstants::VAR_I_RD,
            'kamarruangan_v.status_isi' => true,
            'triase_t.pendaftaran_id' => null
        ])
        ->orderBy(['triase_t.tgl_triase'=>SORT_DESC])
        ->distinct()
        ->asArray()
        ->all();

        if(!empty($EditAbleBedQuery)){
            foreach($bedQuery as $key => $value){
                $bedQuery[$key]['status_edit'] = false;
                foreach($EditAbleBedQuery as $key1 => $value1){
                    if($bedQuery[$key]['kamartempattidur_id'] == $EditAbleBedQuery[$key1]['kamartempattidur_id']){
                        $bedQuery[$key]['status_edit'] = true;
                    }
                }
            }
        }
        $dataGcs = $this->getDataGcs();
        return [
            'dataBed' => $bedQuery,
            'dataGcs' => $dataGcs,
        ];





    }

    private function getDataGcs()
    {
        $getMetodeGcs = MetodeGcs::find()->select([
            'metodegcs_id as id',
            'metodegcs_nama',
            'metodegcs_singkatan',
            'metodegcs_nilai',
        ])->where([
            'IS NOT', 'metodegcs_nilai', NULL
        ])->asArray()->all();
        $metodeGcs = [];
        foreach ($getMetodeGcs as $item => $metode) {
            $metode['text'] = $metode['metodegcs_nama'] . ' - ' . $metode['metodegcs_nilai'];
            $metodeGcs[$metode['metodegcs_singkatan']][] = $metode;
        }

        return [
            'data-listgcs' => $metodeGcs
        ];
    }

    public function actionSaveTriage()
    {
        $request = Yii::$app->request;
        $payloadData = $request->post('formdata', []);
        $model = new Triase;
        $model->attributes = $payloadData;
        $transaction = Yii::$app->db->beginTransaction();

        $updateKamarTempatTidurTriase = Triase::updateAll([
            'kamartempattidur_id' => null
        ],"kamartempattidur_id = {$payloadData['kamartempattidur_id']}");

        if ( !$model->validate() ) {
            $response = $model->getErrors();

            return $this->responseJson(400, 'Simpan data gagal', $response);
        }

        if ( !$model->save() ) {
            return $this->responseJson(400, 'Proses penyimpanan data gagal.');
        }
        if ( !empty($model->kamartempattidur_id) ) {
            $updateKamarTempatTidur = KamarTempatTidur::updateAll([
                'status_isi' => true
            ], 'kamartempattidur_id = :kamartempattidur_id', [
                ':kamartempattidur_id' => $model->kamartempattidur_id
            ]);


            if ( !$updateKamarTempatTidur ) {
                $transaction->rollBack();
                return $this->responseJson(400, 'Proses penyimpanan data gagal.');
            }

            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'ketersediaan-bed-'.Yii::$app->params['mode'],
                'message' => json_encode(['kamartempattidur_id'=>$model->kamartempattidur_id, 'status_isi'=>true,'status_edit'=>true]),
            ]);
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Formulir Triase Berhasil!');
    }

    public function actionPelayananConfigButton() {
        $data = KonfigPelayanan::find()
            ->where(['instalasi_id' => DocoConstants::INST_ID_RD, 'is_active' => true, 'is_deleted' => false])
            ->orderBy(['konfigpelayanan_id'=>SORT_ASC])
            ->asArray()->all();
        return ['data' => $data];
    }

    public function actionTimeResetSuggestSoap($kode_lookup)
    {
        try {
            $result = LookupTransaksi::find()->select([
                'additional_value'
            ])
            ->where(['kode_transaksi' => $kode_lookup])
            ->asArray()->one();
            $time_reset_rj = json_decode($result['additional_value']);
            return [
                'data' => $time_reset_rj[1],
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

    public function actionGetJenisKamar()
    {
        $q = Yii::$app->request->get('term', null);
        $statusIsi = Yii::$app->request->get('status_isi', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;

        $lookup = new Lookup;
        $lookup = $lookup->find()->where(['lookup_type' => 'jenis_kamar', 'is_active' => true, 'is_deleted' => false]);
        if(!empty($q)){
            $lookup = $lookup->andWhere(['ILIKE', 'lookup_value', $q]);
        }
        $jenis_kamar = $lookup->all();

        $jenis_kamar = array_map(function($val){
                          return ['id' => $val['lookup_id'], 'text' => $val['lookup_value'], 'value' => $val['lookup_value']];
                      }, $jenis_kamar);
        
        return $jenis_kamar;
    }
    public function actionGetListDokterSpesialis()
    {
        $instalasi_id = Yii::$app->request->get('instalasi_id', 3);
        $ruangan_id = Yii::$app->request->get('ruangan_id', '');
        $q = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10) + 1;
        $listDokterSpesialis = DokterView::find()
        ->select([
            'pegawai_id as id',
            'nama_pegawai as text',
            'pegawai_id'
        ])
        ->where(['kelompokpegawai_id' => '1'])
        ->andWhere(['not', ['spesialis_id' => null]]);

        if(!empty($instalasi_id)){
            $listDokterSpesialis = $listDokterSpesialis->andWhere(['instalasi_id' => $instalasi_id]);
        }
        if(!empty($ruangan_id)){
            $listDokterSpesialis = $listDokterSpesialis->andWhere(['ruangan_id' => $ruangan_id]);
        }
        if (!empty($q)) {
            $listDokterSpesialis->andWhere([
                'ILIKE', 'nama_pegawai', $q
            ]);
        }

        $listDokterSpesialis->limit($limit);
        $listDokterSpesialis->offset((($page - 1) * $limit));
        return $listDokterSpesialis->orderBy('pegawai_id')->distinct()->asArray()->all();
    }

    private function getCaraBayar()
    {
        $model = CaraBayar::find();
        $query = $model->where(['is_active' => true])
            ->andWhere(['is_deleted' => false])
            ->orderby(['carabayar_nama' => SORT_ASC]);
        return $query->asArray()->all();
    }

    public function actionGetKonfigSystem()
    {
        $konfigSystem = KonfigSystem::find()->one();
        $orderBedahTanpaTindakan = isset($konfigSystem['order_bedah_tanpa_tindakan']) && !empty($konfigSystem['order_bedah_tanpa_tindakan']) ? $konfigSystem['order_bedah_tanpa_tindakan'] : false;
        $instruksiSoap = isset($konfigSystem['hide_instruksi_soap']) && !empty($konfigSystem['hide_instruksi_soap']) ? $konfigSystem['hide_instruksi_soap'] : false;

        return [
            'order_bedah_tanpa_tindakan' => $orderBedahTanpaTindakan,
            'hide_instruksi_soap' => $instruksiSoap
        ];
    }
    public function actionListPegawai()
    {
        try {
            $request = Yii::$app->request;
            $jabatan_id = $request->get('jabatan_id' . null);
            $kelompokpegawai = $request->get('kelompokpegawai_id', []);
            $data = Pegawai::find()->where(['is_deleted' => false]);
            if (!is_null($jabatan_id)) {
                $data->andWhere(['jabatan_id' => $jabatan_id]);
            }
            if (!empty($kelompokpegawai)) {
                $data->andWhere(['IN', 'kelompokpegawai_id', $kelompokpegawai]);
            }
            $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

            return $items;
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

    public function actionGetKalaTigaData()
    {
        $cacheKeperawatan = Cache::getLookUpKeperawatan();
        $setKey = ArrayHelper::index($cacheKeperawatan, null, 'lookup_type');
        $getCacheKeperawatan = ArrayHelper::filter($setKey, [
            'laserisasi'  
        ]);
        $laserisasi = ArrayHelper::map($getCacheKeperawatan['laserisasi'], "lookupkeperawatan_id", "lookup_name");
        return [
            "laserisasi" => $laserisasi,
        ];
    }

    public function actionGetLookupTransaksi($kode_transaksi)
    {
        try {
            $result = LookupTransaksi::find()
                ->where(['kode_transaksi' => $kode_transaksi])
                ->asArray()->one();

            return [
                'data' => $result,
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

}
