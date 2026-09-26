<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\KetersediaanObatView;
use Yii;
use app\modules\v1\models\Bank;
use app\modules\v1\models\Barang;
use app\modules\v1\models\BarangView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\GelarbelakangM;
use app\modules\v1\models\GroupInaCbg;
use app\modules\v1\models\GroupMargin;
use app\modules\v1\models\Indexing;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\Jenisjabatan;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\KategoriTindakan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KelompokBarang;
use app\modules\v1\models\KelompokPegawai;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\Klasifikasipasien;
use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\KonfigantrianV;
use app\modules\v1\models\KonfigGudang;
use app\modules\v1\models\Layarantrian;
use app\modules\v1\models\Loket;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\MakananDiet;
use app\modules\v1\models\MenuDietMP;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\Operasi;
use app\modules\v1\models\Pangkat;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\PendidikanKualifikasi;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PerdaTarif;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ATCCode;
use app\modules\v1\models\ObatAlkesMIMS;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\SubKelompokBarang;
use app\modules\v1\models\Suku;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Manufaktur;
use app\modules\v1\models\WarnaTempatTidur;
use app\modules\v1\models\DashboardKamarView;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\Kabupaten;
use app\modules\v1\payload\MasterForm;
use yii\helpers\ArrayHelper;

// added by: Arief
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\TindakanKomponen;
use app\modules\v1\payload\PayloadForm;
use Doco\components\ConfigTrait;
use Doco\components\DocoAntrian;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\Traits\Select2Trait;

use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use app\modules\v1\models\RuteObat;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\ZatAktif;
use Doco\components\DocoConstansId;
use app\modules\v1\models\SupersetMapping;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;
    use Select2Trait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-konfigantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-layarantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-group-konfig"] = ["POST", "GET"];
        $verbs["get-manufaktur"] = ["GET"];
        $verbs["get-supplier"] = ["GET"];
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
        // return DocoHelpers::decrypt("bWFzdGVyLUNhcmEgTWFzdWsgQ29udHJvbGxlcg");
    }

    /**
     * @author Rizal
     * @since 2018-01-29 11:50:50
     * @param
     * @return array map list data dokter rajal
     * @desc
     */
    public function actionListDokterRajal()
    {
        $data = Pegawai::find()
            ->where(['is_active' => 't', 'kelompokpegawai_id' => 1])
            ->orderBy('nama_pegawai');
        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    }

    /**
     * @author Ardi
     * @since 2018-04-25 10:03:50
     * @param
     * @return array map list data dokter rajal by ruangan
     * @desc
     */
    public function actionListDokterRuangan($ruangan_id)
    {
        $data = Pegawai::find()
            ->where(['is_active' => 't', 'kelompokpegawai_id' => 1])
            ->andWhere("pegawai_id IN (SELECT pegawai_id FROM ruanganpegawai_mp WHERE ruangan_id = {$ruangan_id})")
            ->orderBy('nama_pegawai');
        $items = $data->all();

        return ['data' => $items];
    }

    /**
     * @author Rizal
     * @since 2018-03-02 10:31:25
     * @param ruangan_id
     * @param pegawai_id
     * @param hari_id
     * @return
     * @desc
     */
    public function actionListJadwalDokter($ruangan_id = null, $pegawai_id = null, $hari_id = null, $exception_id = null)
    {

        $wheres = [];
        $exceptions = [];
        if ($ruangan_id) {
            $wheres['ruangan_id'] = $ruangan_id;
        }
        if ($pegawai_id) {
            $wheres['pegawai_id'] = $pegawai_id;
        }
        if ($hari_id) {
            $wheres['jadwaldokter_hari'] = $hari_id;
        }
        $data = JadwalDokter::find()
            ->where($wheres);

        if ($exception_id) {
            $exceptions = ['!=', 'jadwaldokter_id', $exception_id];
            $data->andWhere($exceptions);
        }
        $list = $data->asArray()->all();

        return $list;
    }

    public function actionGetForPengambilanAntrian()
    {
        // get data statusperiksa via lookup
        $data_jenis_antrian = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('jenis_antrian'), true, 'jenis_antrian');
        $data_fungsi_antrian = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('fungsi_antrian'), true, 'fungsi_antrian');
        $data_carabayar = $this->getOrSetCache(DocoConstants::VAR_CACHE_GROUP_CARA_BAYAR, $this->getGroupCaraBayar(), true, 'carabayar');
        // $data_klasifikasi = $this->getOrSetCache(DocoConstants::VAR_CACHE_KLASIFIKASI_PASIEN, $this->getKlasifikasi(), true, 'klasifikasi');
        $data_cara_bayar = $this->getOrSetCache(DocoConstants::VAR_CACHE_CARA_BAYAR, $this->getCaraBayar(), true, 'carabayar_');

        return [
            'data_jenis_antrian' => $data_jenis_antrian,
            'data_fungsi_antrian' => $data_fungsi_antrian,
            'data_carabayar' => $data_carabayar,
            // 'data_klasifikasi' => $data_klasifikasi,
            'data_cara_bayar' => $data_cara_bayar,
        ];
    }

    public function actionGetForLoket()
    {
        // get data statusperiksa via lookup
        $data_jenis_antrian = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE,$this->getLookupByType('jenis_antrian'),true,'jenis_antrian');

        $dataRuangan = Ruangan::find()->where(['instalasi_id' => DocoConstants::INSTALASI_FARMASI]);
        $data_ruangan_farmasi = ArrayHelper::map($dataRuangan->all(), 'ruangan_id', 'ruangan_nama');

        return [
            'data_jenis_antrian' => $data_jenis_antrian,
            'data_ruangan_farmasi' => $data_ruangan_farmasi, //kebutuhan farmasi : ali.padilah@docotel.com
        ];
    }

    // public function actionGetJenisPengambilanAntrian()
    // {
    //     // get data statusperiksa via lookup
    //     $data_fungsi_antrian = $this->getOrSetCache(
    //             DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE,
    //             $this->getLookupByType('fungsi_antrian'),
    //             true,
    //             'fungsi_antrian');

    //     return [
    //             'data_fungsi_antrian' => $data_fungsi_antrian,
    //         ];
    // }

    /**
     * @author Arief Saputra
     * @see Fungsi get data konfigantrian_m
     * @return array, activeQueryRecords
     *
     */
    public function actionGetKonfigantrianByJenis($jenisantrian_id)
    {
        $data = KonfigantrianV::find()
            ->where(['jenisantrian_id' => $jenisantrian_id]);
        $items = ArrayHelper::map($data->all(),
            'konfigantrian_id',
            function ($model) {
                return (isset($model['kode_antrian']) ? $model['kode_antrian'] . '-' : '') . $model['fungsi_antrian'] . (isset($model['carabayar_nama']) ? '-' . $model['carabayar_nama'] : '');
            });

        return $items;
    }

    /**
     * @author unknow
     * @modify ali.padilah@docotel.com
     *
     */
    public function actionGetGroupKonfig($jenisantrian_id = null)
    {
        $model = new PayloadForm;
        $model->jenisantrian_id = $jenisantrian_id;
        if (!$model->validate()) {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }
        // $sql = "
        //     SELECT a.konfigantrian_id,a.kode_antrian,b.lookup_name FROM konfigantrian_m a
        //     INNER JOIN lookup_m b ON a.fungsiantrian_id = b.lookup_id
        // ";
        // if ($jenisantrian_id) {
        //     $sql .= " WHERE a.jenisantrian_id = {$jenisantrian_id}";
        // }
        // $query = Yii::$app->db->createCommand($sql)->queryAll();
        // $listData = $group = [];
        // foreach ($query as $value) {
        //     if (!isset($listData[$value['kode_antrian']])) {
        //         $listData[$value['kode_antrian']] = [];
        //     }
        //     $listData[$value['kode_antrian']][$value['konfigantrian_id']] = $value['lookup_name'];
        // }
        // return $listData;

        $model = new MasterForm;
        $model->jenisantrian_id = $jenisantrian_id;
        if($model->validate()) {
            $sql = "SELECT
                    konfigantrian_id,
                    kode_antrian,
                    concat(group_carabayar,' - ',klasifikasipasien_nama,' (',kode_antrian,')') as konfig_name
                FROM
                    konfigantrian_v";

            if ($jenisantrian_id) {
                $sql .= " WHERE jenisantrian_id = {$jenisantrian_id}";
            }

            $sql .= " order by group_carabayar,kode_antrian";

            $query = Yii::$app->db->createCommand($sql)->queryAll();

            return $query;
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors
            ];
        }
    }

    /**
     * @author Arief Saputra
     * @see Fungsi get data layarantrian_m
     * @return array, activeQueryRecords
     *
     */
    public function actionGetLayarantrianByJenis($jenisantrian_id = null)
    {
        $data = Layarantrian::find();
        if (!empty($jenisantrian_id)) {
            $data->where(['jenisantrian_id' => $jenisantrian_id]);
        }
        $items = ArrayHelper::map($data->all(),
            'layarantrian_id',
            function ($model) {
                return $model['layarantrian_nama'];
            });

        return $items;
    }

    public function actionGetJenisPengambilanAntrian($fungsiantrian_id = null)
    {
        if (!$fungsiantrian_id) {
            return [];
        }

        $data = Layarantrian::find()
            ->where(['fungsiantrian_id' => $fungsiantrian_id]);
        $items = ArrayHelper::map($data->all(),
            'layarantrian_id',
            function ($model) {
                return $model['layarantrian_nama'];
            });

        return $items;
    }

    /**
     * @author Arief Saputra
     * @see Fungsi get data konfigantrian_m
     * @return array, activeQueryRecords
     *
     */
    public function actionGetFungsiantrianByJenis($jenisantrian_id)
    {
        $data = Lookup::find()
            ->where(['lookup_kode' => $jenisantrian_id]);
        $items = ArrayHelper::map($data->all(),
            'lookup_id', 'lookup_name');

        return $items;
    }

    /**
     * @author Ardi Pratama
     * @see Fungsi get data konfigantrian_m
     * @return array, activeQueryRecords
     *
     */
    public function actionGetRuanganByFungsi($fungsiantrian_id)
    {
        $items = [];
        $dataFungsi = Lookup::find()->where(['lookup_id' => $fungsiantrian_id])->one();
        if (isset($dataFungsi->additional_data)) {
            $add_data = json_decode($dataFungsi->additional_data, true);
            if (isset($add_data['instalasi_id'])) {
                $instalasi_id = $add_data['instalasi_id'];
                $instalasi_id = (int) $instalasi_id;

                $dataRuangan = Ruangan::find()->where(['instalasi_id' => $instalasi_id]);
                $items = ArrayHelper::map($dataRuangan->all(), 'ruangan_id', 'ruangan_nama');
            }
        }

        return $items;
    }

    /**
     * @see Fungsi get data carabayar_m
     * @return array, activeQueryRecords
     *
     */
    private function getCaraBayar()
    {
        $result = Carabayar::find();
        $result->andWhere(['is_active' => true])
            ->orderBy(['carabayar_nama' => SORT_ASC]);

        return $result;
    }

    /**
     * @see Fungsi get data lookup carabayar
     * @return array, activeQueryRecords
     *
     */
    private function getGroupCaraBayar()
    {
        $result = Lookup::find()->where(['lookup_type' => 'group_carabayar']);
        $result->andWhere(['is_active' => true]);

        return $result;
    }

    /**
     * @see Fungsi get data klasifikasi_m
     * @return array, activeQueryRecords
     *
     */
    private function getKlasifikasi()
    {
        $result = Klasifikasipasien::find();
        $result->andWhere(['is_active' => true]);

        return $result;
    }

    /**
     * @author Rizal
     * @since 2018-03-27 11:31:47
     * @param
     * @return array list of instalasi
     * @desc
     */
    public function actionListInstalasi(array $instalasi_id = [])
    {
        try {
            $model = new Instalasi;
            $model = $model->find()
                ->andWhere(['is_pelayanan' => true]);
            if ($instalasi_id) {
                $model->andWhere(['instalasi_id' => $instalasi_id]);
            }
            $count = $model->count();
            $data = $model
                ->orderBy('instalasi_id')
                ->asArray()
                ->all();

            $results = [
                'data' => $data,
                'count' => $count,
            ];
            return $results;
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

    /**
     * @author Rizal
     * @since 2018-03-27 13:18:42
     * @param int instalasi_id
     * @return array list of ruangan
     * @desc needs for depdrop or dropdown
     */
    public function actionListRuangan($instalasi_id = null, $singkatan = null)
    {
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => 't']);
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }
        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }
        $data->orderBy('ruangan_m.ruangan_id');

        $results = [
            'data' => $data->all(),
            'count' => $data->count(),
        ];
        return $results;
    }

    /**
     * @author Arief
     * @since 2018-04-19 17:08:42
     * @return array list of pendidikan
     * @desc needs for dropdown
     */
    public function actionListPendidikan()
    {
        $data = Pendidikan::find()->where(['is_deleted' => 'f'])->orderBy(['pendidikan_urutan' => SORT_ASC]);
        $items = ArrayHelper::map($data->all(), 'pendidikan_id', 'pendidikan_nama');

        return $items;
    }

    /**
     * @author Arief
     * @since 2018-04-19 17:08:42
     * @return array list of group pegawai
     * @desc needs for dropdown
     */
    public function actionListKelompokpegawai()
    {
        $data = KelompokPegawai::find()->where(['is_deleted' => 'f'])->orderBy(['kelompokpegawai_nama' => SORT_ASC]);

        $items = ArrayHelper::map($data->all(), 'kelompokpegawai_id', 'kelompokpegawai_nama');

        return $items;
    }

    /**
     * @author Arief
     * @since 2018-04-20 16:18:42
     * @return array list of indexing
     * @desc needs for dropdown
     */
    public function actionListIndexing()
    {
        $data = Indexing::find()->where(['is_deleted' => 'f']);

        $items = ArrayHelper::map($data->all(), 'indexing_id', 'indexing_nama');

        return $items;
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => true]);
        }

        return $result;
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getDataLookup($type = null)
    {
        $result = $this->getLookupByType($type);

        return $result->all();
    }

    public function actionListRuanganPegawai($pegawai_id, $jadwaldokter_id = null)
    {
        try {
            $selectedData = '';
            if ($jadwaldokter_id) {
                $selectedData = JadwalDokter::findOne($jadwaldokter_id);
            }
            $query = "
                SELECT rp.ruangan_id,r.ruangan_nama
                FROM ruanganpegawai_mp rp
                LEFT JOIN ruangan_m r ON r.ruangan_id = rp.ruangan_id
                WHERE rp.pegawai_id = '{$pegawai_id}'
            ";
            $data = Yii::$app->db->createCommand($query)->queryAll();

            return ['data' => $data, 'selectedData' => $selectedData];
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

    public function actionListHariLookup()
    {
        try {
            $data = $this->getLookupByType('hari');
            $data->select(['hari_id' => 'lookup_id', 'hari_nama' => 'lookup_name']);
            $data->orderBy('lookup_id');
            return [
                'data' => $data->asArray()->all(),
            ];
        } catch (Exception $e) {
            var_dump($e->getMessage());exit;
        }
    }

    public function actionListWaktuPelayanan()
    {
        $type = 'waktu_pelayanan';
        $data = $this->getLookupByType($type);
        return $data->asArray()->all();
    }

    public function actionListHariRuangan($ruangan_id, $jadwaldokter_id = null)
    {
        try {
            $ruangan_id = (int) $ruangan_id;
            $selectedData = '';
            if ($jadwaldokter_id) {
                $selectedData = JadwalDokter::findOne($jadwaldokter_id);
            }
            $query = "
                SELECT
                jbp.jadwalbukapoli_id,
                hari.lookup_name as hari_nama,
                CONCAT(hari.lookup_name,' > ',to_char(jbp.jam_mulai,'HH24:MI'),'-',to_char(jbp.jam_tutup,'HH24:MI')) as hari_jam,
                to_char(jbp.jam_mulai,'HH24:MI') as jam_mulai,
                to_char(jbp.jam_tutup,'HH24:MI') as jam_tutup,
                hari.lookup_id as hari_id
                FROM jadwalbukapoli_m jbp
                LEFT JOIN lookup_m hari ON hari.lookup_id = jbp.hari AND hari.lookup_type = 'hari'
                WHERE jbp.ruangan_id = '{$ruangan_id}' AND jbp.is_deleted = FALSE AND jbp.is_active = TRUE
            ";
            $data = Yii::$app->db->createCommand($query)->queryAll();

            return ['data' => $data, 'selectedData' => $selectedData];
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

    public function actionListJadwalDokterTersedia($pegawai_id, $hari)
    {
        try {
            $query = "
                SELECT
                    jadwal.jadwaldokter_id,
                    hari_lookup.lookup_name as hari_nama,
                    to_char(jadwal.jadwaldokter_mulai,'HH24:SS')as jam_mulai,
                    to_char(jadwal.jadwaldokter_tutup,'HH24:SS')as jam_tutup
                FROM jadwaldokter_m jadwal
                JOIN jadwalbukapoli_m bukapoli ON jadwal.jadwalbukapoli_id = bukapoli.jadwalbukapoli_id
                JOIN lookup_m hari_lookup ON bukapoli.hari = hari_lookup.lookup_id
                WHERE
                    jadwal.pegawai_id = '{$pegawai_id}'
                    AND bukapoli.hari = '{$hari}'
                    AND jadwal.is_deleted = false
            ";
            $data = Yii::$app->db->createCommand($query)->queryAll();
            return $data;
        } catch (Exception $e) {
            // asd
        }
    }

    /**
     * @author Iqbal Qurahman
     * @since 2018-07-19 11:50:50
     * @param
     * @return array map informasi tarif pelayanan
     * @desc
     */
    public function actionListInfTarifPelayanan()
    {
        $request = Yii::$app->request;
        $arr = $request->post();

        $result = $this->getListInfTarifPelayanan($arr['ruangan_id']);
        return $result;
    }

    public function getListInfTarifPelayanan($ruangan_id)
    {
        $litPenjamin = $this->ListPenjaminByRuanganId($ruangan_id);
        $litDaftarTindakan = $this->ListDaftarTindakanByRuanganId($ruangan_id);
        $litKategoriTindakan = $this->ListKategoriTindakanByRuanganId($ruangan_id);
        $litKelasPelayanan = $this->ListKelasPelayananByRuanganId($ruangan_id);
        $result = ['penjamin' => $litPenjamin,
            'daftarTindakan' => $litDaftarTindakan,
            'kategoriTindakan' => $litKategoriTindakan,
            'kelasPelayanan' => $litKelasPelayanan,
        ];
        return $results = ['data' => $result];
    }

    public function ListPenjaminByRuanganId($ruangan_id = null)
    {
        $data = Penjamin::find();
        if ($ruangan_id) {
            $data->leftJoin('tariftindakan_m', 'tariftindakan_m.penjamin_id = penjamin_m.penjamin_id');
            $data->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id');
            $data->leftJoin('tindakanruangan_mp', 'tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id');
            $data->where(['tindakanruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('penjamin_nama');
        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');

        return $items;
    }

    public function ListKelasPelayananByRuanganId($ruangan_id = null)
    {
        $data = KelasPelayanan::find();
        if ($ruangan_id) {
            $data->leftJoin('tariftindakan_m', 'tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id');
            $data->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id');
            $data->leftJoin('tindakanruangan_mp', 'tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id');
            $data->where(['tindakanruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('kelaspelayanan_nama');
        $items = ArrayHelper::map($data->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    public function ListDaftarTindakanByRuanganId($ruangan_id = null)
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

    public function ListKategoriTindakanByRuanganId($ruangan_id = null)
    {
        $data = KategoriTindakan::find();
        if ($ruangan_id) {
            $data->leftJoin('daftartindakan_m', 'daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id');
            $data->leftJoin('tindakanruangan_mp', 'tindakanruangan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id');
            $data->where(['tindakanruangan_mp.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('kategoritindakan_nama');
        $items = ArrayHelper::map($data->all(), 'kategoritindakan_id', 'kategoritindakan_nama');

        return $items;
    }

    public function actionListPenjamin($carabayar_id = null)
    {
        $data = Penjamin::find();
        if ($carabayar_id) {
            $data->where(
                [
                    'is_active' => 't',
                    'carabayar_id' => $carabayar_id,
                ]
            );
        }
        $data->orderBy('penjamin_nama');

        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');
        return $items;
    }

    public function actionViewJadwalDokter($id)
    {
        return JadwalDokter::find()->where(['jadwaldokter_id' => $id])->asArray()->one();
    }

    public function actionGetLookup($type)
    {
        $data = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType($type), true, $type);
        return $data;
    }

    public function actionLoopAksi()
    {
        $request = Yii::$app->request;
        $arr = $request->post();
        $data = [];
        try {
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = (count($value) > 1)
                    ? $this->{$value[0]}($value[1])
                    : $this->{$value[0]}();
                } else {
                    // perubahan get value name controller : ali.padilah@docotel.com
                    // $data[$key] = $this->$value();
                    $data[$key] = $this->{$value}();
                }
            }

        } catch (\Exception $e) {

            $data[] = $e->getMessage();
        }

        return $data;
    }

    public function actionListJenisObat()
    {
        // $data = JenisObatAlkes::find()->asArray()->all();
        $data = $this->getOrSetCache(DocoConstants::VAR_J_OA, JenisObatAlkes::find(), true);
        return $data;
    }
    public function actionListKelompokBarang()
    {
        $data = $this->getKelompokBarang();
        return $data;
    }
    public function actionListSubKelompokBarang()
    {
        $data = $this->getSubKelompokBarang();
        return $data;
    }
    public function getKelompokBarang()
    {
        $data = KelompokBarang::find()->where(['is_active' => true])->all();

        return $data;
    }
    public function getSubKelompokBarang()
    {
        $data = SubKelompokBarang::find()->where(['is_active' => true])->all();

        return $data;
    }

    public function actionListSatuan( /*$type = 0*/)
    {
        // $key = 'kecil';
        // if($type == '0'){
        //     $key = 'kecil';
        // } else if($type == '1'){
        //     $key = 'sedang';
        // } else if($type == '2'){
        //     $key = 'besar';
        // }
        // return $this->getOrSetCache(DocoConstants::VAR_S_KBS, $this->getSatuan(), true);
        return $this->getSatuan();
    }
    public function actionListInaCbg()
    {
        return $this->getInaCbg();
    }

    public function getInaCbg()
    {
        $data = GroupInaCbg::find()->where(['is_active' => true, 'is_obat' => true])->all();

        return $data;
    }

    public function getSatuan()
    {
        $data = SatuanUnit::find()->where(['is_active' => true])->all();

        return $data;
    }

    public function actionGetListZatAktif()
    {
        $model = ZatAktif::find()
        ->select([
            'zataktif_id',
            'zataktif_nama',
        ])
        ->where(['is_active' => true])
        ->asArray()
        ->all();

    return $model;
    }
    
    public function actionGetLastKodeOa()
    {
        try {
            $sql = "SELECT obatalkes_id as last_number from obatalkes_m order by obatalkes_id desc limit 1";
            $query = Yii::$app->db->createCommand($sql)->queryOne();
            $nomor = 'A001';
            if (isset($query['last_number'])) {
                $last_number = $query['last_number'] + 1;
                $prefix = 'A';
                if (strlen($last_number) == 1) {
                    $last_number = '00' . $last_number;
                } else if (strlen($last_number) == 2) {
                    $last_number = '0' . $last_number;
                } else if (strlen($last_number) == 3) {
                    $last_number = $last_number;
                }
                $nomor = $prefix . $last_number;
            }
            $query = $nomor;
        } catch (\yii\db\Exception $e) {
            $query = '';
        }
        return $query;
    }

    public function actionGetKonfigGudang()
    {
        try {
            $query = $this->getOrSetCache(DocoConstants::VAR_K_G, KonfigGudang::find(), false);
        } catch (\yii\db\Exception $e) {
            $query = [];
        }
        return $query;

    }

    public function getKelasPelayanan()
    {
        $data = KelasPelayanan::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kelaspelayanan_nama' => SORT_ASC]);

        return $result;
    }

    public function getPerdaTarif($status = false)
    {
        $query = PerdaTarif::find();
        if ($status) {
            $query->where(['is_active' => true, 'is_deleted' => false]);
        }
        $result = $query->orderBy([
            'is_active' => true,
            'perdanama_sk' => SORT_ASC,
        ]);

        return $result;
    }

    public function getDataKomponen()
    {
        try {
            $query = KomponenTarif::find();
            $query->where(['is_active' => true, 'is_deleted' => false]);
            $result = $query;
            return $result;

        } catch (Exception $e) {
            return [];
        }
    }

    public function actionPackTarif()
    {
        $carabayar = $this->getOrSetCache(DocoConstants::VAR_CACHE_CARA_BAYAR, $this->getCaraBayar(), true, 'carabayar_');
        $kelaspelayanan = $this->getOrSetCache(DocoConstants::VAR_C_KP, $this->getKelasPelayanan(), true);
        $perdatarif = $this->getOrSetCache('cache_perda_tarif', $this->getPerdaTarif(), true);
        $komponen = $this->getOrSetCache('cache_kompononen', $this->getDataKomponen(), true);
        $kamar = $this->getOrSetCache('cache_kamar', $this->getKamar(), true);
        return [
            'carabayar' => $carabayar,
            'kelaspelayanan' => $kelaspelayanan,
            'perdatarif' => $perdatarif,
            'komponen' => $komponen,
            'kamar' => $kamar
        ];
    }
    public function actionListCarabayar($penjamin_id = null)
    {
        $data = Penjamin::find()->with(
            [
                'caraBayar' => function ($model) {
                    $model->select(['carabayar_nama', 'carabayar_id']);
                },
            ]
        );
        if ($penjamin_id) {
            $data->where(
                [
                    'penjamin_m.is_active' => 't',
                    'penjamin_m.penjamin_id' => $penjamin_id,
                ]
            );
        }
        $data->orderBy('penjamin_nama');
        // $result = $data->asArray()->all();
        // $items = ArrayHelper::map($result, 'carabayar.carabayar_id', 'carabayar.carabayar_nama');
        return $data->asArray()->all();
    }

    public function actionListTindakanOperasi()
    {
        $model = new Operasi;
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $query = $model->find();
        $query->where(['is_active' => true]);
        if ($term) {
            $query->andWhere(['ILIKE', 'operasi_nama', $term]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    // get list tindaakan
    public function actionListTindakan()
    {
        $model = new DaftarTindakan;
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $query = $model->find();
        $query->where(['is_active' => true]);
        if ($term) {
            $query->andWhere(['ILIKE', 'daftartindakan_nama', $term]);
            $query->andWhere(['is_deleted' => false]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /*
     * @see Fungsi get data tindakan infinity scroll
     * @return array, activeQueryRecords
     *
     */
    public function actionListTindakanInfinity()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);

        $model = new DaftarTindakan();
        $query = $model::find();
        if($term){
            $query->where(['ILIKE','LOWER(daftartindakan_nama)',$term]);
            $query->orWhere(['ILIKE','LOWER(daftartindakan_kode)',$term]);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    // get list obat alkes
    public function actionListObatAlkes()
    {
        $model = new ObatAlkes;
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $query = $model->find();
        $query->where(['is_active' => true]);
        if ($term) {
            $query->andWhere(['ILIKE', 'obatalkes_nama', $term]);
            $query->andWhere(['is_deleted' => false]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListObatAlkesInfinity()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);

        $model = new ObatAlkes();
        $query = $model::find();
        $query->leftJoin('jenisobatalkes_m', 'jenisobatalkes_m.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id');
         
        $group = $params->get('group',null);
        if(!empty($group)){
            $query->andWhere(['jenisobatalkes_m.group_jenisobat' => $group]);
        }
        if($term){
            $query->andWhere(['or', 
                ['ILIKE','LOWER(obatalkes_m.obatalkes_nama)',$term],
                ['ILIKE','LOWER(obatalkes_m.obatalkes_namalain)',$term],
                ['ILIKE','LOWER(obatalkes_m.obatalkes_kode)',$term]
            ]);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    /**
     * @todo Fungsi untuk mendapatkan list satuan besar dari masing2 obat
     * @author Wahyu Saepuloh
     */
    public function actionListSatuanBesar() {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $data = SatuanKonversiView::find()
                    ->where([
                        'obatalkes_id' => $obatalkes_id,
                        'is_active' => true
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

    // get nilai konversi untuk masing2 satuan yang dipilih
    public function actionGetKonversi() {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $satuanbesar_id = $request->get('satuanbesar_id', null);
            $data = SatuanKonversiView::find()
                ->where([
                    'obatalkes_id' => $obatalkes_id,
                    'satuanbesar_id' => $satuanbesar_id
                ]);

            $items = $data->asArray()->one();

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

    public function actionGetDataObatBmhp()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if (!empty($get['term'])) {
            $result = $this->getObatBmhp();
            $term = $get['term'];
            $result->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        }
    }

    public function actionGetDataObatAlkes($limit = 10)
    {
            $request = Yii::$app->request;
            $get = $request->get();
            $tipe = isset($get['tipe']) ? $get['tipe'] : null;
            $is_consignment = $request->get('is_consignment',null);
            if (!empty($get['tipe'])) {
                if (!is_null($tipe)) {
                    if ($tipe == 0) {
                        $query = InfoObatAlkesView::find();
                    } elseif ($tipe == 3) {
                        $query = ObatAlkesView::find();
                    } else {
                        $query = InfoStokObatAlkesView::find()
                            ->where(['ruangan_id' => Yii::$app->jwt->ruangan_id]);
                    }
                } else {
                    $query = ObatAlkes::find()->where(['is_active' => true]);
                }
            } else {
                $query = InfoObatAlkesView::find();
            }

            $term = $get['term'];
            if(!empty($is_consignment)){
                $query->where(['is_consigment' => $is_consignment]);
            }
            $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($term)]);
            if ($limit) {
                $query->limit($limit);
            }
            return $query->asArray()->all();
    }

    public function actionGetDataBarangAdjustment()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tipe = isset($get['tipe']) ? $get['tipe'] : null;

        if ($tipe == 0) {
            $query = BarangView::find();
        } else {
            $query = InfoStokBarang::find()
                ->where(['ruangan_id' => Yii::$app->jwt->ruangan_id]);
        }

        $term = $get['term'];
        $query->andWhere(['ILIKE', 'LOWER(barang_nama)', strtolower($term)]);
        return $query->limit(10)->asArray()->all();

    }

    public function actionGetDataBarang()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = Barang::find();
        $term = $get['term'];

        $query->andWhere(['like', 'LOWER(barang_nama)', strtolower($term)]);
        return $query->limit(10)->asArray()->all();

    }

    public function actionGetDataBarangCustom()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = BarangView::find();
        $term = $get['term'];

        $query->andWhere(['like', 'LOWER(barang_nama)', strtolower($term)]);
        return $query->limit(10)->asArray()->all();

    }

    public function actionGetDataTindakan()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $query = DaftarTindakan::find();
            $term = $get['term'];
            $query->andWhere(['like', 'LOWER(daftartindakan_nama)', strtolower($term)]);

            return $query->limit(10)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getObatBmhp()
    {
        $model = ObatAlkes::find()->joinWith(['satuankecil'])
            ->where(['obatalkes_m.is_active' => true]);

        return $model;
    }

    public function getSatuanKonversi($column, $parent_label)
    {
        $model = SatuanKonversi::find()
            ->select(['satuankonversi_m.satuankonversi_id', 'satuankonversi_m.nilai_konversi',
                'kecil.satuanunit_nama as kecil', 'besar.satuanunit_nama as besar',
                'satuankonversi_m.satuankecil_id', 'satuankonversi_m.satuanbesar_id'])
            ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = satuankonversi_m.satuankecil_id')
            ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = satuankonversi_m.satuanbesar_id')
            ->where([
                'satuankonversi_m.is_active' => true,
                'satuankonversi_m.' . $column => $parent_label,
                'kecil.is_deleted' => false,
                'besar.is_deleted' => false,
            ]);

        return $model;
    }

    public function getSatuanKonversiBarangAdjustment($column, $parent_label)
    {
        $model = SatuanKonversiBarang::find()
            ->select(['satuankonversibrg_m.satuankonversibrg_id', 'satuankonversibrg_m.nilai_konversi',
                'kecil.satuanunit_nama as kecil', 'besar.satuanunit_nama as besar',
                'satuankonversibrg_m.satuankecil_id', 'satuankonversibrg_m.satuanbesar_id'])
            ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = satuankonversibrg_m.satuankecil_id')
            ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = satuankonversibrg_m.satuanbesar_id')
            ->where([
                'satuankonversibrg_m.is_active' => true,
                'satuankonversibrg_m.' . $column => $parent_label,
                'kecil.is_deleted' => false,
                'besar.is_deleted' => false,
            ]);

        return $model;
    }

    public function getDataSatuan()
    {
        $model = SatuanUnit::find()
            ->where(['is_active' => true])
            ->orderBy('satuanunit_nama');

        return $model;
    }

    public function getPegawai()
    {
        $model = Pegawai::find()
            ->where(['is_active' => true]);

        return $model;
    }

    private function getPegawaiView()
    {
        $model = PegawaiView::find()->where(['is_active' => true]);
        return $model;
    }

    public function getPegawai2()
    {
        $model = PegawaiView::find()
            ->where(['ruangan_id' => 38]);

        return $model;
    }

    public function actionGetListCaraBayar()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = CaraBayar::find()->where(['is_active' => true]);
        if (!empty($get['term'])) {
            $term = $get['term'];
            $query->andWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($term)]);
            return $query->limit(10)->asArray()->all();
        } else {
            return $query->asArray()->all();
        }
    }

    public function actionGetListCaraBayarPenjamin()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = CaraBayar::find()
            ->where(['is_active' => true])
            ->andWhere(['<>', 'carabayar_id', DocoConstants::PENJAMIN_UMUM]);

        if (!empty($get['term'])) {
            $term = $get['term'];
            $query->andWhere(['ILIKE', 'LOWER(carabayar_nama)', strtolower($term)]);
            return $query->limit(10)->asArray()->all();
        } else {
            return $query->asArray()->all();
        }
    }

    public function actionGetListSupplier()
    {
        $model = Supplier::find()
            ->select([
                'supplier_id',
                'supplier_nama',
            ])
            ->where(['is_active' => true])
            ->asArray()
            ->all();

        return $model;
    }

    public function getSupplier()
    {
        $model = Supplier::find()
            ->select([
                'supplier_id',
                'supplier_nama',
            ])
            ->where(['is_active' => true]);

        return $model;
    }

    public function getObat()
    {
        $model = ObatAlkes::find()
            ->where(['is_active' => true]);

        return $model;
    }

    public function actionGetDataManufaktur()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');

        $model = Manufaktur::find()->where(['ILIKE', 'nama', $q]);
        
        return $model->asArray()->all();
    }

    public function getBarang()
    {
        $model = Barang::find()
            ->where(['barang_m.is_active' => true]);

        return $model;
    }

    public function actionGetListPenjamin()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = isset($get['id']) ? $get['id'] : '';
        if (isset($get['id'])) {
            $carabayar_id = $get['id'];
        } elseif (isset($get['parent_label'])) {
            $carabayar_id = $get['parent_label'];
        }

        $result = Penjamin::find()
            ->where([
                'is_active' => true,
                'is_deleted' => false, 
                'carabayar_id' => $carabayar_id])
            ->orderBy(['penjamin_nama' => SORT_ASC]);

        return $result->asArray()->all();
    }

    public function actionListSatuanKonversi()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $parent_label = $get['parent_label'];
        $column = $get['column'];

        $result = $this->getSatuanKonversi($column, $parent_label);
        return $result->limit(10)->asArray()->all();
    }

    public function actionListSatuanKonversiBarangAdjustment()
    {
        $request = Yii::$app->request;
        $payload = $request->get();
        if (isset($payload['groupByBarang']) && !empty($payload['groupByBarang'])) {
            return $this->getSatuanKonversiBarangAdjustment('barang_id', $payload['groupByBarang'])->asArray()->all();
        } else {
            $parent_label = $payload['parent_label'];
            $column = $payload['column'];
            return $this->getSatuanKonversiBarangAdjustment($column, $parent_label)->limit(10)->asArray()->all();
        }
    }

    public function actionListSatuanKonversiItem()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $item_id = $get['item_id'];
        $tipe = $get['tipe'];
        $result = ($tipe == 'B') ? $this->getSatuanKonversiBarang($item_id) : $this->getSatuanKonversiObat($item_id);
        return $result->limit(10)->asArray()->all();
    }

    public function getSatuanKonversiObat($item_id)
    {
        $model = SatuanKonversi::find()
            ->select(['satuankonversi_m.satuankonversi_id', 'satuankonversi_m.nilai_konversi',
                'kecil.satuanunit_nama as kecil', 'besar.satuanunit_nama as besar',
                'satuankonversi_m.satuankecil_id', 'satuankonversi_m.satuanbesar_id'])
            ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = satuankonversi_m.satuankecil_id')
            ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = satuankonversi_m.satuanbesar_id')
            ->where([
                'satuankonversi_m.is_active' => true,
                'satuankonversi_m.obatalkes_id' => $item_id,
                'kecil.is_deleted' => false,
                'besar.is_deleted' => false,
            ]);

        return $model;
    }

    public function getSatuanKonversiBarang($item_id)
    {
        $model = SatuanKonversiBarang::find()
            ->select(['satuankonversibrg_m.satuankonversibrg_id', 'satuankonversibrg_m.nilai_konversi',
                'kecil.satuanunit_nama as kecil', 'besar.satuanunit_nama as besar',
                'satuankonversibrg_m.satuankecil_id', 'satuankonversibrg_m.satuanbesar_id'])
            ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = satuankonversibrg_m.satuankecil_id')
            ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = satuankonversibrg_m.satuanbesar_id')
            ->where([
                'satuankonversibrg_m.is_active' => true,
                'satuankonversibrg_m.barang_id' => $item_id,
                'kecil.is_deleted' => false,
                'besar.is_deleted' => false,
            ]);

        return $model;
    }

    public function actionListSubKelompok()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $parent_label = $get['parent_label'];
        $result = SubKelompokBarang::find()
            ->where(['is_active' => true, 'kelompokbarang_id' => $parent_label]);

        return $result->limit(10)->asArray()->all();
    }

    public function actionListSatuanMasuk()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if (!empty($get['term'])) {
            $result = $this->getDataSatuan();
            $term = $get['term'];
            $result->andWhere(['ILIKE', 'LOWER(satuanunit_nama)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        }
    }

    public function actionListPegawai()
    {
        $request = Yii::$app->request;
        $payload = $request->get();
        $limit = isset($payload['limit']) ? $payload['limit'] : 11;
        if (isset($payload['page']) && !empty($payload['page']) && is_int((int) $payload['page'])) {

            $result = $this->getPegawai()->select([
                'pegawai_id AS id',
                'nama_pegawai as text',
                'pegawai_id',
                'nama_pegawai',
                'nomorindukpegawai'
            ]);

            if (isset($payload['term']) && !empty($payload['page'])) {
                $result->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($payload['term'])]);
            }

            $result->orderBy('nama_pegawai ASC');
            return $result->offset(($payload['page'] - 1) * 10)->limit($limit)->asArray()->all();
        } else if (!empty($payload['term'])) {
            $result = $this->getPegawai();
            $term = $payload['term'];
            $result->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        } else {
            return [];
        }
    }

    public function actionListPegawai2()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if (!empty($get['pegawai_id'])) {
            $result = $this->getPegawai2();
            $pegawai_id = $get['pegawai_id'];
            $result->andWhere(['pegawai_id' => $pegawai_id]);
            return $result->one();
        }
    }

    public function actionListSupplier()
    {
        $request = Yii::$app->request;
        $payload = $request->get();
        if (isset($payload['page']) && !empty($payload['page']) && is_int((int) $payload['page'])) {
            $result = $this->getSupplier();
            if (isset($payload['term']) && !empty($payload['page'])) {
                $result->andWhere(['ILIKE', 'LOWER(supplier_nama)', strtolower($payload['term'])]);
            }
            return $result->offset(($payload['page'] - 1) * 10)->limit(11)->asArray()->all();
        } else if (!empty($payload['term'])) {
            $result = $this->getSupplier();
            $result->andWhere(['ILIKE', 'LOWER(supplier_nama)', strtolower($payload['term'])]);
            return $result->limit(10)->asArray()->all();
        } else {
            return [];
        }
    }

    public function actionListObat()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if (!empty($get['term'])) {
            $result = $this->getObat();
            $term = $get['term'];
            $result->andWhere(['ILIKE', 'LOWER(obatalkes_nama)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        }
    }

    public function actionListBarang()
    {
        $request = Yii::$app->request;
        $payload = $request->get();

        if (isset($payload['page']) && !empty($payload['page']) && is_int((int) $payload['page'])) {
            $result = $this->getBarang()->select([
                'barang_m.barang_id as id',
                'barang_m.barang_nama as text',
            ]);
            if (isset($payload['term']) && !empty($payload['page'])) {
                $result->andWhere(['ILIKE', 'LOWER(barang_nama)', strtolower($payload['term'])]);
            }
            $result->andWhere([
                'exists', (new \yii\db\Query())
                ->select(['satuankonversibrg_id'])
                ->from('satuankonversibrg_m')
                ->where('satuankonversibrg_m.barang_id = barang_m.barang_id')
                ->andWhere([
                    'satuankonversibrg_m.is_active' => true
                ])
                ->limit(1)
            ]);
            return $result->offset(($payload['page'] - 1) * 10)->limit(11)->asArray()->all();
        } else if (!empty($payload['term'])) {
            $result = $this->getBarang();
            $term = $payload['term'];
            $result->andWhere(['ILIKE', 'LOWER(barang_nama)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        } else {
            return [];
        }
    }

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key => $className) {
            $class = "app\modules\\v1\models\\" . $className;
            $model = new $class;
            $q = $model->find();
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
        }
        return $results;
    }

    /**
     * @author Iqbal
     * @since 2018-09-04 11:06:02
     * @param
     * @return array $results :
     * @desc
     */
    public function actionGetListJenisKamar()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new Lookup;
            $res = $model->find()
                ->where(['lookup_type' => $get['type'], 'is_active' => true, 'is_deleted' => false])
                ->asArray()->all();
            return $res;

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

    /**
     * @author Rizqi Febian
     * @since 2018-04-03 13:40:02
     * @param
     * @return array $results :
     * @desc
     */
    public function listLookup($types)
    {
        $results = [];
        $res = [];
        foreach ($types as $key => $type) {
            $lookup = new Lookup;
            $q = $lookup->find()->where(['lookup_type' => $type, 'is_active' => true, 'is_deleted' => false]);
            $results[$type] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $q, true, $type);
        }
        return $results;
    }

    /**
     * @author Rizal
     * @since 2018-03-14 11:06:02
     * @param
     * @return array $results :
     * @desc
     */
    public function actionListLookupTypes()
    {
        $types = Yii::$app->request->post();
        $results = $this->listLookup($types);
        return $results;
    }

    /**
     * @todo get all data for pendaftaran frontend
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetApi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan = $cara_bayar = [];
        $result = [];
        try {
            // get all lookup by request
            $listRequestLookup = [
                'jenis_kamar',
                'gelar_depan',
                'gelar_belakang',
                'jenis_kelamin',
                'status_perkawinan',
                'agama',
                'golongan_darah',
                'warga_negara',
                'suku',
                'provinsi',
                'pendidikan',
                'kualifikasi_pendidikan',
                'jabatan',
                'pangkat',
                'pangkat',
                'warna_kulit',
                'kelompok_pegawai',
                'kategori_pegawai',
                'nama_bank',
                'bahasa',
                'jenisrs_profilrs',
                'kelas_rumahsakit',
            ];

            $lookup = $this->listLookup($listRequestLookup);

            // gelarbelakang
            $gelar_belakang = GelarbelakangM::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($gelar_belakang)) {
                $lookup['gelar_belakang'] = $gelar_belakang;
            }

            // suku
            $suku = Suku::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($suku)) {
                $lookup['suku'] = $suku;
            }
            // Propinsi
            $provinsi = Propinsi::findAll(['is_active' => true, 'is_deleted' => false]);

            if (!empty($provinsi)) {
                $lookup['provinsi'] = $provinsi;
            }

            // Pendidikan
            $pendidikan = Pendidikan::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($pendidikan)) {
                $lookup['pendidikan'] = $pendidikan;
            }

            // Pendidikankualifikasi
            $pendidikanKualifikasi = PendidikanKualifikasi::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($pendidikanKualifikasi)) {
                $lookup['kualifikasi_pendidikan'] = $pendidikanKualifikasi;
            }

            // Jenisjabatan
            $Jenisjabatan = Jenisjabatan::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($Jenisjabatan)) {
                $lookup['jabatan'] = $Jenisjabatan;
            }

            // Jabatan
            $jabatan = Jabatan::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($jabatan)) {
                $lookup["jabatan_m"] = $jabatan;
            }

            // Pangkat
            $pangkat = Pangkat::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($pangkat)) {
                $lookup['pangkat'] = $pangkat;
            }

            // KelompokPegawai
            $KelompokPegawai = KelompokPegawai::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($KelompokPegawai)) {
                $lookup['kelompok_pegawai'] = $KelompokPegawai;
            }

            // Bank
            $bank = Bank::findAll(['is_active' => true, 'is_deleted' => false]);
            if (!empty($bank)) {
                $lookup['nama_bank'] = $bank;
            }

            $result = array(
                "lookup" => $lookup,
            );

            return $result;
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

    // set global get loket by jenis antrian : ali.padilah@docotel.com
    public function actionGetLoket($jenisantrian_id = null,$ruangan_id=null)
    {
        try {
            $loket = new Loket;
            // $fungsiAntrian = DocoConstants::LOOKUP_TYPE_FUNGSI_ANTRIAN;
            // $defaultPendaftaran = DocoConstants::LOOKUP_NAME_DEFAULT_PENDAFTARAN;
            $post = Yii::$app->request->post();

            $ja_dftr = DocoConstants::VAR_JA_PD;
            $sql = "  
                SELECT
                    t.loket_id,
                    t.loket_nama,
                    t.loket_namalain,
                    x.loginpemakai_id,
                    x.nama_pemakai,
                    t.jenisantrian_id
                FROM loket_m t
                    RIGHT JOIN loket_mp loket_mp ON t.loket_id = loket_mp.loket_id AND loket_mp.is_deleted = false AND loket_mp.is_active = true
                    LEFT JOIN loginpemakai_k x ON x.loginpemakai_id = t.loginpemakai_id
                WHERE 1=1
                -- loginpemakai_id IS NULL
                AND t.is_deleted = false
                AND t.is_active = true
                AND t.ruangan_id = {$ruangan_id}
                AND t.jenisantrian_id = {$jenisantrian_id}
                GROUP BY t.loket_id, x.loginpemakai_id
                ORDER BY t.loket_nama
                ";
            // return $sql;
            $result = Yii::$app->db->createCommand($sql)->queryAll();
            // $result = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOKET_PENDAFTARAN, $query, true);
            return $result;
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
    // end set global get loket by jenis antrian

    /**
     * @author Rizal
     * @since 2018-05-03 15:48:54
     * @param loginpemakai_id
     * @return
     * @desc
     */
    public function actionUnsetLoket($loginpemakai_id)
    {
        try {
            return Loket::updateAll(['loginpemakai_id' => null], "loginpemakai_id = {$loginpemakai_id}");
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

    /**
     * @author Iqbal Qurahman
     * @since 2018-09-19 11:08:54
     * @param
     * @return
     * @desc Get Data Dashboard Ranap Kamar
     */
    public function actionGetDashboardKamarRanap()
    {

        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $kelas_pelayanan = $post['kelas_pelayanan'];
            $ruangan = $post['ruangan'];

            $getRuangan = $this->GetRuanganAll();
            $getWarnaBed = $this->WarnaTempatTidur();
            $getDashboardKamarView = $this->DashboardKamar($kelas_pelayanan, $ruangan);
            $getKelasPelayanan = $this->getKelasPelayanan()->asArray()->all();
            $getDashboardKamarViewFilter = $this->DashboardKamar(0, 0);
            $resKelas = [];
            $resRuangan = [];
            foreach ($getDashboardKamarViewFilter as $keyfilter => $valfilter) {
                $resKelas[$valfilter['kelaspelayanan_id']] = $valfilter['kelaspelayanan_nama'];
                $resRuangan[$valfilter['ruangan_id']] = $valfilter['ruangan_nama'];
            }

            $resDashboardKelas = [];
            $getKelasPelayananHeader = [];
            foreach ($getDashboardKamarView as $key => $val) {
                $resDashboardKelas[$val['kelaspelayanan_nama']][] = $val;
                $getKelasPelayananHeader[$val['kelaspelayanan_id']] = $val['kelaspelayanan_nama'];
            }

            $resultDashboardRuangan = [];
            foreach ($resDashboardKelas as $keys => $valKelas) {
                $arrRuangan = [];
                $tota = [];
                foreach ($valKelas as $keyz => $valdetail) {
                    $arrRuangan[$valdetail['kelaspelayanan_nama']][] = $valdetail;
                }
                $resultKamarDashboard = [];
                foreach ($arrRuangan as $keyr => $valruangan) {
                    $resKamar = [];
                    foreach ($valruangan as $keyh => $valkamar) {
                        $resKamar[$valkamar['kamarruangan_nokamar']][] = $valkamar;
                    }
                    $resultKamar = [];
                    $arrKamar = [];
                    foreach ($resKamar as $keynk => $valnk) {
                        $arrNoKamar = [];
                        $total_isi = 0;
                        $total_kosong = 0;
                        $total = 0;
                        foreach ($valnk as $keynkd => $valnkdetail) {
                            $total += 1;
                            if ($valnkdetail['is_dashboard']) {
                                if ($valnkdetail['kamarruangan_nokamar'] != '') {
                                    $arrNoKamar[$valnkdetail['kelaspelayanan_nama'] . ' - ' . $valnkdetail['kamarruangan_nokamar']][] = $valnkdetail;
                                } else {
                                    $arrNoKamar[$valnkdetail['kelaspelayanan_nama']][] = $valnkdetail;
                                }

                                if ($valnkdetail['status_isi'] == true) {
                                    $total_isi += $valnkdetail['status_isi'];
                                }

                                if ($valnkdetail['status_isi'] == false) {
                                    $total_kosong += $valnkdetail['kamarruangan_nokamar'];
                                }

                            }
                        }

                        $totals_kosong = $total - $total_isi;
                        $arrKamar[] = [
                            'kamar' => $arrNoKamar,
                            'total' => $total,
                            'total_isi' => $total_isi,
                            'total_kosong' => $totals_kosong,
                        ];
                    }
                    $resultKamarDashboard[] = $arrKamar;
                }
                $resultDashboardRuangan[$keys] = $resultKamarDashboard;
            }

            return ['dashboard_kamar' => $resultDashboardRuangan,
                'warna_tempat_tidur' => $getWarnaBed,
                'kelas_pelayanan' => $getKelasPelayanan,
                'kelas_pelayanan_header' => $getKelasPelayananHeader,
                'kelas_filter' => $resKelas,
                'ruangan_filter' => $resRuangan,
            ];

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    private function GetRuanganAll()
    {
        $model = new Ruangan;
        $query = $model->find()
            ->where(['is_active' => true, 'is_deleted' => false, 'is_modul' => true])
            ->orderBy(['ruangan_nama' => SORT_ASC]);
        $result = $query->asArray()->all();

        return $result;
    }

    private function WarnaTempatTidur()
    {
        $model = new WarnaTempatTidur;
        $query = $model->find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kamarruangan_jenis' => SORT_ASC]);
        $result = $query->asArray()->all();

        return $result;
    }

    private function DashboardKamar($kelas_pelayanan, $ruangan)
    {

        $model = new DashboardKamarView;
        $query = $model->find();
        $query->orderBy(['kelaspelayanan_nama' => SORT_DESC,
            'kamarruangan_nokamar' => SORT_ASC,
            'no_tempattidur' => SORT_ASC]);
        if (!empty($kelas_pelayanan)) {
            $query->andWhere(['kelaspelayanan_id' => $kelas_pelayanan]);
        }

        if (!empty($ruangan)) {
            $query->andWhere(['ruangan_id' => $ruangan]);
        }

        $result = $query->asArray()->all();

        return $result;
    }

    /**
     * @author Ardi Pratama
     * @since 2018-09-12 11:51:00
     * @return
     * @desc
    Cron Menambah tindakan akomodasi kamar
     */
    public function actionCreateAkomodasiKamar()
    {
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d');
        $hari = $tanggal['hari'];
        $transaction = $connection->beginTransaction();
        try {
            $queryAmbilTarifKamar = "
                SELECT
                    masukkamar_t.masukkamar_id,
                    masukkamar_t.pasienadmisi_id,
                    masukkamar_t.penjamin_id,
                    masukkamar_t.kelaspelayanan_id,
                    masukkamar_t.ruangan_id,
                    masukkamar_t.carabayar_id,
                    pasienadmisi_t.pendaftaran_id,
                    pasienadmisi_t.pasien_id,
                    pasienadmisi_t.pegawai_id,
                    pendaftaran_t.jeniskasuspenyakit_id,
                    pendaftaran_t.instalasi_id,
                    infotarifrs_v.perdatarif_id,
                    infotarifrs_v.kategoritindakan_id,
                    infotarifrs_v.daftartindakan_id,
                    infotarifrs_v.tipepaket_id,
                    infotarifrs_v.komponentarif_id,
                    infotarifrs_v.harga_tariftindakan,
                    infotarifrs_v.persencyto_tindakan,
                    infotarifrs_v.persendiskon_tindakan
                FROM masukkamar_t
                JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.pasienpulang_id IS NOT NULL
                LEFT JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN infotarifrs_v ON infotarifrs_v.penjamin_id = masukkamar_t.penjamin_id AND infotarifrs_v.kelaspelayanan_id = masukkamar_t.kelaspelayanan_id AND infotarifrs_v.ruangan_id = masukkamar_t.ruangan_id AND infotarifrs_v.kelompoktindakan_id = 16
                WHERE masukkamar_t.pindahkamar_id IS NULL AND masukkamar_t.tgl_last_akomodasi < CURRENT_DATE
            ";
            $dataTarifRs = $connection->createCommand($queryAmbilTarifKamar)->queryAll();
            if (count($dataTarifRs) == 0) {
                return ['message' => 'Tidak ada data yang diupdate'];
            }
            $data_tindakankomponen = [];
            $groupingKomponenTarif = [];
            $newArr = [];
            foreach ($dataTarifRs as $valTarifRs) {
                $groupingKomponenTarif[$valTarifRs['masukkamar_id']][$valTarifRs['daftartindakan_id']][] = $valTarifRs;
            }
            foreach ($groupingKomponenTarif as $id_masukkamar => $groupMasukKamar) {
                foreach ($groupMasukKamar as $id_daftartindakan => $groupDaftarTindakan) {
                    $tempArr = $groupDaftarTindakan;
                    $key_result = array_search(6, array_column($tempArr, 'komponentarif_id'));
                    $data_tindakanpelayanan = $tempArr[$key_result];
                    unset($tempArr[$key_result]);
                    $harga_cyto = 0;
                    $harga_cyto = ((int) $data_tindakanpelayanan['persencyto_tindakan'] / 100) * $data_tindakanpelayanan['harga_tariftindakan'];
                    $harga_diskon = 0;
                    $harga_diskon = ((int) $data_tindakanpelayanan['persendiskon_tindakan'] / 100) * $data_tindakanpelayanan['harga_tariftindakan'];
                    $total_harga = 0;
                    $total_harga = $data_tindakanpelayanan['harga_tariftindakan'] + $harga_cyto + $harga_diskon;
                    $attribDataPelayanan = [
                        'pendaftaran_id' => $data_tindakanpelayanan['pendaftaran_id'],
                        'pasien_id' => $data_tindakanpelayanan['pasien_id'],
                        'kelaspelayanan_id' => $data_tindakanpelayanan['kelaspelayanan_id'],
                        'instalasi_id' => $data_tindakanpelayanan['instalasi_id'],
                        'carabayar_id' => $data_tindakanpelayanan['carabayar_id'],
                        'jeniskasuspenyakit_id' => $data_tindakanpelayanan['jeniskasuspenyakit_id'],
                        'ruangan_id' => $data_tindakanpelayanan['ruangan_id'],
                        'penjamin_id' => $data_tindakanpelayanan['penjamin_id'],
                        'tgl_tindakan' => date('Y-m-d H:i:s'),
                        'tarif_satuan' => $data_tindakanpelayanan['harga_tariftindakan'],
                        'tarif_tindakan' => $total_harga,
                        'tarifcyto_tindakan' => $harga_cyto,
                        'qty_tindakan' => 1,
                        'dokterpenanggungjawab_id' => $data_tindakanpelayanan['pegawai_id'],
                        'discount_tindakan' => $harga_diskon,
                    ];
                    $mTindakanPelayanan = new TindakanPelayanan;
                    $mTindakanPelayanan->attributes = $attribDataPelayanan;
                    if (!$mTindakanPelayanan->save()) {
                        return ['message' => $mTindakanPelayanan->getErrors()];

                    }
                    foreach ($tempArr as $valTempArr) {
                        $attribDataKomponen = [];
                        $hargakomp_cyto = 0;
                        $hargakomp_cyto = ((int) $valTempArr['persencyto_tindakan'] / 100) * $valTempArr['harga_tariftindakan'];
                        $total_hargakomp = 0;
                        $total_hargakomp = $valTempArr['harga_tariftindakan'] + $hargakomp_cyto;
                        $attribDataKomponen = [
                            'tindakanpelayanan_id' => $mTindakanPelayanan->getPrimaryKey(),
                            'pendaftaran_id' => $valTempArr['komponentarif_id'],
                            'tarif_kompsatuan' => $valTempArr['harga_tariftindakan'],
                            'tarif_tindakankomp' => $total_hargakomp,
                            'tarifcyto_tindakankomp' => $hargakomp_cyto,
                            'subsidiasuransikomp' => 0,
                            'subsidipemerintahkomp' => 0,
                            'iurbiayakomp' => 0,
                        ];
                        $data_tindakankomponen[] = $attribDataKomponen;
                    }
                }
            }
            TindakanKomponen::batchInsert($data_tindakankomponen);
            $queryUpdateTanggalCron = "
                UPDATE masukkamar_t
                SET tgl_last_akomodasi = CURRENT_TIMESTAMP
                FROM (
                    SELECT masukkamar_t.masukkamar_id
                    FROM masukkamar_t
                    JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.pasienpulang_id IS NOT NULL
                    WHERE masukkamar_t.pindahkamar_id IS NULL AND masukkamar_t.tgl_last_akomodasi < CURRENT_DATE) AS subquery
                WHERE masukkamar_t.masukkamar_id=subquery.masukkamar_id;
            ";
            $exeTanggalCron = $connection->createCommand($queryUpdateTanggalCron)->execute();
            $transaction->commit();
            return ['message' => 'Data Akomodasi Berhasil Ditambah'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetListKelompokBarang()
    {
        $model = KelompokBarang::find()
            ->where(['is_active' => true])
            ->all();

        return $model;
    }

    public function actionGetAllDiagnosa()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $diagnosa = Diagnosa::find()
            ->select([
                'diagnosa_id',
                'diagnosa_nama',
                new Expression("CONCAT(diagnosa_kode,' - ',diagnosa_nama) AS nama_diagnosa"),
                // new Expression("CONCAT(diagnosa_kode,' - ',diagnosa_nama) AS nama_diagnosa")
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $diagnosa->andWhere(['ILIKE', 'LOWER(diagnosa_kode)', strtolower($term)]);
            $diagnosa->orWhere(['ILIKE', 'LOWER(diagnosa_nama)', strtolower($term)]);

        }
        $diagnosa->andWhere(['is_active' => true, 'is_deleted' => false]);

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionGetAllJenisPenyakit()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $offset = $request->get('offset', 0);

        $diagnosa = JenisKasusPenyakit::find()
            ->select([
                'jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama',
                new Expression("CONCAT(jeniskasuspenyakit_nama) AS nama_jenis_penyakit"),
                // new Expression("CONCAT(diagnosa_kode,' - ',jeniskasuspenyakit_nama) AS nama_diagnosa")
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $diagnosa->orWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($term)]);

        }
        $diagnosa->andWhere(['is_deleted' => false]);

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionGetAllRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $keyword = $request->get('keyword', '');
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $diagnosa = Ruangan::find()
            ->select([
                'ruangan_id',
                'ruangan_nama',
                new Expression("CONCAT(ruangan_id) AS nama_ruangan"),
            ]);
        if (!empty($get['keyword'])) {
            $term = $get['keyword'];
            $diagnosa->orWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($term)]);

        }
        $diagnosa->andWhere(['is_deleted' => false]);

        return $diagnosa->offset($offset)->limit($limit)->asArray()->all();
    }

    public function getDataJenisPenyakit()
    {
        $data = JenisKasusPenyakit::find();
        $result = $data->where(['is_deleted' => false])
            ->orderBy(['jeniskasuspenyakit_nama' => SORT_ASC]);

        return $result;
    }

    public function getDataDiagnosa()
    {
        $data = Diagnosa::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['diagnosa_nama' => SORT_ASC]);

        return $result;
    }

    public function actionGetFilterKasusPenyakitDiagonsa()
    {

        $getRuanganAll = $this->GetRuanganAll();
        $arrRuanganAll = ArrayHelper::map($getRuanganAll, 'ruangan_id', 'ruangan_nama');

        $jenis_kasus_penyakit = $this->getDataJenisPenyakit()->asArray()->all();
        $arrjenis_kasus_penyakit = ArrayHelper::map($jenis_kasus_penyakit, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

        return [
            'jenis_kasus_penyakit' => $arrjenis_kasus_penyakit,
            'ruangan' => $arrRuanganAll,
        ];
    }

    public function actionGetListPenyakitDiagonsa()
    {

        $diagnosa = $this->getDataDiagnosa()->asArray()->all();
        $arrdiagnosa = ArrayHelper::map($diagnosa, 'diagnosa_id', 'diagnosa_nama');

        return [
            'diagnosa' => $arrdiagnosa,
        ];
    }

    public function actionGetAllMakananDiet()
    {

        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];

            $model = MakananDiet::find()->andWhere(['is_deleted' => false, 'is_active' => true]);
            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'makanandiet_nama', $q]);
            }
            $result = $model->all();

            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetListJenisDiet()
    {

        $dataMenuDiet = MenuDietMP::find()->select(['jenisdiet_id'])
            ->groupBy(['jenisdiet_id'])
            ->asArray()
            ->all();
        $arr_menuDiet = [];
        if (Yii::$app->request->get('not_in') == "true") {
            foreach ($dataMenuDiet as $key => $value) {
                $arr_menuDiet[] = $value['jenisdiet_id'];
            }
        }
        $jenisDiet = $this->getJenisDiet($arr_menuDiet)->asArray()->all();
        $arrJenisDiet = ArrayHelper::map($jenisDiet, 'jenisdiet_id', 'jenisdiet_nama');

        return [
            'jenis_diet' => $arrJenisDiet,
            'data_menuDiet' => $arr_menuDiet,
        ];
    }

    public function actionGetDataSupplier()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');

        $model = Supplier::find()->where(['ILIKE', 'supplier_nama', $q]);
        
        return $model->asArray()->all();
    }

    public function getJenisDiet($jenisID)
    {
        $data = JenisDiet::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['jenisdiet_nama' => SORT_ASC]);
        if ($jenisID) {
            $result->andWhere(['not in', 'jenisdiet_id', $jenisID]);
        }
        return $result;
    }

    public function actionGetDataMakananDiet()
    {
        $request = Yii::$app->request;
        $word = $request->get('term');
        if ($word) {
            return MakananDiet::find()
                ->where(['is_active' => true, 'is_deleted' => false])
                ->andWhere([
                    'ILIKE', 'LOWER(makanandiet_nama)', strtolower($word),
                ])
                ->orderBy(['makanandiet_nama' => SORT_ASC])
                ->asArray()->all();
        }
        return [];
    }

    /**
     * @todo cron for unset notification jadwal dokter
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionUnsetNotifDokter()
    {
        $connection = Yii::$app->db;
        $sql = "UPDATE jadwaldokter_m
            SET
                notifikasi_id = NULL
            WHERE
                notifikasi_id IS NOT NULL
        ";
        $connection->createCommand($sql)->execute();
        return [
            'message' => 'Notif dokter berhasil di unset',
        ];
    }

    /**
     * @todo get sisa antrian
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetSisaAntrian($id)
    {
        try {
            $model = new PayloadForm;
            $model->jenisantrian_id = $id;
            if (!$model->validate()) {
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }
            $data = DocoAntrian::getSisaAntrian($id);
            $dataSisaAntrian['list_sisa_antrian'] = $data;
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'panggil-antrian-' . $mode,
                'message' => json_encode(['data' => $dataSisaAntrian]),
            ]);

            return [
                'message' => 'Data Fetched',
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\yii\base\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    private function buttonEditBasePrice($query, $ruangan_id)
    {
        $query->andWhere(['!=', 'ruangan_id', $ruangan_id]);
        $getData = $query->asArray()->one();
        $result = !empty($getData) ? true : false;

        return $result;
    }

    public function actionGetDataPegawai()
    {
        $request = Yii::$app->request;
        $superVisor = false;
        $staffGudangFarmasi = false;
        try {
            $result = ['style' => ''];
            $superVisorK = null;
            $pegawai_id = $request->get('pegawai_id');
            $jabatan_id = $request->get('jabatan_id');
            $ruangan_id = $request->get('ruangan_id');
            $query = $this->getPegawaiView();
            $query->where(['pegawai_id' => $pegawai_id]);

            $notBoth = $this->buttonEditBasePrice($query, $jabatan_id);
            if ($notBoth == true) {
                $result = ['style' => 'display:none;'];
            }
            return $result;
        } catch (Exception $e) {
            return ['supervisor' => $superVisor,
                'staff_gudang_farmasi' => $staffGudangFarmasi,
            ];
        }
    }

    public function kelompokPemeriksaan()
    {
        try {
            $data = KelompokPemeriksaanLab::find()->orderBy(['nama_kelompok' => SORT_ASC])->where(['is_active' => true])->all();
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionGetKelompokPemeriksaan()
    {

        $getkelompokPemeriksaan = $this->kelompokPemeriksaan();
        $reskelompokPemeriksaan = [];
        if (count($getkelompokPemeriksaan) > 0) {
            $reskelompokPemeriksaan = ArrayHelper::map($getkelompokPemeriksaan, 'kelompokpemeriksaanlab_id', 'nama_kelompok');
        }

        return [
            'kelompok_pemeriksaan_lab' => $reskelompokPemeriksaan,
        ];
    }

    /*filter index pegawai */
    public function actionListJabatan()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);

        $model = new Jabatan();
        $query = $model::find();
        if ($term) {
            $query->where(['ILIKE', 'LOWER(jabatan_nama)', $term]);
            $query->orWhere(['ILIKE', 'LOWER(jabatan_singkatan)', $term]);
        }
        $query->orderBy(['jabatan_nama' => SORT_ASC]);
        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionListPangkat()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);

        $model = new Pangkat();
        $query = $model::find();
        if ($term) {
            $query->where(['ILIKE', 'LOWER(pangkat_nama)', $term]);
        }
        $query->orderBy(['pangkat_nama' => SORT_ASC]);
        return $query->offset($offset)->limit($limit)->asArray()->all();
    }

    /**
     * @author : Novia (novia.putri@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetListGroupMargin($keyValue) {
        $data = GroupMargin::find()->where(['is_deleted' => false
                                            ,'is_active'=>true])
                                ->orderBy(['groupmargin_id' => SORT_ASC])
                                ->asArray()->all();
        $items = ArrayHelper::map($data, $keyValue, 'groupmargin_nama');
        return $items;
    }

    public function getKamar()
    {
        $data = KamarRuangan::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kamarruangan_nokamar' => SORT_ASC]);

        return $result;
    }

    public function actionGetListManufaktur()
    {
        $model = Manufaktur::find()
            ->select([
                'manufaktur_id',
                'nama',
            ])
            ->where(['is_active' => true])
            ->asArray()
            ->all();

        return $model;
    }
    
    public function actionGetRuteObat()
    {
        $model = RuteObat::find()
            ->select([
                'ruteobat_id',
                'nama_rute',
            ])
            ->where(['is_active' => true])
            ->asArray()
            ->all();

        return $model;
    }

    public function actionKonfigAutoGenerateKodeBarang()
    {
        $query = KonfigGudang::find()->select('is_autogeneratekodebarang')->one();

        return $query;
    }

    public function actionGetListJenisObat(){
        $request = Yii::$app->request;
        $data = JenisObatAlkes::find()
            ->select(["jenisobatalkes_id", "jenisobatalkes_nama", "group_jenisobat"])
            ->where(["is_consignment" => $request->get('is_consignment', false)])
            ->orderBy("jenisobatalkes_nama", SORT_ASC)
            ->asArray()
            ->all();
        
        $group_obat = DocoConstants::GROUP_JENISOBAT_OBAT;
        $group_alkes = DocoConstants::GROUP_JENISOBAT_ALKES;

        $data_group = ArrayHelper::index($data, null, [function ($element) {
            return !empty($element['group_jenisobat']) ? $element['group_jenisobat'] : "no_group";
        }]);
        
        return [
            'list_jenis_obat' => isset($data_group["no_group"]) ? $data_group["no_group"] : [],
            'data_obat' => isset($data_group[$group_obat]) ? $data_group[$group_obat] : [],
            'data_alkes' => isset($data_group[$group_alkes]) ? $data_group[$group_alkes] : []
        ];
    }

    public function actionGetNarkotikaId() {
        return [
            'narkotika_id' => DocoConstansId::actionGetId(DocoConstants::JENISOBAT_NARKOTIKA)
        ];
    }
    
    public function actionGetAtcCode()
    {
        $model = ATCCode::find()
            ->select([
                'atccode_id',
                new Expression("CONCAT(atccode,' - ',classification_atccode) AS atccode")
            ])
            ->where(['is_active' => true])
            ->asArray()
            ->all();

        return $model;
    }
    
    public function actionGetMims($parent = 0) {
        $mims = ObatAlkesMIMS::find()
            ->select([
                'obatalkesmims_id',
                'parent_id',
                'obatalkesmims_nama'
            ])
            ->where(['is_active' => true])
            ->asArray()
            ->all();
        
        return ArrayHelper::index($mims, null, 'parent_id');
    }

    public function actionGetKonfig()
    {
        $result = KonfigFarmasi::find()->where(['=', 'konfigfarmasi_id', "1"]);
        $konfig = $result->asArray()->one();

        return $konfig;
    }

    public function actionGetSupersetMapping()
    {
        return SupersetMapping::find()->asArray()->all();
    }

    public function actionGetAllBarang()
    {
        $model = new BarangView;
        $query = $model->find()
            ->asArray()
            ->all();

        return $query;
    }

    public function actionGetAllObatAlkes()
    {
        $model = new ObatAlkesView;
        $query = $model->find()
            ->asArray()
            ->all();

        return $query;
    }

    public function actionAllPegawaiList()
    {
        $request = Yii::$app->request;
        $payload = $request->get('payload', []);
        $kelompok = $request->get('kelompok');
        $term = isset($payload['term']) ? $payload['term'] : null;
        $page = $payload['page'];
        $query = Pegawai::find()
            ->select([
                'nama_pegawai as id',
                'nama_pegawai as text'
            ]);
            
        if($kelompok != null){
            $query = $query->where(['kelompokpegawai_id' => '1'])
                ->orderBy(['nama_pegawai' => SORT_ASC]);
        }    

        if ($term != null) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }
        $query = $query->andWhere(['=', 'is_active', true]);
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }
}
