<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi All Apotek
 * @copyright 10 January 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoDetailResepturView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\KlasifikasiPasien;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KuotaDokter;
use app\modules\v1\models\StokKuotaDoktor;
use app\modules\v1\models\KonfigSystem;
use Doco\components\ConfigTrait;
use yii\helpers\ArrayHelper;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\components\DocoConstants;
use Doco\Services\Cache;

use app\modules\v1\models\Loket;

// added by Arief
use app\modules\v1\models\Antrian;
use app\modules\v1\models\KonfigantrianV;
use app\modules\v1\models\Pasien;
use Doco\Services\RegistrationService;


class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\InformasiReseptur';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["get-antrian-by-layar"] = ["POST", "GET"];
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


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['set-antrian-number','set-kuota-dokter','lists'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['set-antrian-number','set-kuota-dokter'],
        ];

        return $behaviors;
    }

    /**
     * @todo get all data resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer tipe resep
     */
    private function getAllDataResep($id = null)
    {
        $resep = InformasiResepturView::find()->select([
            'reseptur_id',
            'noresep',
            'tglresep',
            'nama_pasien',
            'instalasireseptur_nama',
            'ruangan_nama',
            'no_pendaftaran',
            'nama_pegawai',
            'pendaftaran_id',
            'carabayar_id',
            'penjamin_id',
            'pasien_id',
            'pasienadmisi_id',
            'penjualanresep_id',
            'kelaspelayanan_id'
        ]);

        if ($id) {
            $resep->where('reseptur_id = :id', ['id' => $id]);
        }

        return $resep;
    }

    /**
     * @todo get all detail obat resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer reseptur id
     */
    private function getAllDetailResep($id)
    {
        $detail = InfoDetailResepturView::find(true)->where(['reseptur_id' => $id]);

        return $detail;
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $data_resep = $this->getAllDataResep()->asArray()->all();
        $data_pegawai = $this->getAllDataDokter()->asArray()->all();
        $data_pasien = $this->getAllDataPasien()->asArray()->all();
        $data_cara_bayar = CaraBayar::find()->asArray()->all();
        $data_penjamin = Penjamin::find()->asArray()->all();
        $data_stok = [];
        if (isset($get['instalasi_id'])) {
            $data_stok = $this->getStokApotek($get['instalasi_id'])->asArray()->all();
        }

        return [
            'data-resep' => $data_resep,
            'data-pegawai' => $data_pegawai,
            'data-pasien' => $data_pasien,
            'data-stok' => $data_stok,
            'data-cara-bayar' => $data_cara_bayar,
            'data-penjamin' => $data_penjamin
        ];
    }

    /**
     * @todo get all detail data resep obat
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer reseptur id
     */
    public function actionDetailResep($id)
    {
        $detail_resep = $this->getAllDetailResep($id)->asArray()->all();

        return [
            'detail-resep' => $detail_resep
        ];
    }

    /**
     * @todo get all data pasien
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getAllDataPasien()
    {
        $pasien = PasienView::find();

        return $pasien;
    }

    /**
     * @todo get all data dokter
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    private function getAllDataDokter()
    {
        $dokter = DokterView::find();

        // if ($id) {
        //     $dokter->where('reseptur_id = :id', ['id' => $id]);
        // }

        return $dokter;
    }

    /**
     * @todo get all data stok apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer instalasi_id
     */

    private function getStokApotek($instalasi_id)
    {
        $stok = InfoStokObatAlkesView::find()->select([
            'instalasi_id',
            'obatalkes_id',
            'obatalkes_kode',
            'obatalkes_namalain',
            'qty_tersedia',
            'ppn',
            'hargajual',
            'satuankecil_id',
            'harganetto'
        ]);

        if ($instalasi_id) {
            $stok->where(['instalasi_id' => $instalasi_id]);
        }

        return $stok;
    }

    public function actionListStokApotek()
    {
        $model = new InfoStokObatAlkesView;
        $request = Yii::$app->request;
        $get = $request->get();
        $query = $this->getStokApotek($get['instalasi_id']);
        if (isset($_GET['advanced-filter']['obatalkes_namalain'])) {
            $obat_alkes_nama = $request->get('advanced-filter')['obatalkes_namalain'];
            $query->andFilterWhere(['ILIKE', 'obatalkes_namalain', $obat_alkes_nama]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListPasien()
    {
        $model = new PasienView;
        $request = Yii::$app->request;
        $get = $request->get();
        $query = $this->getAllDataPasien();
        if (isset($_GET['advanced-filter']['nama_pasien'])) {
            $nama_pasien = $request->get('advanced-filter')['nama_pasien'];
            $query->andFilterWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
        }
        if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
            $get_tgl = $request->get('advanced-filter')['tanggal_lahir'];
            $tanggal = date('Y-m-d', strtotime($get_tgl));
            $query->where('tanggal_lahir = :tanggal_lahir', ['tanggal_lahir' => $tanggal]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListDokter()
    {
        $model = new DokterView;
        $request = Yii::$app->request;
        $get = $request->get();
        $query = $this->getAllDataDokter();
        // if (isset($_GET['advanced-filter']['nama_pasien'])) {
        //     $nama_pasien = $request->get('advanced-filter')['nama_pasien'];
        //     $query->andFilterWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
        // }
        // if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
        //     $get_tgl = $request->get('advanced-filter')['tanggal_lahir'];
        //     $tanggal = date('Y-m-d', strtotime($get_tgl));
        //     $query->where('tanggal_lahir = :tanggal_lahir', ['tanggal_lahir' => $tanggal]);
        // }
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo get no_resep
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetNoResep()
    {
        $data = ApotekComponent::getNoResep();

        return $data;
    }
    public function actionUpdateNoResep()
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $post = $request->post();
        $db->createCommand()
            ->update('penomoran_k', [
                'last_generate' => $post['last_generate'],
                'last_number' => $post['last_number']
            ], "penomoran_id = 4")
            ->execute();

        return ['message' => 'Success'];
    }

    public function actionGetDataObatalkes()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        if(!empty($post['term'])){

            $result = $this->getObatAlkes();
            $result->select(['obatalkes_namalain']);
            $term = $post['term'];
            $result->where(['like', 'LOWER(obatalkes_namalain)', $term]);
            return $result->asArray()->all();
        }else{
            $model = new ObatAlkes;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }
    }
    public function getObatAlkes()
    {
        $model = ObatAlkes::find();
        return $model;
    }

    public function actionListCaraBayar($default='1') {
        $data = CaraBayar::find()->where(['is_active' => 't'])->orderBy('carabayar_id');
        if ($default=="1") {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        } else {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'namaAndSingkatan');
        }

        return $items;
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    private function getLookupDataByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => TRUE]);
        }

        return $result;
    }

    public function actionListKlasifikasiPasien()
    {
        // perubahan untuk pengambilan sesuai dengan config : ali.padiah@docotel.com
        try{
            // $data = KlasifikasiPasien::find()->select(['klasifikasipasien_id','klasifikasipasien_nama'])->where(['is_active' => true,'is_deleted'=>false])->asArray()->all();


            $sql = " SELECT
                        a.klasifikasipasien_id,a.klasifikasipasien_nama,string_agg(b.groupcarabayar_id::text, '-'::text) AS cara_bayar
                    from
                        klasifikasipasien_m a
                    join
                        konfigantrian_m b on a.klasifikasipasien_id = b.klasifikasipasien_id
                    where
                        a.is_active = true and
                        a.is_deleted = false and
                        b.is_active = true and
                        b.is_deleted = false
                    group by
                        a.klasifikasipasien_id";

            return Yii::$app->db->createCommand($sql)->queryAll();

        } catch(\Exception $e){
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionListStatusPasien()
    {
        try{
            $query = $this->getLookupDataByType('status_pasien');
            return $query
                    ->select([
                        'statuspasien_id'=>'lookup_id',
                        'statuspasien_nama'=>'lookup_name'
                    ])
                    ->asArray()->all();
        } catch(\Exception $e){
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionListGroupCaraBayar()
    {
        try{
            $query = $this->getLookupDataByType('group_carabayar');
            return $query
                    ->select([
                        'groupcarabayar_id'=>'lookup_id',
                        'groupcarabayar_nama'=>'lookup_name'
                    ])
                    ->asArray()->all();
        } catch(\Exception $e){
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionListRuangan($id)
    {
        try{
            $data = Ruangan::find()->select(['ruangan_id' ,'ruangan_nama' ,'is_active'])
                ->where(['instalasi_id' => $id])->asArray()->all();
            return $data;
        } catch(\Exception $e){
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionListInstalasiPenunjang()
    {
        try{
            $data = Instalasi::find()->select(['instalasi_id','instalasi_nama'])->where(['is_active' => true,'is_deleted'=>false,'is_penunjang'=>true])->asArray()->all();
            return $data;
        } catch(\Exception $e){
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionGetAntrianByLayar()
    {
        $model = new Antrian;
        $request = Yii::$app->request;
        $get = $request->get();

        $loket_id = $get['advanced-filter']['loket_id'];
        $loket_id = json_decode($loket_id);

        $status_antrian = $get['advanced-filter']['status_antrian'];
        $status_antrian = json_decode($status_antrian);

        $result = $this->getData();
        $result->andWhere(['IN', 'loket_id', $loket_id ]);
        $result->andWhere(['IN', 'status_antrian', $status_antrian ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $result);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSetKuotaDokter()
    {
        set_time_limit(0);
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d');
        $hari = $tanggal['hari'];
        $kuotaAntrianDokter = DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;

        $modelKonfig = KonfigSystem::find()->one();
        $kuotaAntrian = empty($modelKonfig->kuota_antrian) ? DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER : $modelKonfig->kuota_antrian;

        // UNtuk mengamil id_hari
        $lookup = $connection->createCommand("
            SELECT lookup_id FROM lookup_m WHERE lookup_type = 'hari' AND lookup_name = '{$hari}'
        ")->queryOne();
        $lookup_id = isset($lookup['lookup_id']) ? $lookup['lookup_id'] : 0;

        // Mencari Penambahan Kuota
        $penambahanKuota = $connection->createCommand("
            SELECT * FROM jadwaldoktertambahan_m WHERE tgl_penambahan = '{$dateNow}'
        ")->queryAll();



        // Grouping kuota berdasarkan jadwaldokter
        $groupKuota = $tambahanId = [];
        foreach ($penambahanKuota as $value) {
            if (!isset($groupKuota[$value['jadwaldokter_id']])) {
                $groupKuota[$value['jadwaldokter_id']] = 0;
            }
            $tambahanId[$value['jadwaldokter_id']] = $value['jadwaldoktertambahan_id'];
            $groupKuota[$value['jadwaldokter_id']] += $value['kuota_penambahan'];
        }

        if ($kuotaAntrian == $kuotaAntrianDokter) {
            $this->getKuotaDaftaraOnline();

            $connection->createCommand("
                DELETE FROM kuotadokter_r WHERE jadwalbukapoli_id IS NULL
            ")->execute();
            // Filterig jadwal dokter hari ini
            $jadwalDokter = $connection->createCommand("
                SELECT jadwaldokter_m.* FROM jadwaldokter_m
                INNER JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                WHERE jadwalbukapoli_m.is_deleted = FALSE
                AND jadwalbukapoli_m.is_active = true
                AND jadwalbukapoli_m.hari = '{$lookup_id}'
            ")->queryAll();

            if ($jadwalDokter) {
                $quotaDokter = $jadwalId = $tmpQuota = [];
                foreach ($jadwalDokter as $value) {
                    $quotaTambahan = isset($groupKuota[$value['jadwaldokter_id']]) ? $groupKuota[$value['jadwaldokter_id']] : 0;
                    $idTambahan = isset($tambahanId[$value['jadwaldokter_id']]) ? $tambahanId[$value['jadwaldokter_id']] : null;
                    $quota = $value['maximumantrian'] + $quotaTambahan;
                    $jadwalId[] = $value['jadwaldokter_id'];
                    $kuotaBpjsOff = !is_null($value['kuota_bpjs_offline']) ? $value['kuota_bpjs_offline'] : 0;
                    $kuotaNonBpjsOff = !is_null($value['kuota_nonbpjs_offline']) ? $value['kuota_nonbpjs_offline'] : 0;
                    $kuotaBpjsOl = !is_null($value['kuota_bpjs_online']) ? $value['kuota_bpjs_online'] : 0;
                    $kuotaNonBpjsOl = !is_null($value['kuota_nonbpjs_online']) ? $value['kuota_nonbpjs_online'] : 0;

                    // if(!isset($tmpQuota[$value['jadwaldokter_id']])) {
                    //     $tmpQuota[$value['jadwaldokter_id']][0] = [
                    //         'jadwaldokter_id' => $value['jadwaldokter_id'],
                    //         'tgltransaksi_in' => $dateNow,
                    //         'kuota_in' => $quota,
                    //             'kuota_out' => 0,
                    //         'is_online' => false,
                    //         'flag' => true,
                    //         'kuota_bpjs_offline' => (int) $kuotaBpjsOff,
                    //         'kuota_nonbpjs_offline' => (int) $kuotaNonBpjsOff,
                    //         'kuota_bpjs_online' => 0,
                    //         'kuota_nonbpjs_online' => 0,
                    //         'kuota_out_bpjs' => 0,
                    //         'kuota_out_nonbpjs' => 0
                    //     ];

                    //     if (!is_null($value['kuota_online'])) {
                    //         $tmpQuota[$value['jadwaldokter_id']][1] = [
                    //             'jadwaldokter_id' => $value['jadwaldokter_id'],
                    //             'tgltransaksi_in' => $dateNow,
                    //             'kuota_in' => $value['kuota_online'],
                    //             'kuota_out' => 0,
                    //             'is_online' => true,
                    //             'flag' => true,
                    //             'kuota_bpjs_online' => (int) $kuotaBpjsOl,
                    //             'kuota_nonbpjs_online' => (int) $kuotaNonBpjsOl,
                    //             'kuota_bpjs_offline' => 0,
                    //             'kuota_nonbpjs_offline' => 0,
                    //             'kuota_out_bpjs' => 0,
                    //             'kuota_out_nonbpjs' => 0
                    //         ];
                    //     }
                    // }

                    $quotaDokter[] = [
                        'jadwaldokter_id' => $value['jadwaldokter_id'],
                        'tgltransaksi_in' => $dateNow,
                        'kuota_in' => $quota,
                            'kuota_out' => 0,
                        'is_online' => false,
                        'flag' => true,
                        'kuota_bpjs_offline' => (int) $kuotaBpjsOff,
                        'kuota_nonbpjs_offline' => (int) $kuotaNonBpjsOff,
                        'kuota_bpjs_online' => 0,
                        'kuota_nonbpjs_online' => 0,
                        'kuota_out_bpjs' => 0,
                        'kuota_out_nonbpjs' => 0
                    ];

                    if (!is_null($value['kuota_online'])) {
                        $quotaDokter[] = [
                            'jadwaldokter_id' => $value['jadwaldokter_id'],
                            'tgltransaksi_in' => $dateNow,
                            'kuota_in' => $value['kuota_online'],
                            'kuota_out' => 0,
                            'is_online' => true,
                            'flag' => true,
                            'kuota_bpjs_online' => (int) $kuotaBpjsOl,
                            'kuota_nonbpjs_online' => (int) $kuotaNonBpjsOl,
                            'kuota_bpjs_offline' => 0,
                            'kuota_nonbpjs_offline' => 0,
                            'kuota_out_bpjs' => 0,
                            'kuota_out_nonbpjs' => 0
                        ];
                    }
                }

                // if(isset($tmpQuota) && !empty($tmpQuota)) {
                //     foreach($tmpQuota as $key => $value) {
                //         foreach($value  as $k => $v) {
                //             $quotaDokter[] = $v;
                //         }
                //     }
                // }

                $inCondition = '(' . implode(",", $jadwalId) . ')';
                $connection->createCommand("
                    UPDATE stokkuotadokter_t
                        SET is_deleted = TRUE,
                            flag = FALSE,
                            is_active = FALSE
                        WHERE jadwaldokter_id IN {$inCondition}
                ")->execute();

                StokKuotaDoktor::batchInsert($quotaDokter,false);

                // set kuota from daftar online
                $this->setKuotaDaftarOnline();
            }
        } else {
            $connection->createCommand("
                DELETE FROM kuotadokter_r WHERE jadwalbukapoli_id IS NOT NULL
            ")->execute();

            // Filterig jadwal dokter hari ini
            $jadwalPoli = $connection->createCommand("
                SELECT *
                FROM jadwalbukapoli_m
                WHERE is_deleted = false
                AND is_active = true
                AND hari = '{$lookup_id}'
            ")->queryAll();

            if ($jadwalPoli) {
                $quotaPoli = $jadwalId = [];

                foreach ($jadwalPoli as $value) {
                    // $quotaTambahan = isset($groupKuota[$value['jadwaldokter_id']]) ? $groupKuota[$value['jadwaldokter_id']] : 0;
                    // $idTambahan = isset($tambahanId[$value['jadwaldokter_id']]) ? $tambahanId[$value['jadwaldokter_id']] : null;
                    // $quota = $value['maxantrian_poli'] + $quotaTambahan;
                    $jadwalId[] = $value['jadwalbukapoli_id'];

                    $quotaPoli[] = [
                        'jadwalbukapoli_id' => $value['jadwalbukapoli_id'],
                        'tgltransaksi_in' => $dateNow,
                        'kuota_in' => $value['maxantrian_poli'],
                        'kuota_out' => 0,
                        'is_online' => false,
                        'flag' => true,
                        'kuota_bpjs_offline' => 0,
                        'kuota_nonbpjs_offline' => 0,
                        'kuota_bpjs_online' => 0,
                        'kuota_nonbpjs_online' => 0,
                        'kuota_out_bpjs' => 0,
                        'kuota_out_nonbpjs' => 0
                    ];

                    if (!is_null($value['kuota_online'])) {
                        $quotaPoli[] = [
                            'jadwalbukapoli_id' => $value['jadwalbukapoli_id'],
                            'tgltransaksi_in' => $dateNow,
                            'kuota_in' => $value['kuota_online'],
                            'kuota_out' => 0,
                            'is_online' => true,
                            'flag' => true,
                            'kuota_bpjs_offline' => 0,
                            'kuota_nonbpjs_offline' => 0,
                            'kuota_bpjs_online' => 0,
                            'kuota_nonbpjs_online' => 0,
                            'kuota_out_bpjs' => 0,
                            'kuota_out_nonbpjs' => 0
                        ];
                    }
                }

                $inCondition = '(' . implode(",", $jadwalId) . ')';
                $connection->createCommand("
                    UPDATE stokkuotadokter_t
                        SET is_deleted = true,
                            flag = false,
                            is_active = false
                        WHERE jadwalbukapoli_id IN {$inCondition}
                ")->execute();

                StokKuotaDoktor::batchInsert($quotaPoli,false);

                // set kuota from daftar online
                // $this->setKuotaDaftarOnline();
            }
        }

        return [
            'message' => 'Kuota dokter berhasil di buat'
        ];
    }

    private function getData($filter = null)
    {
        $returnData = Antrian::find();
        return $returnData;
    }

    public function actionGetJenispengambilanByLayar()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = $this->getData();

        $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListJenisAntrian()
    {
        try {
            $query = $this->getLookupDataByType('jenis_antrian');
            return $query
                    ->select([
                        'jenisantrian_id'=>'lookup_id',
                        'jenisantrian_nama'=>'lookup_name'
                    ])
                    ->asArray()->all();
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }

    public function actionListJenisKunjungan()
    {
        try {
            $query = $this->getLookupDataByType('jeniskunjungan');
            return $query
                    ->select([
                        'jeniskunjungan_id'=>'lookup_id',
                        'jeniskunjungan_nama'=>'lookup_value'
                    ])
                    ->asArray()->all();
        } catch (Exception $e) {
            return ['message'=>'Kesalahan Internal'];
        }
    }


    public function actionListPolyAntrian($jenis_id = null, $detail_id = null)
    {
        // $date_now = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));

        // $jam = date('H:i:s');

        // // Get hari from lookup
        // $modelLookup = Lookup::find()->where(['lookup_value' => $date_now['hari']])->one();

        // // Check model lookup
        // if ($modelLookup != null) {
        //     // Assign hari
        //     $hari = $modelLookup->lookup_id;
        // }
        // else {
        //     $hari = 0;
        // }

        // $sql = "
        //     SELECT
        //         ruangan.ruangan_id,
        //         ruangan.ruangan_nama,
        //         ruangan.is_active,
        //         jadwal_ruangan.is_active,
        //         jadwal_ruangan.jam_mulai,
        //         jadwal_ruangan.jam_tutup,
        //         jadwal_ruangan.maxantiran_poli,
        //         CASE
        //     WHEN jadwal_ruangan.jadwalbukapoli_id IS NOT NULL THEN
        //         TRUE
        //     ELSE
        //         FALSE
        //     END AS is_buka,
        //      jadwal_ruangan_dokter.sisa_kuota AS sisa_kuota
        //     FROM
        //         ruangan_m ruangan
        //     LEFT JOIN (
        //         SELECT
        //             jadwal_buka_poli.jadwalbukapoli_id,
        //             jadwal_buka_poli.ruangan_id,
        //             jadwal_buka_poli.jam_mulai,
        //             jadwal_buka_poli.is_active,
        //             jadwal_buka_poli.jam_tutup,
        //             jadwal_buka_poli.maxantiran_poli
        //         FROM
        //             jadwalbukapoli_m jadwal_buka_poli
        //         WHERE
        //             jadwal_buka_poli.is_deleted = FALSE
        //         AND jadwal_buka_poli.hari = '{$hari}'
        //         AND jadwal_buka_poli.jam_tutup :: TIME >= now() :: TIME
        //     ) jadwal_ruangan ON jadwal_ruangan.ruangan_id = ruangan.ruangan_id
        //     LEFT JOIN (
        //         SELECT
        //             jadwal_dokter.ruangan_id,
        //             SUM (jadwal_kuota.sisa_kuota) AS sisa_kuota
        //         FROM
        //             jadwaldokter_m jadwal_dokter
        //         LEFT JOIN (
        //             SELECT
        //                 kuota_dokter.jadwaldokter_id,
        //                 SUM (
        //                     kuota_dokter.kuota_tersedia
        //                 ) AS sisa_kuota
        //             FROM
        //                 kuotadokter_r kuota_dokter
        //             GROUP BY
        //                 kuota_dokter.jadwaldokter_id
        //         ) jadwal_kuota ON jadwal_kuota.jadwaldokter_id = jadwal_dokter.jadwaldokter_id
        //         WHERE
        //             jadwal_dokter.is_deleted = FALSE
        //         AND jadwal_dokter.jadwaldokter_tutup :: TIME >= now() :: TIME
        //         GROUP BY
        //             jadwal_dokter.ruangan_id
        //     ) jadwal_ruangan_dokter ON jadwal_ruangan_dokter.ruangan_id = ruangan.ruangan_id
        //     WHERE
        //         ruangan.is_deleted = FALSE
        //     AND ruangan.instalasi_id = 1
        //     GROUP BY
        //         ruangan.ruangan_id,
        //         jadwal_ruangan.jam_mulai,
        //         jadwal_ruangan.jam_tutup,
        //         jadwal_ruangan.is_active,
        //         jadwal_ruangan.maxantiran_poli,
        //         jadwal_ruangan.jadwalbukapoli_id,
        //         jadwal_ruangan_dokter.sisa_kuota
        // ";

        // new concept get poli antrian : ali.padilah@docotel.com
        $model_konfig = KonfigSystem::find()->one();
        $this_day = DocoHelpers::getIdHariIni();
        // interval berapa jam dokter aktif dari jam sekarang
        $interval = empty($model_konfig->start_antrian) ? 0 : $model_konfig->start_antrian;
        $interval_status = empty($model_konfig->start_antrian_status) ? false : $model_konfig->start_antrian_status;
        $kuotaAntrian = empty($model_konfig->kuota_antrian) ? DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER : $model_konfig->kuota_antrian;
        $executive_condition = $this->getExecutiveCondition($jenis_id);

        // $sql = " SELECT
        //             a.jadwalbukapoli_id,a.ruangan_id,a.waktu_pelayanan,a.jam_mulai,a.jam_tutup,b.ruangan_nama,
        //             ( select
        //                 sum(y.kuota_tersedia)
        //              from
        //                 cetakjadwaldokter_v x
        //              inner join
        //                 kuotadokter_r y on x.jadwaldokter_id = y.jadwaldokter_id
        //              where
        //                 hari_id = a.hari and
        //                 ruangan_id = a.ruangan_id and
        //                 y.is_online = false ) as sisa_kuota
        //          from
        //             jadwalbukapoli_m a
        //          inner join
        //             ruangan_m b on a.ruangan_id = b.ruangan_id
        //          where
        //             a.is_deleted = false and
        //             a.is_active = true and
        //             b.is_deleted = false and
        //             b.is_active = true and
        //             a.hari = {$this_day} and ";
        // if ($interval_status) {
        //     $sql .= "jam_mulai :: time <= now() :: time + interval '{$interval} minutes' and ";
        // }
        // $sql .= " jam_tutup :: time >= now() :: time
        //          order by
        //             sisa_kuota asc";

        if ($kuotaAntrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) {
            $join = '';
            $where = '';
            if ($detail_id) {
                $join = ' INNER JOIN jenisantrianruangan_mp ON cetakjadwaldokter_v.ruangan_id = jenisantrianruangan_mp.ruangan_id';
                $where = ' AND jenisantrianruangan_mp.jenisantriandetail_id = '.$detail_id;
            }

            $sql = "SELECT
                data_kuota.*,
                b.ruangan_nama
            FROM
                (
                SELECT
                    cetakjadwaldokter_v.jadwalbukapoli_id,
                    cetakjadwaldokter_v.ruangan_id,
                    cetakjadwaldokter_v.instalasi_id,
                    cetakjadwaldokter_v.waktu_pelayanan,
                    cetakjadwaldokter_v.jam_mulai,
                    cetakjadwaldokter_v.jam_tutup,
                    SUM ( y.kuota_tersedia ) AS sisa_kuota
                FROM
                    cetakjadwaldokter_v
                    INNER JOIN kuotadokter_r y ON cetakjadwaldokter_v.jadwaldokter_id = y.jadwaldokter_id
                    $join
                WHERE
                    y.is_online = FALSE
                    AND jadwaldokter_tutup :: TIME >= now() :: TIME
                    $where
                GROUP BY cetakjadwaldokter_v.jadwalbukapoli_id,
                    cetakjadwaldokter_v.ruangan_id,
                    cetakjadwaldokter_v.instalasi_id,
                    cetakjadwaldokter_v.waktu_pelayanan,
                    cetakjadwaldokter_v.jam_mulai,
                    cetakjadwaldokter_v.jam_tutup
                ) AS data_kuota
                JOIN jadwalbukapoli_m j ON j.jadwalbukapoli_id = data_kuota.jadwalbukapoli_id
                INNER JOIN ruangan_m b ON data_kuota.ruangan_id = b.ruangan_id AND b.is_executive IS ".json_encode($executive_condition)."
            WHERE
                j.is_deleted = FALSE
                AND j.is_active = TRUE
                AND b.is_deleted = FALSE
                AND b.is_active = TRUE
                AND j.hari = {$this_day}

            ";

            if ($interval_status) {
                $sql .= " AND j.jam_mulai :: time <= now() :: time + interval '{$interval} minutes' ";
            }

             $sql .= "
                AND j.jam_tutup :: TIME >= now() :: TIME
                ORDER BY b.ruangan_nama ASC,
                    sisa_kuota ASC
            ";
        } else {
            $join = '';
            $where = '';
            if ($detail_id) {
                $join = ' INNER JOIN jenisantrianruangan_mp ON jadwalbukapoli_m.ruangan_id = jenisantrianruangan_mp.ruangan_id';
                $where = ' AND jenisantrianruangan_mp.jenisantriandetail_id = '.$detail_id;
            }
            $sql = "SELECT
                data_kuota.*,
                ruangan_m.instalasi_id,
                ruangan_m.ruangan_nama
            FROM
                (
                SELECT
                    jadwalbukapoli_m.jadwalbukapoli_id,
                    jadwalbukapoli_m.ruangan_id,
                    jadwalbukapoli_m.waktu_pelayanan,
                    jadwalbukapoli_m.jam_mulai,
                    jadwalbukapoli_m.jam_tutup,
                    jadwalbukapoli_m.is_deleted,
                    jadwalbukapoli_m.is_active,
                    jadwalbukapoli_m.hari,
                    SUM(kuotadokter_r.kuota_tersedia) AS sisa_kuota
                FROM
                    jadwalbukapoli_m
                INNER JOIN kuotadokter_r ON kuotadokter_r.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
                $join
                WHERE
                jadwalbukapoli_m.jam_tutup :: TIME >= now() :: TIME
                $where
                GROUP BY jadwalbukapoli_m.jadwalbukapoli_id,
                    jadwalbukapoli_m.ruangan_id,
                    jadwalbukapoli_m.waktu_pelayanan,
                    jadwalbukapoli_m.jam_mulai,
                    jadwalbukapoli_m.jam_tutup,
                    jadwalbukapoli_m.is_deleted,
                    jadwalbukapoli_m.is_active,
                    jadwalbukapoli_m.hari
                ) AS data_kuota
                INNER JOIN ruangan_m ON ruangan_m.ruangan_id  = data_kuota.ruangan_id AND ruangan_m.is_executive IS ".json_encode($executive_condition)."
            WHERE data_kuota.is_deleted = FALSE
            AND data_kuota.is_active = TRUE
            AND ruangan_m.is_deleted = FALSE
            AND ruangan_m.is_active = TRUE
            AND data_kuota.hari = {$this_day}

            ";

            if ($interval_status) {
                $sql .= " AND data_kuota.jam_mulai :: time <= now() :: time + interval '{$interval} minutes' ";
            }

            $sql .= "
                AND data_kuota.jam_tutup :: TIME >= now() :: TIME
                ORDER BY ruangan_m.ruangan_nama ASC,
                    sisa_kuota ASC
            ";
        }

        return Yii::$app->db->createCommand($sql)->queryAll();
    }


    public function actionLists()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $results = [];
        $params = $post['params'];
        $jenis_id = $post['jenis_id'] ? $post['jenis_id'] : '';
        $detail_id = isset($post['detail_id']) ? $post['detail_id'] : '';
        foreach ($params as $each) {
            if ($each == 'actionListPolyAntrian') {
                $results[$each] = $this->$each($jenis_id, $detail_id);
            }else {
                $results[$each] = $this->$each();
            }

        }
        return $results;
    }


    public function actionSetAntrianNumber()
    {
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d');

        $allJenisAntrian = [DocoConstants::VAR_JA_PEN,DocoConstants::VAR_JA_PD,DocoConstants::VAR_JA_KSR,DocoConstants::VAR_JA_P,DocoConstants::VAR_JA_F];
        $allJenisAntrian = implode(",",$allJenisAntrian);

        // Filtering display Antrian ambil di penomoran_k berdasarkan konfigantrian_m yg jenisantrian_id=177(pendaftaran)
        $ambilDataAntrian = $connection->createCommand("
            SELECT penomoran_k.penomoran_id, konfigantrian_m.jenisantrian_id, penomoran_k.last_number
            FROM penomoran_k
            INNER JOIN konfigantrian_m ON konfigantrian_m.penomoran_id = penomoran_k.penomoran_id
            WHERE konfigantrian_m.is_deleted = FALSE
            AND konfigantrian_m.is_active = true
            AND konfigantrian_m.jenisantrian_id in (".$allJenisAntrian.")
            GROUP BY penomoran_k.penomoran_id, konfigantrian_m.jenisantrian_id
            ORDER BY penomoran_k.penomoran_id
        ")->queryAll();
        // print_r($ambilDataAntrian);die;
        foreach ($ambilDataAntrian as $key => $value) {
            //value id penomoran
            $id = $value['penomoran_id'];
            //update set last_number di tabel penomoran_k
            $sql_set_lastnumber = "UPDATE penomoran_k SET last_number = '000' WHERE penomoran_id = {$id};";
            $update_set_lastnumber = $connection->createCommand($sql_set_lastnumber);
            $update_set_lastnumber->execute();
            //
        }

        return [
            'message' => 'Semua Antrian Berhasil di set ulang'
        ];
    }


    /**
     * @author Rizal
     * @since 2018-05-03 15:48:54
     * @param loginpemakai_id
     * @return
     * @desc
     */
    public function actionGetCurrentLoket($loginpemakai_id = null, $jenisantrian_id = null, $instalasi_id = null)
    {

        try {
            // return Loket::find()
            //     ->andWhere(['loginpemakai_id' => $loginpemakai_id,'jenisantrian_id' => $jenisantrian_id])
            //     ->asArray()->one();

            $loket = new Loket;

            $sql = "
                SELECT
                    t.loket_id,
                    t.loket_nama,
                    t.loket_nourut
                FROM loket_m t
                    LEFT JOIN loket_mp loket_mp ON t.loket_id = loket_mp.loket_id AND loket_mp.is_deleted = false AND loket_mp.is_active = true ";

                    if (!empty($instalasi_id)) {
                        if ($jenisantrian_id == DocoConstants::VAR_JA_PEN) { // penunjang
                            $sql .= "AND instalasi_id = {$instalasi_id} ";
                        }
                    }

            $sql.= "WHERE loginpemakai_id = {$loginpemakai_id}
                AND t.is_deleted = false
                AND t.is_active = true
                AND t.jenisantrian_id = {$jenisantrian_id}
                GROUP BY t.loket_id
                ORDER BY t.loket_nama
            ";

            // return $sql;
            $result = $loket->findBySql($sql)->one();
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

    private function setKuotaDaftarOnline()
    {
        $connection = Yii::$app->db;
        $listDeletedId = Yii::$app->cache->get('data_id_online');
        $listUpdatedKuota = Yii::$app->cache->get('data_kuotadokter_online');
        if(!empty($listDeletedId) && !empty($listUpdatedKuota)) {
            // Yii::error(json_encode($listUpdatedKuota));
            $connection->createCommand("
                    DELETE FROM kuotadokter_r
                    WHERE
                        jadwaldokter_id IN({$listDeletedId})
                    AND is_online = true
            ")->execute();

            KuotaDokter::batchInsert($listUpdatedKuota);
        }
        Yii::$app->cache->delete('data_id_online');
        Yii::$app->cache->delete('data_kuotadokter_online');
    }

    private function getKuotaDaftaraOnline()
    {
        $connection = Yii::$app->db;
        $dateNow = date('Y-m-d');
        $sql = "SELECT
                dk.*,
                k.kuota_masuk,
                k.kuota_bpjs_online,
                k.kuota_nonbpjs_online,
                k.kuota_out_bpjs,
                k.kuota_out_nonbpjs
            FROM
                (
                    SELECT
                        jadwaldokter_id,
                        count(antrian_id) as kuota
                    FROM
                        antrian_t
                    WHERE
                        tgl_antrian > '{$dateNow} 00:00:00'
                    AND
                        is_online = TRUE
                    GROUP BY
                        jadwaldokter_id
                ) as dk
            JOIN kuotadokter_r k ON k.jadwaldokter_id = dk.jadwaldokter_id
            WHERE k.is_online = TRUE
        ";
        $data_antrian = $connection->createCommand($sql)->queryAll();
        if (!empty($data_antrian)) {
            $list_jadwaldokter_id = [];
            $list_kuota_update = [];
            foreach ($data_antrian as $key => $value) {
                $list_jadwaldokter_id[] = $value['jadwaldokter_id'];
                $list_kuota_update[] = [
                    'jadwaldokter_id' => $value['jadwaldokter_id'],
                    'kuota_masuk' => (int) $value['kuota_masuk'],
                    'kuota_keluar' => (int) $value['kuota'],
                    'kuota_tersedia' => ($value['kuota_masuk'] - $value['kuota']),
                    'is_online' => true,
                    'kuota_bpjs_online' => (int) $value['kuota_bpjs_online'],
                    'kuota_nonbpjs_online' => (int) $value['kuota_nonbpjs_online'],
                    'kuota_bpjs_offline' => 0,
                    'kuota_nonbpjs_offline' => 0,
                    'kuota_out_bpjs' => (int) $value['kuota_out_bpjs'],
                    'kuota_out_nonbpjs' => (int) $value['kuota_out_nonbpjs']
                ];
            }
            $replace_list_id = implode(', ', $list_jadwaldokter_id);
            Yii::$app->cache->set('data_id_online', $replace_list_id);
            Yii::$app->cache->set('data_kuotadokter_online', $list_kuota_update);
        }
    }

    public function actionKonfigUrlCetak()
    {
        $mKonfig = KonfigSystem::find()->limit(1)->one();
        $linkDefault = 'http://localhost:8000/print/print-antrian';
        if(!is_null($mKonfig)){
            $linkDefault = @$mKonfig->url_print;
        }
        return $linkDefault;
    }

    /**
    * @todo Fungsi untuk mendapatkan konfig jenis kuota antrian
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionKonfigKuotaAntrian()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();

        $defaultKuotaAntrian = DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
        if(!is_null($konfig)) {
            $defaultKuotaAntrian = isset($konfig->kuota_antrian) ? $konfig->kuota_antrian : DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER;
        }

        return $defaultKuotaAntrian;
    }

    /**
    * @todo Fungsi untuk mendapatkan konfig system
    * @author Sigit Arif Munandar <sigit@docotel.com>
    **/
    public function actionKonfigSystem()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();

        return $konfig;
    }

    public function actionGetJadwalDokter()
    {
        $request = Yii::$app->request;
        $kode_poli = $request->get('kode_poli', null);
        $today_id = date('N') + 74;
        $data = Yii::$app->db->createCommand("
            SELECT p.pegawai_id, p.nama_pegawai, p.kode_dokter_bpjs, rm.kode_ruangan_bpjs, CONCAT(TO_CHAR(jadwaldokter_mulai, 'HH24:MI'), '-', TO_CHAR(jadwaldokter_tutup, 'HH24:MI')) as jam_praktek
            FROM jadwalbukapoli_m j
                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                LEFT JOIN ruangan_m rm ON d.ruangan_id = rm.ruangan_id
            WHERE
                p.kode_dokter_bpjs IS NOT NULL
                AND j.hari = {$today_id}
                AND rm.kode_ruangan_bpjs = '{$kode_poli}'
                AND d.is_deleted = false and d.is_active = TRUE
                AND p.is_deleted = false and p.is_active = TRUE
        ")->queryAll();
        $dokter = (new RegistrationService)->getDokterCuti([
            'data' => $data
        ]);
        return $dokter;
    }

    public function actionGetPoliDokter()
    {
        $request = Yii::$app->request;
        $dokter_id = $request->get('dokter_id', null);
        $data = Yii::$app->db->createCommand("
            SELECT ruangan_id, ruangan_nama, kode_ruangan_bpjs
            FROM ruangan_m
            WHERE ruangan_id IN (
                SELECT ruangan_id
                FROM ruanganpegawai_mp
                WHERE pegawai_id = {$dokter_id}
            ) AND kode_ruangan_bpjs IS NOT NULL;
        ")->queryAll();

        return $data;
    }

    public function actionGetPasien()
    {
        $request = Yii::$app->request;
        $nopeserta_bpjs = $request->get('nopeserta_bpjs', null);
        $query = Pasien::find()->where(['nopeserta_bpjs' => $nopeserta_bpjs])->one();

        return $query;
    }

    public function actionGetUrlFinger()
    {
        $type = Yii::$app->request->get('type');
        return DocoHelpers::response($this->getLookupByType('bpjs')->andWhere(['lookup_name'=>$type])->asArray()->one());
    }

    public function actionGetHistoryPoliPasien()
    {
        $pasien_id = Yii::$app->request->get('pasien_id',0);
        $history = Pasien::getHistoryPoli($pasien_id);
        $trace_log = $history['trace_log'];
        unset($trace_log['trace_log']);
        return [
            'data' => $history,
            'trace_log' => $trace_log
        ];
    }

    public function actionCekPasienBaru(){
        $request = Yii::$app->request;
        $no_identitas = $request->get('no_identitas');
        $no_kartu = $request->get('no_kartu');
        try {
            $is_validated = false;
            $identitas = 'Data';
            $query = Pasien::find();
            if(isset($no_identitas)){
                $identitas = 'NIK';
                $query = $query->andWhere(['no_identitas_pasien' => $no_identitas])->asArray()->all();
            } else if (isset($no_kartu)){
                $identitas = 'No. Kartu BPJS';
                $query = $query->andWhere(['nopeserta_bpjs' => $no_kartu])->asArray()->all();
            } else {
                $query = null;
            }
            if ($query === null || empty($query)) {
                $is_baru = true;
                $text = 'Data No RM tidak ditemukan. Harap ambil antrian pasien BPJS Baru.';
            }elseif(count($query)>1){
                $is_baru = false;
                $text = $identitas.' yang di inputkan double, harap menghubungi petugas';
            } else {
                $is_baru = false;
                $is_validated = true;
                $text = 'Data No RM ditemukan';
            }
            return [
                'text' => $text,
                'is_baru' => $is_baru,
                'is_validated' => $is_validated,
                'pasien_id' => ArrayHelper::getValue($query,'0.pasien_id'),
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
    * @todo Fungsi untuk mendapatkan konfig jenis antrian
    * @author Yafi <yafi.kusnaedi@gmail.com>
    **/
    private function getExecutiveCondition($jenis_id = '')
    {
        $lookup_jenisantrian = Cache::getLookupByType('jenis_antrian'); // cache key => 'var_cache_lookup_by_type-jenis_antrian'

        $is_executive = array_filter($lookup_jenisantrian, function($val) use($jenis_id) {
            if ($val['lookup_id'] == $jenis_id && !empty($val['additional_data'])) {
                $additional_data = json_decode($val['additional_data'], true);
                if (isset($additional_data['is_executive']) && $additional_data['is_executive']) {
                    return true;
                }
            }
            return false;
        });

        return !empty($is_executive);
    }


}
