<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi All Apotek
 * @copyright 10 January 2018 aweutist
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\KetersediaanObatView;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoDetailResepturView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\InfoStokObatAlkesFnr;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\RiwayatAlergiView;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InformasiResepView;
use Doco\components\ConfigTrait;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\PenjualanResep;
use yii\helpers\ArrayHelper;
use Doco\Notifications\FarmasiNotification;
use Doco\models\NotifikasiFarmasi;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\KonfigRak;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\Rakobat;
use app\modules\v1\models\InfoDataKunjunganView;
use Doco\components\DocoConstansId;
use app\modules\v1\models\InformasiResepDetailView;
use Exception;

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
        $action_path = 'app\modules\v1\actions\Allow';
        $action = [
            'list-obat-pelayanan'   => $action_path . '\ListObatPelayananAction',
            'get-detail-obat'       => $action_path . '\GetDetailObatAction',
            'potong-stok'           => $action_path . '\PotongStokAction',
            'clear-notifications' => $action_path . '\ClearNotificationsAction',
        ];
        $actions = array_merge($actions,$action);
        return $actions;
    }

    /**
     * @todo get data resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer tipe resep
     */
    private function getDataResep($id = null)
    {
        $resep = InformasiResepturView::find()->select([
            'reseptur_id',
            'noresep',
            'tglreseptur',
            'nama_pasien',
            'instalasi_reseptur',
            'ruangan_reseptur',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pegawai',
            'pendaftaran_id',
            'carabayar_id',
            'penjamin_id',
            'pasien_id',
            'pasienadmisi_id',
            'penjualanresep_id',
            'diagnosa_nama',
            'status_reseptur_id',
            'status_reseptur',
            'tanggal_lahir',
            'carabayar_nama',
            'penjamin_nama',
            'catatan',
            'iter',
            'riwayat_alergi',
            'diagnosa_text',
            'biaya_administrasi',
            'berat_badan',
            'tinggi_badan',
            'nama_depan'
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
        $detail = InfoDetailResepturView::find(true)->where(['reseptur_id' => $id])
                    ->orderBy(['racikan_id'=>SORT_ASC, 'rke'=>SORT_ASC, 'resepturdetail_id'=>SORT_ASC, ]);
        return $detail;
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        $data_alergi = $data_resep = $detail_resep = [];
        $request = Yii::$app->request;
        $get = $request->get();
        $data_dokter = $this->getAllDataDokter()->asArray()->all();
        $data_pasien = $this->getAllDataPasien()->asArray()->all();
        $data_karyawan = Pegawai::find()->asArray()->all();
        $data_cara_bayar = CaraBayar::find()->asArray()->all();
        $data_penjamin = Penjamin::find()->asArray()->all();
        $data_pegawai = PegawaiView::find()->asArray()->all();
        $konfig = $this->getOrSetCache(DocoConstants::VAR_CACHE_KONFIG_FARMASI, $this->konfigFarmasi(), true);
        $konfigsys = $this->getOrSetCache(DocoConstants::VAR_K_S, $this->konfigSystem(), false);
        $data_stok = $data_resep = $detail_resep = $data_signa = [];
        $data_signa = SignaObat::find()->asArray()->all();
        if (isset($get['instalasi_id'])) {
            $data_stok = $this->getStokApotek($get['instalasi_id'], $get['ruangan_id'])->asArray()->all();
        }

        if (isset($get['reseptur_id'])) {
            $data_resep = $this->getDataResep($get['reseptur_id'])->asArray()->one();
            $detail_resep = $this->getAllDetailResep($get['reseptur_id'])->asArray()->all();
            $data_alergi = RiwayatAlergiView::find()->where([
                'pasien_id' => $data_resep['pasien_id']
            ])->all();
        }
        $data_obat_ruangan = $this->getObatRuangan($get['ruangan_id']);

        return [
            'data-resep' => $data_resep,
            'detail-resep' => $detail_resep,
            'data-dokter' => $data_dokter,
            'data-pasien' => $data_pasien,
            'data-alergi' => $data_alergi,
            'data-stok' => $data_stok,
            'data-cara-bayar' => $data_cara_bayar,
            'data-penjamin' => $data_penjamin,
            'data-pegawai' => $data_pegawai,
            'data-karyawan' => $data_karyawan,
            'data-signa' => $data_signa,
            'konfig' => $konfig,
            'konfigsys' => isset($konfigsys) ? $konfigsys : [],
            'data-obat-ruangan' => $data_obat_ruangan
        ];
    }

    public function actionGetBundleResepBebas(){
        $request = Yii::$app->request;
        $result = [ 'data-cara-bayar' => [],
                    'data-signa' => [],
                    'konfig' => [],
                    'konfigsys' => []
                ];
        try {
            $data_cara_bayar = CaraBayar::find()->asArray()->all();
            $konfig = $this->getOrSetCache(DocoConstants::VAR_CACHE_KONFIG_FARMASI, $this->konfigFarmasi(), true);
            $data_signa = SignaObat::find()->asArray()->all();
            $konfigsys = $this->getOrSetCache(DocoConstants::VAR_K_S, $this->konfigSystem(), true);
            $result = [ 'data-cara-bayar' => $data_cara_bayar,
                        'data-signa' => $data_signa,
                        'konfig' => $konfig,
                        'konfigsys' => isset($konfigsys[0]) ? $konfigsys[0] : []
                    ];
            return $result;
        } catch (Exception $e) {
            return $result;
        }
    }

    public function actionGetRs()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $konfig = $this->getOrSetCache(DocoConstants::VAR_CACHE_KONFIG_FARMASI, $this->konfigFarmasi(), true);
        $konfigsys = $this->getOrSetCache(DocoConstants::VAR_K_S, $this->konfigSystem(), true);
        $data_stok = $data_resep = $detail_resep = [];
        if (isset($get['instalasi_id'])) {
            $data_stok = $this->getStokApotek($get['instalasi_id'], $get['ruangan_id'])->asArray()->all();
        }

        if (isset($get['reseptur_id'])) {
            $data_resep = $this->getDataResep($get['reseptur_id'])->asArray()->one();
            $detail_resep = $this->getAllDetailResep($get['reseptur_id'])->asArray()->all();
        }
        return [
            'data-resep' => $data_resep,
            'detail-resep' => $detail_resep,
            'data-stok' => $data_stok,
            'konfig' => $konfig,
            'konfigsys' => isset($konfigsys[0]) ? $konfigsys[0] : []
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

    public function actionGetDetailStok(){
        $request = Yii::$app->request;
        try{
            $ruanganId = $request->get('ruangan_id',null);
            $obatAlkesId = $request->get('obatalkes_id',null);
            $penjaminId = $request->get('penjamin_id',null);
            $kelasPelayananId = $request->get('kelaspelayanan_id',null);
            if(is_null($ruanganId) || is_null($obatAlkesId) || is_null($penjaminId) || is_null($kelasPelayananId)){
                return [
                    'data' => [
                        'message' => 'Parameter harus terdiri atas ruangan_id, obatalkes_id, penjamin_id, kelaspelayanan_id',
                        'text' => 'Parameter harus terdiri atas ruangan_id, obatalkes_id, penjamin_id, kelaspelayanan_id',
                        'detail' => null
                    ]
                ];
            }
            $duration = 60;
            $detail = (new InfoStokObatAlkesFn(['extParam'=>[$penjaminId,$kelasPelayananId]]))->getDb()->cache(function ($db) use($penjaminId,$kelasPelayananId,$ruanganId,$obatAlkesId){
                $query = (new InfoStokObatAlkesFn(['extParam'=>[$penjaminId,$kelasPelayananId]]))->find()
                ->select([
                        'instalasi_id',
                        'obatalkes_id',
                        'obatalkes_kode',
                        'obatalkes_namalain',
                        'obatalkes_nama',
                        'qty_tersedia',
                        'ppn',
                        'hargaygdipakai as hargajual',
                        'satuankecil_id',
                        'satuankecil_nama',
                        'satuansedang_id',
                        'satuansedang_nama',
                        'satuanbesar_id',
                        'satuanbesar_nama',
                        'harganetto_ygdipakai as harganetto',
                        'ruangan_id',
                        'instalasi_id',
                        'hargaygdipakai',
                        'hn_diskon',
                        'hn_ppn',
                        'hn_margin',
                        'disc',
                        'ppn',
                        'margin',
                        'group_jenisobat',
                        'group_jenisobat_nama',
                        'jenisobatalkes_id',
                        'jenisobatalkes_nama'])
                ->where(['ruangan_id'=>$ruanganId,'obatalkes_id'=>$obatAlkesId])->asArray()->one();
                return $query;
            },$duration);
            return ['data'=>['message'=>'OK','text'=>'OK','detail'=>$detail]];
        }catch(\Exception $e){
            return ['data'=>[
                'message' => 'Terjadi Kesalahan',
                'text' => 'Terjadi Kesalahan',
                'detail'=>null
            ]];
        }
    }

    public function actionGetListObatRuangan(){
        try{
            $ruanganId = Yii::$app->request->get('ruangan_id',null);
            if(empty($ruanganId)) throw new \Exception("Ruangan ID tidak boleh kosong", 1);

            $data = $this->getObatRuangan($ruanganId);
            return ['data'=>$data];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'data'=>null
            ];
        }
    }

    private function getObatRuangan($ruangan_id){
        $duration = 60*10;
        return Yii::$app->db->cache(function($db)use($ruangan_id){
            $query = "
                    SELECT
                        stokobatalkes_r.obatalkes_id,
                        obatalkes_m.obatalkes_nama,
                        obatalkes_m.obatalkes_namalain,
                        stokobatalkes_r.qty_dipesan,
                        stokobatalkes_r.qty_tersedia,
                        stokobatalkes_r.qty_sisa
                    FROM
                        stokobatalkes_r
                    JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
                    JOIN obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
                    WHERE stokobatalkes_r.ruangan_id = {$ruangan_id}
                    ORDER BY obatalkes_m.obatalkes_nama ASC";
            return $db->createCommand($query)->queryAll();
        },$duration);
    }

    public function actionGetListStokApotek(){
        return Yii::$app->docoPlugin->execute('get_data_stok_apotek');
    }

    public function actionGetMultipleStokObat()
    {
        $request = Yii::$app->request;
        $result = [];
        try {
            $page = $request->get('page');
            $listobat = $request->get('listobat');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id',0);
            $query = $this->getStokApotekMarginFn($request->get('ruangan_id'), $request->get('penjamin_id'), $kelaspelayanan_id);
            if(is_array($listobat)){
                $query->andWhere(['IN','obatalkes_id',$listobat]);
            }
            $query->offset(($page-1)*10)->limit(11);
            $result = $query->all();
            return [
                'data' => $result,
                'payload' => $request->get()
            ];
        } catch (Exception $e) {
            return $result;
        }
    }

    public function actionGetListPasien(){
        $request = Yii::$app->request;
        try {
            $query = $this->getAllDataPasien();
            if(!empty($request->get('keyword') )){
                $keyword = $request->get('keyword');
                $query->andWhere(['like', 'LOWER(nama_pasien)', strtolower($keyword) ]);
                $query->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($keyword) ]);
            }
            $query->select(['pasien_id','nama_pasien','no_rekam_medik']);
            $query->orderBy(['nama_pasien' => SORT_ASC]);
            $query->groupBy(['pasien_id','nama_pasien','no_rekam_medik']);
            $result = $query->all();

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionGetListPegawai(){
        $request = Yii::$app->request;
        try {
            $query = PegawaiView::find();
            if(!empty($request->get('keyword') )){
                $keyword = $request->get('keyword');
                $query->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($keyword) ]);
            }
            $query->select(['pegawai_id','nama_pegawai']);
            $query->orderBy(['nama_pegawai' => SORT_ASC]);
            $query->groupBy(['pegawai_id','nama_pegawai']);
            $result = $query->all();

            return $result;
        } catch (Exception $e) {
            return [];
        }
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
        $dokter = DokterView::find()->select('distinct ON (pegawai_id) *');
        // if ($id) {
        //     $dokter->where('reseptur_id = :id', ['id' => $id]);
        // }

        return $dokter;
    }

    /**
     * @todo get all data stok apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer instalasi_id, ruangan_id
     */
    private function getStokApotek($instalasi_id = null, $ruangan_id = null, $obatalkes_id = null)
    {
        $stok = InfoStokObatAlkesView::find()->select([
            'instalasi_id',
            'obatalkes_id',
            'obatalkes_kode',
            'obatalkes_namalain',
            'obatalkes_nama',
            'qty_tersedia',
            'ppn',
            'hargaygdipakai as hargajual',
            'satuankecil_id',
            'satuankecil_nama',
            'satuansedang_id',
            'satuansedang_nama',
            'satuanbesar_id',
            'satuanbesar_nama',
            'harganetto_ygdipakai as harganetto',
            'ruangan_id',
            'instalasi_id',
            'hargaygdipakai',
            'hn_diskon',
            'hn_ppn',
            'hn_margin',
            'disc',
            'ppn',
            'margin',
        ]);

        if ($instalasi_id && $ruangan_id) {
            $stok->where(['instalasi_id' => $instalasi_id, 'ruangan_id' => $ruangan_id]);
        }

        if ($ruangan_id) {
            $stok->where(['ruangan_id' => $ruangan_id]);
        }

        if ($obatalkes_id && $ruangan_id) {
            $stok->where(['obatalkes_id' => $obatalkes_id, 'ruangan_id' => $ruangan_id]);
        }

        return $stok;
    }

    /**
     * @todo get all data stok apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer instalasi_id, ruangan_id
     */
    private function getKetersediaanObat($instalasi_id = null, $ruangan_id = null, $obatalkes_id = null)
    {
        $stok = KetersediaanObatView::find()->select('*');

        if ($instalasi_id && $ruangan_id) {
            $stok->where(['instalasi_id' => $instalasi_id, 'ruangan_id' => $ruangan_id]);
        }

        if ($ruangan_id) {
            $stok->where(['ruangan_id' => $ruangan_id]);
        }

        if ($obatalkes_id && $ruangan_id) {
            $stok->where(['obatalkes_id' => $obatalkes_id, 'ruangan_id' => $ruangan_id]);
        }

        return $stok;
    }

    private function getStokApotekMarginFn($ruangan_id = null, $penjamin_id = null, $kelaspelayanan_id = 0) {
        $stok = InfoStokObatAlkesFn::getInfoStokObatAlkesFn($penjamin_id, $kelaspelayanan_id);

        // if ($instalasi_id && $ruangan_id) {
        //     $stok->where(['instalasi_id' => $instalasi_id, 'ruangan_id' => $ruangan_id]);
        // }

        if ($ruangan_id) {
            $stok->where(['ruangan_id' => $ruangan_id]);
        }

        $stok->orderBy(['obatalkes_nama' => SORT_ASC]);
        return $stok;
    }

    /**
     * @todo get all data stok apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer instalasi_id, ruangan_id
     */
    // unused function; changed to getStokApotekMarginFn
    private function getStokApotekFn($instalasi_id = null,$ruangan_id = null)
    {
        $stok = InfoStokObatAlkesFn::find()->select([
            'instalasi_id',
            'obatalkes_id',
            'obatalkes_kode',
            'obatalkes_namalain',
            'obatalkes_nama',
            'qty_tersedia',
            'ppn',
            'hargaygdipakai as hargajual',
            'satuankecil_id',
            'satuankecil_nama',
            'satuansedang_id',
            'satuansedang_nama',
            'satuanbesar_id',
            'satuanbesar_nama',
            'harganetto_ygdipakai as harganetto',
            'ruangan_id',
            'instalasi_id',
            'hargaygdipakai',
            'hn_diskon',
            'hn_ppn',
            'hn_margin',
            'disc',
            'ppn',
            'margin',
        ]);

        if ($instalasi_id && $ruangan_id) {
            $stok->where(['instalasi_id' => $instalasi_id, 'ruangan_id' => $ruangan_id]);
        }

        if ($ruangan_id) {
            $stok->where(['ruangan_id' => $ruangan_id]);
        }

        $stok->orderBy(['obatalkes_nama' => SORT_ASC]);
        return $stok;
    }

    public function actionListStokApotek()
    {
        $model = new InfoStokObatAlkesView;
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi_id = isset($get['instalasi_id']) ? $get['instalasi_id'] : null;
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
        $term = isset($get['term']) ? $get['term'] : null;
        $page = isset($get['page']) ? $get['page'] : 1;

        $query = $this->getKetersediaanObat($instalasi_id, $ruangan_id);

        if (isset($_GET['advanced-filter']['obatalkes_namalain'])) {
            $obat_alkes_nama = $request->get('advanced-filter')['obatalkes_namalain'];
            $query->andFilterWhere(['ILIKE', 'obatalkes_namalain', $obat_alkes_nama]);
        }
        if ($term) {
            $query->andFilterWhere(['ILIKE', 'obatalkes_nama', $term]);
        }

        $perpage = 10;
        $limit = 11;

        $offset = ($page - 1) * $perpage;

        $query->offset($offset)->limit($limit);

        $keyObat = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $keyObat[] = $value['obatalkes_id'];
        }
        $satuan = $this->getSatuanKonversi($keyObat);
        $result['data'] = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $value['satuan'] = isset($satuan[$value['obatalkes_id']]) ? $satuan[$value['obatalkes_id']] : [];
            $result['data'][] = $value;
        }
        return $result;
    }

    public function actionGetStokRuangan($obatalkes_id, $ruangan_id)
    {
        $result = $this->getStokApotek(null, $ruangan_id, $obatalkes_id)->asArray()->one();
        return $result;

    }

    public function actionListObatAlkes()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
        $page = isset($get['page']) ? $get['page'] : 1;
        $is_produksi = isset($get['is_produksi']) ? $get['is_produksi'] : null;

        $listObat = ObatAlkesView::find()->select([
            'obatalkes_id',
            'obatalkes_nama',
            'obatalkes_kode',
            'satuankecil_id',
            'satuankecil_nama',
            'satuanbesar_id',
            'satuanbesar_nama',
            'harganetto_ygdipakai'
        ]);

        if ($is_produksi) {
            $listObat->andWhere(['is_produksi' => $is_produksi]);
        }

        if ($term) {
            $listObat->andFilterWhere(['ILIKE', 'obatalkes_nama', $term]);
            $listObat->orFilterWhere(['ILIKE', 'obatalkes_kode', $term]);
        }

        $perpage = 10;
        $limit = 11;

        $offset = ($page - 1) * $perpage;

        $listObat->offset($offset)->limit($limit);

        $keyObat = [];
        foreach ($listObat->asArray()->all() as $value) {
            $keyObat[] = $value['obatalkes_id'];
        }
        $satuan = $this->getSatuanKonversi($keyObat, $is_produksi);
        $result['data'] = [];
        foreach ($listObat->asArray()->all() as $key => $value) {
            $value['satuan'] = isset($satuan[$value['obatalkes_id']]) ? $satuan[$value['obatalkes_id']] : [];
            $value['qty_tersedia'] = 0;
            $value['harganetto'] = $value['harganetto_ygdipakai'];
            $result['data'][] = $value;
        }
        return $result;
    }

    public function getSatuanKonversi($id, $is_produksi = false)
    {
        $result = [];
        $satuan = SatuanKonversiView::find()->select([
            'obatalkes_id',
            'satuan_besar',
            'satuanbesar_id',
            'satuankecil_id',
            'nilai_konversi'
        ])->where([
            'jenis'=>'obat',
            'obatalkes_id' => $id,
            'is_active' => 1
        ]);

        if ($is_produksi) {
            $satuan->andWhere(['nilai_konversi' => 1]);
        }

        $satuan = $satuan->asArray()->all();
        foreach ($satuan as $key => $value) :
            $result[$value['obatalkes_id']][] = $value;
        endforeach;
        return $result;
    }
    /**
    * @author yaya
    * Set cache buat konversi satuan
    * @return array
    * @example data <id-satuan-besar> [
    * ..
    * <id-satuan-kecil> => <nilai-konversi>
    *]
    **/

    public static function actionSetCacheKonvertSatuan()
    {
        $cacheSatuan = Yii::$app->cache->get(DocoConstants::KONV_SATUAN);
        if ($cacheSatuan === false) {
            $satuanKonv = SatuanKonversi::find()->all();
            $konvSatuan = [];
            foreach ($satuanKonv as $value) {
                $konvSatuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
            }
            Yii::$app->cache->set(DocoConstants::KONV_SATUAN,$konvSatuan,DocoConstants::DURATION);
            $cacheSatuan = $konvSatuan;
        }
        return $cacheSatuan;
    }

    public static function actionResetCacheKonvertSatuan()
    {
        $cacheSatuan = Yii::$app->cache->get(DocoConstants::KONV_SATUAN);
        if($cacheSatuan !== false){
            Yii::$app->cache->delete(DocoConstants::KONV_SATUAN);
            $satuanKonv = SatuanKonversi::find()->all();
            $konvSatuan = [];
            foreach ($satuanKonv as $value) {
                $konvSatuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
            }
            Yii::$app->cache->set(DocoConstants::KONV_SATUAN,$konvSatuan,DocoConstants::DURATION);
        }

        return ['message'=>'Cache Telah direset'];
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
            $result->select([
                'obatalkes_id',
                'obatalkes_namalain'
            ]);
            $term = $post['term'];
            $result->where(['like', 'LOWER(obatalkes_namalain)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
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

    /**
     * @todo check stok
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer ruangan_id, obatalkes_id
     * @last-edited-by: Rizqi Fitrianto
     * change tablte target into stokobatalkes_r and remove obatalkes_namalain
     */
    public function actionCheckStok($ruangan_id = null, $obatalkes_id = null)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT obatalkes_id, qty_tersedia FROM infostokobatalkes_v";

        if ($ruangan_id && $obatalkes_id) {
            $sql .=  " WHERE ruangan_id = {$ruangan_id} AND obatalkes_id = {$obatalkes_id}";
        }

        $stok = $connection->createCommand($sql)->queryOne();

        return !empty($stok) ? $stok : [];
    }

    /**
     * @todo  konfig farmasi
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function konfigFarmasi()
    {
        $model = KonfigFarmasi::find();

        return $model;
    }

    public function actionKonfigFarmasi()
    {
        return $this->konfigFarmasi()->one();
    }

    public function actionLookupTransaksi($kode)
    {
        $model = LookupTransaksi::find()
            ->select(['kode_transaksi', 'kode_id'])
            ->where(['kode_transaksi' => $kode])->one();
            
        return $model;
    }

    public function actionGetLookupTransaksi($kode)
    {
        return DocoConstansId::actionGetId($kode);
    }

    /**
     * @todo  konfig system
     * @author Rizqi Febian
     */
    public function konfigSystem()
    {
        $model = KonfigSystem::find();
        return $model;
    }
    public function actionAutoObat()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $result = $this->getObatAlkes();
            $result->select(['obatalkes_id', 'obatalkes_nama']);
            if (!empty($post['term'])) {
                $term = $post['term'];
                $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);
            }
            return $result->asArray()->all();

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'carabayar_m');

        $model = new Penjamin;
        $query = $model::find()
            ->joinWith(['caraBayar' => function($query){
                $query->from('carabayar_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionGetCarabayar()
    {
        $model = new CaraBayar;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
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

    public function actionGetInstalasi($state = true)
    {
        $connection = Yii::$app->db;
        try {

            //get data instalasi
            $data['instalasi'] = Instalasi::find()->where(['is_active' => true, 'is_deleted'=>false])->all();
            //get data periode stok
            $sql = "SELECT periodestokobat_id, periodestok_nama FROM periodestokobat_m WHERE is_active=true AND is_deleted=false ";
            if($state){
                $periode_stok = $connection->createCommand($sql)->queryAll();
                $data['periode'] = $periode_stok;
            }
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionGetRuangan($state = true)
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = new Ruangan;
        $query = $model::find();
        if(isset($get['instalasi_id'])) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['instalasi_id']]);
            $query->andWhere(['is_active' => true, 'is_deleted'=>false]);
        }

        if(isset($get['id'])) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['id']]);
            $query->andWhere(['is_active' => true, 'is_deleted'=>false]);
        }
        if($state){
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }else{
            return ['data' => $query->asArray()->all()];
        }
    }

    public function actionGetRuanganByName()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi = Instalasi::findOne($get['instalasi_id']);

        $model = new Ruangan;
        $query = $model::find();
        if ($get['instalasi_id']) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['instalasi_id']]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetApi()
    {
        try {
            // get all master by request
            $listRequestMaster = [
                'instalasi' => 'Instalasi',
                'ruangan' => 'Ruangan',
                'carabayar' => 'CaraBayar',
                'penjamin' => 'Penjamin',
            ];
            $master = $this->getListMaster($listRequestMaster);

            $listLookup = [
                'status_pesan',
                'status_mutasi',
            ];

            $lookup = $this->listLookup($listLookup);

            $result = [
                'master' => $master,
                'lookup' => $lookup
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

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key=>$className) {
            $class = "app\modules\\v1\models\\" . $className;
            $model = new $class;
            $q = $model->find();
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
        }
        return $results;
    }

    public function actionGetInstalasiBy($id)
    {
        $sql = 'SELECT t.*
            FROM
                instalasi_m t
            JOIN ruangan_m r ON r.instalasi_id = t.instalasi_id
            WHERE r.ruangan_id = '.$id.'
        ';

        $result = Instalasi::findBySql($sql)->all();

        return $result;
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => $id]);

        return $data->all();
    }

    public function actionGetResepturAntrian($no_rekam_medik = null)
    {
        $data = InformasiResepturView::find()
                ->where(['no_rekam_medik' => $no_rekam_medik])
                ->andWhere(['penjualanresep_id' => null])
                ->andWhere(['antrian_id' => null]);

        return ['data' => $data->all()];
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

    public function actionSearchDataPasien($no_pendaftaran = NULL) {
        $data = InfoDataPendaftaranView::find()->where(['like', 'LOWER(no_pendaftaran)', strtolower($no_pendaftaran)]);
        return ['data' => $data->all()];
    }

    public function actionSearchDataPendaftaran($term = NULL) {
        $request = Yii::$app->request;
        try {
            $page = $request->get('page', 1);
        } catch (\Exception $e) {

        }

        $data = InfoDataKunjunganView::find()
            ->where(['like', 'LOWER(no_pendaftaran)', strtolower($term)])
            ->orWhere(['like', 'LOWER(nama_pasien)', strtolower($term)])
            ->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($term)])
            ->andWhere('pendaftaran_id IS NOT NULL');
        $data->offset( ($page-1)*10 )->limit(10);
        return ['data' => $data->asArray()->all()];
    }

    public function actionSearchDataMasterPasien($term = NULL) {
        $request = Yii::$app->request;
        try {
            $page = $request->get('page', 1);
        } catch (\Exception $e) {

        }

        $data = PasienView::find()
            ->where(['like', 'LOWER(nama_pasien)', strtolower($term)])
            ->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($term)]);
        $data->offset( ($page-1)*10 )->limit(10);
        return ['data' => $data->asArray()->all()];
    }

    public function actionSearchDataKaryawan($term = NULL) {
        $request = Yii::$app->request;
        try {
            $page = $request->get('page', 1);
        } catch (\Exception $e) {

        }
        $data = PegawaiMasterView::find()
            ->where(['like', 'LOWER(nomorindukpegawai)', strtolower($term)])
            ->orWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)])
            ->andWhere('pegawai_id IS NOT NULL');
        $data->offset(($page-1)*10)->limit(10);
        return ['data' => $data->all()];
    }

    public function actionInfoResep()
    {
        try{
            $get = Yii::$app->request->get();
            $data = InfoResepView::find()
                ->where(['nomor'=>$get['nomor']])
                ->orWhere(['no_resep' => $get['nomor']]);
            $info_resep = $data->one();
            $reseptur_id = $info_resep['reseptur_id'];
            $data_racikan = is_null($reseptur_id) ? [] : ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all();

            if(is_null($info_resep)){
                throw new \Exception("Data Resep Tidak Ada", 1);
            }

            return [
                'data' => $info_resep,
                'data_racikan' => $data_racikan
            ];
        } catch(\Exception $e){
            return [
                'data' => [],
                'data_racikan' => []
            ];
        }
    }

    // Kebutuhan di fitur lihat detail reseptur pasien
    // Pengembangan action inforesep dengan menyesuaikan informasi kebutuhan
    public function actionInfoResepV2()
    {
        try{
            $get = Yii::$app->request->get();
            $data = InformasiResepView::find()
                ->where(['nomor'=>$get['nomor']])
                ->orWhere(['no_resep' => $get['nomor']]);
            $info_resep = $data->one();

            if(is_null($info_resep)){
                throw new \Exception("Data Resep Tidak Ada", 1);
            }

            $reseptur_id = $info_resep['reseptur_id'];
            $data_racikan = is_null($reseptur_id) ? [] : ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all();


            $riwayat_personal = null;
            if(isset($info_resep['pasien_id'])){
                $riwayat_personal = Yii::$app->db->createCommand("SELECT catatanpenting_pasien, riwayat_penyakit, riwayat_obat, riwayat_alergi FROM riwayatpersonalpasien_v WHERE pasien_id = :pasien_id")->bindValue(":pasien_id",$info_resep['pasien_id'])->queryOne();
            }
            $pendaftaran_id = isset($info_resep['pendaftaran_id']) ? $info_resep['pendaftaran_id'] : null;

            if(isset($pendaftaran_id) && !empty($pendaftaran_id)){
                $info_pasien = InfoDataKunjunganView::find()
                            ->select(["berat_badan","tinggi_badan","pendaftaran_id"])
                            ->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            }else{
                $info_pasien = [
                    'tinggi_badan' => '-',
                    'berat_badan' => '-',
                    'pendaftaran_id' => null
                ];
            }

            $konfig_kronis = KonfigFarmasi::find()->select((["enable_split_kronis"]))->one();


            return [
                'data' => $info_resep,
                'data_racikan' => $data_racikan,
                'info_pasien' => $info_pasien,
                'konfig_farmasi' => $konfig_kronis,
                'riwayat_personal' => $riwayat_personal,
            ];
        } catch(\Exception $e){
            return [
                'data' => [],
                'data_racikan' => [],
                'info_pasien' => [],
                'konfig_farmasi' => null,
                'riwayat_personal' => null,
            ];
        }
    }

    public function actionViewInfoResep()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = $get['id'];
            $data_penjualanresep = $this->getDataPenjualanResep($id, $get['jenis']);

            $signa = [];
            $signa_ids = $request->get('signa_ids', []);
            if (!empty($signa_ids)) {
                $signa = SignaObat::find()->select(['signa_id', 'signa_nama', 'qty_obat', 'iterasi']);
                $signa = $signa->where(['IN', 'signa_id', $signa_ids])->asArray()->all();
            }

            $kode_transaksi = null;
            if ($get['kode']) {
                $kode_transaksi = LookupTransaksi::find()
                    ->select(['kode_transaksi', 'kode_id'])
                    ->where(['kode_transaksi' => $get['kode']])->one();
            }

            return [
                'data_penjualanresep' => $data_penjualanresep,
                'master_signa' => $signa,
                'kode_transaksi' => $kode_transaksi,
            ];
        } catch(\Exception $e) {
            Yii::error($e);
            return [
                'data_penjualanresep' => [],
                'master_signa' => [],
                'kode_transaksi' => [],
            ];
        }
    }

    protected function getDataPenjualanResep($id, $jenis = 'resep')
    {
        $data_penjualanresep = PenjualanResep::find()->select(['reseptur_id','penjualanresep_id','carabayar_id','penjamin_id','pegawai_id','resep_kronis_asal_id','hasil_resep_kronis_id','reseptur_kronis_asal_id']);
        if ($jenis == 'resep') {
            $data_penjualanresep = $data_penjualanresep->where(['penjualanresep_id' => $id])->one();
        } else {
            $data_penjualanresep = $data_penjualanresep->where(['reseptur_id' => $id])->one();
        }
        return $data_penjualanresep;
    }

    public function actionGetResepByResepturId($reseptur_id)
    {
        $resep = InfoResepView::find()->where(['reseptur_id'=>$reseptur_id])->one();
        return $resep;
    }

    public function actionUpdateNotif() {
        FarmasiNotification::updateNotif();
        return $this->responseJson(200, 'Notifikasi farmasi terinisiasi.');
    }

    public function actionReadNotif() {
        $notifikasi_id = Yii::$app->request->get('notifikasi_id', null);
        if (!empty($notifikasi_id)) {
            NotifikasiFarmasi::updateAll(['is_read' => true], compact('notifikasi_id'));
            FarmasiNotification::updateNotif();
            return $this->responseJson(200, 'Status Notifikasi berhasil diperbarui');
        } else {
            return $this->responseJson(400, 'ID Notifikasi tidak boleh kosong');
        }
    }

    public function actionDeleteCacheByKey($key) {
        Yii::$app->cache->delete($key);
        return $this->responseJson(200, 'Cache '.$key.' berhasil di hapus');
    }

    public function actionDeleteCacheByTag($tag) {
        \yii\caching\TagDependency::invalidate(Yii::$app->cache, $tag);
        return $this->responseJson(200, 'Cache '.$tag.' berhasil di hapus');
    }

    public function actionGetJenisObatAlkes() {
        $model = new JenisObatAlkes;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    /**
     * @author Lukman Hakim
     * @since 2021-03-19 15:29:02
     * @param integer obatalkes_id, ruangan_id
     * @return array $data:
     */

    public function actionGetKonfigRak($obatalkes_id, $ruangan_id)
    {
        $data = KonfigRak::find()
            ->select(["obatalkes_id", "ruangan_id", "min_stok", "max_stok"])
            ->where(['obatalkes_id' => $obatalkes_id, 'ruangan_id' => $ruangan_id])
            ->asArray()
            ->one();

        return $data;
    }

    public function actionGetRakobat($type = null, $ruangan_id = null)
    {
        $model = new Rakobat;
        $query = $model::find();

        if($ruangan_id != null) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        // $type
        // type 1 digunakan untuk mengambil data rakobat_m yang tidak memiliki parent (rak obat).
        // type 2 digunakan untuk mengambil data rakobat_m yang memiliki parent (laci obat).
        // default tanpa type digunakan untuk mengambil keseluruhan data rakobat_m.

        switch($type) {
            case "1":
                $query->andWhere(['parentrakobat_id' => null]);
                break;
            case "2":
                $query->andWhere(['not', ['parentrakobat_id' => null]]);
                break;
            default:
                break;
        };

        return ['data' => $query->orderBy(['rakobat_nama'=>SORT_ASC])->asArray()->all()];
    }

    public function actionCheckStokNew($ruangan_id = null, $obatalkes_id = null)
    {
        $sql = null;
        if ($ruangan_id && $obatalkes_id) {
            $sql = KetersediaanObatView::find()->where(['ruangan_id'=>$ruangan_id,'obatalkes_id'=>$obatalkes_id])->one();
        
        }
        return !empty($sql) ? $sql : [];
    }

    public function actionListKetersediaanStokObat() 
    {
        $request    = Yii::$app->request;
        $get        = $request->get();
        $term       = ArrayHelper::getValue($get, 'term');
        $ruangan_id = ArrayHelper::getValue($get, 'ruangan_id');
        $page       = ArrayHelper::getValue($get, 'page');

        $model = new KetersediaanObatView;
        $query = $model::find()
        ->select([
            'ruangan_id','obatalkes_id',
            'obatalkes_nama','obatalkes_kode',
            'satuankecil_id','satuankecil_nama',
            'satuanbesar_id','satuanbesar_nama',
            'harganetto','qty_tersedia'
        ])->where(['ruangan_id' => $ruangan_id]);
        
        if ($term) {
            $query->andFilterWhere(['ILIKE', 'obatalkes_nama', $term]);
            $query->orFilterWhere(['ILIKE', 'obatalkes_kode', $term]);
        }

        $perpage = 10;
        $limit = 11;

        $offset = ($page - 1) * $perpage;

        $query->offset($offset)->limit($limit);

        $keyObat = ArrayHelper::getColumn($query->asArray()->all(), 'obatalkes_id');
        $satuan  = $this->getSatuanKonversi($keyObat);
        $result['data'] = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $value['satuan'] = isset($satuan[$value['obatalkes_id']]) ? $satuan[$value['obatalkes_id']] : [];
            $result['data'][] = $value;
        }

        return $result;
    }

    public function actionListSigna()
    {
        $request = Yii::$app->request;
        $term  = Yii::$app->request->get('term', null);
        $page  = Yii::$app->request->get('page', 1);
        $limit      = DocoConstants::LIMIT_INFINITY_SCROLL + 1;
        $offset     = ($page - 1) * $limit;
        
        $result = SignaObat::find()->select([
            'signa_id',
            'signa_nama',
            'signa_kode',
            'qty_obat',
            'iterasi',
        ]);
        $result->andWhere([
            'OR', 
            ['ILIKE', 'LOWER(signa_nama)', strtolower($term)],
            ['ILIKE', 'LOWER(signa_kode)', strtolower($term)]
        ]);
        $result->andWhere(['is_active' => true]);
        return $result->limit($limit)->offset($offset)->all();
    }

    public function actionGetMoreNotifications() {
        $request = Yii::$app->request;
        $last_id  = Yii::$app->request->get('last_id', null);
        $limit  = Yii::$app->request->get('limit', 1);

        $result = NotifikasiFarmasi::find();
        $result->andWhere(['<', 'notifikasi_id', $last_id]);
        $result->andWhere(['is_active' => true, 'is_deleted' => false]);
        $result->orderBy('notifikasi_id DESC');
        $result->limit($limit);

        return $result->asArray()->all();
    }

    public function actionGetLookup($type){
        $lookup = new Lookup;
        $results = $lookup->find()->where(['lookup_type' => $type, 'is_active' => true, 'is_deleted' => false]);
        
        return ['data' => $results->asArray()->all()];
    }

    /* detail resep filler resep */
    public function actionGetInfoResepData($id_name, $id_value, $has_reseptur, $pasien_id = null, $pendaftaran_id = null, $cache_duration = 2)
    {
        try {
            $selects = ['dokter_nama', 'tanggal_lahir'];
            $data = self::getInfoResepData($id_name, $id_value, $has_reseptur, $cache_duration);
            if (!empty($data) && $data['jenispenjualan'] == DocoConstants::PENJUALAN_RESEP_KARYAWAN) {
                $info_pasien = isset($data['karyawan_id']) && empty($data['karyawan_id']) ? [] : self::getInfoKaryawan($data['karyawan_id'], $selects, $cache_duration);
                $riwayat_personal = [];
            } else if (!empty($data) && ($data['jenispenjualan'] == DocoConstants::PENJUALAN_RESEP_RS || $has_reseptur || !empty($pasien_id))) {
                $info_pasien = is_null($pasien_id) ? [] : self::getInfoDataKunjunganPasien($pasien_id, $pendaftaran_id, $selects, $cache_duration);
                $riwayat_personal = is_null($pasien_id) ? [] : TransaksiResepController::getRiwayatPersonalPasien($pasien_id, $cache_duration);
            } else { // resep bebas atau data kosong
                $info_pasien = [];
                $riwayat_personal = [];
            }

            // digabungin ke data resep untuk kebutuhan cache generate kronis
            if (!empty($data)) {
                $data['nosep'] = isset($data['nosep']) && !empty($data['nosep']) ? $data['nosep'] : ArrayHelper::getValue($info_pasien, 'nosep', null);
                $data['nama'] = isset($data['nama_pembeli']) && !empty($data['nama_pembeli']) ? $data['nama_pembeli'] : ArrayHelper::getValue($info_pasien, 'nama', null);
                $data['carabayar_id'] = isset($data['carabayar_id']) && !empty($data['carabayar_id']) ? $data['carabayar_id'] : ArrayHelper::getValue($info_pasien, 'carabayar_id', null);
                $data['penjamin_id'] = isset($data['penjamin_id']) && !empty($data['penjamin_id']) ? $data['penjamin_id'] : ArrayHelper::getValue($info_pasien, 'penjamin_id', null);
            }

            return [
                'data' => $data,
                'info_pasien' => $info_pasien,
                'riwayat_personal' => $riwayat_personal
            ];
        } catch(Exception $e) {
            Yii::error($e);
            return [
                'data' => [],
                'riwayat_personal' => [],
                'info_pasien' => [],
                'data_alergi' => [],
            ];
        }
    }

    public static function getInfoResepData($column, $value, $has_reseptur, $duration = 5, $tagName = null)
    {
        if ($has_reseptur) {
            return Reseptur::getDb()->cache(function ($db) use ($column, $value) {
                return Reseptur::getResepturData($column, $value);
            }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
        } else {
            return PenjualanResep::getDb()->cache(function ($db) use ($column, $value) {
                 return PenjualanResep::getResepData($column, $value);
            }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
            return PenjualanResep::getResepData($value);
        }
    }

    public static function getInfoDataKunjunganPasien($pasien_id, $pendaftaran_id, $selects = [], $duration = 5, $tagName = null)
    {
        // $default_select = ['pendaftaran_id', 'berat_badan', 'tinggi_badan'];
        // if (!empty($selects)) {
        //     $selects = is_array($selects) ? $selects : [$selects];
        // }
        // $selects = array_merge($selects, $default_select);
        // $where = ['pasien_id' => $pasien_id];
        if (!empty($pendaftaran_id)) {
            $where = ' pasien_m.pasien_id = '.$pasien_id.' AND pendaftaran_t.pendaftaran_id = '.$pendaftaran_id;

            return Pasien::getDb()->cache(function ($db) use ($selects, $where) {
                return Pasien::getInfoPasien($where);
            }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
        } else {
            return Pasien::getDb()->cache(function ($db) use ($selects, $pasien_id) {
                 return Pasien::find()->select(['no_rekam_medik', 'nama_pasien', 'CONCAT(nama_depan.lookup_value,\' \',nama_pasien) AS nama', 'alamat_pasien', 'tanggal_lahir', 'no_telepon_pasien'])
                 ->leftJoin('lookup_m nama_depan', 'nama_depan.lookup_id = pasien_m.namadepan::int')
                 ->where(['pasien_id' => $pasien_id])->asArray()->one();
            }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
        }
    }


    public static function getInfoKaryawan($karyawan_id, $selects = [], $duration = 5, $tagName = null)
    {
        return Pegawai::getDb()->cache(function ($db) use ($selects, $karyawan_id) {
            return Pegawai::find()->select(['CONCAT(gelar_depan.lookup_value,\' \',nama_pegawai) AS nama', 'nama_pegawai as nama_pasien', 'tgl_lahirpegawai as tanggal_lahir', 'alamat_pegawai as alamat_pasien'])
                ->leftJoin('lookup_m gelar_depan', 'gelar_depan.lookup_id = pegawai_m.gelardepan::int')
                ->where(['pegawai_id' => $karyawan_id])->asArray()->one();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getKonfigFarmasi($selects, $duration = 5, $tagName = null)
    {
        return KonfigFarmasi::getDb()->cache(function ($db) use ($selects) {
            if (empty($selects)) {
                return KonfigFarmasi::findOne(1);
            } else {
                return KonfigFarmasi::find()->select(is_array($selects) ? $selects : [$selects])->one();
            }
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getKonfigSystem($selects, $duration = 5, $tagName = null)
    {
        return KonfigSystem::getDb()->cache(function ($db) use ($selects) {
            if (empty($selects)) {
                return KonfigSystem::findOne(1);
            } else {
                return KonfigSystem::find()->select(is_array($selects) ? $selects : [$selects])->one();
            }
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public function actionListHistoryResep()
    {
        $pasienId = Yii::$app->request->get('pasien_id', null);
        $no_resep = Yii::$app->request->get('no_resep', null);
        $limit = Yii::$app->request->get('length', 10);
        $offset = Yii::$app->request->get('start', 0);
        
        $getResepturHeader = \Doco\models\HistoryResepView::find()->select([
            'reseptur_id',
            'resep_id',
            'pasien_id',
            'pendaftaran_id',
            'no_reseptur',
            'nomor',
            'tglreseptur',
            'tglresep',
            'COALESCE(tglreseptur, tglresep) as tgl_resep',
            'nama_pegawai',
            'instalasi_reseptur',
            'ruangan_reseptur',
            'instalasi_resep',
            'ruangan_tujuan',
            'kategori_resep',
            'kategori_resep_nama',
            'kategori_resep_kode' 
        ]);
        if(!empty($pasienId)){
            $getResepturHeader = $getResepturHeader->andWhere([
                'pasien_id' => $pasienId
            ]);
        }else{
            $getResepturHeader = $getResepturHeader->andWhere([
                'nomor' => $no_resep
            ]);
        }
        $getResepturHeader = $getResepturHeader->orderBy([
                'tgl_resep' => SORT_DESC
        ]);

        $totalDataCount = $getResepturHeader->count();

        $data = $getResepturHeader->limit($limit)->offset($offset)->asArray()->all();
        // return $no_resep;
        if ( $data ) {
            $nomorResep = ArrayHelper::getColumn($data, 'nomor');
            $resepturIds = ArrayHelper::getColumn($data, 'reseptur_id');

            $getDetailResep = \Doco\models\DetailResepObatalkesView::find();
            $getDetailResep = $getDetailResep->select([
                'noresep',
                'reseptur_id',
                'racikan_id',
                'obatalkes_id',
                'obatalkes_nama',
                'satuan_kecil',
                'qty_reseptur',
                'qty_transaksi',
                'det_transaksi',
                'qty_racikan',
                'signa_nama',
                'satuan_racikan_nama',
                'racikan_nama',
                'tgl_update_resep',
                'user_update_resep',
                'kelompokpegawai_user_resep',
                'nama_racikan',
                'rke',
                'is_kronis',
            ]);
            
            $getDetailResep = $getDetailResep->andWhere(['IN', 'noresep', $nomorResep])->orderBy(['racikan_nama' => SORT_ASC])->asArray()->all();
            $getReseptur = \Doco\models\ResepturDetail::find()->select([
                'reseptur_id',
                'obatalkes_id',
            ])->andWhere([
                'IN', 'reseptur_id', $resepturIds
            ])->andWhere(['racikan_id' => 1])->asArray()->all();

            $getResepturRacikan = \Doco\models\ResepturRacikan::find()
                ->andWhere(['reseptur_id' => $resepturIds])
                ->orderBy('rke ASC')
            ->asArray()->all();

            $newDataResep = [];
            foreach($getDetailResep as $key => $value){
                $newDataResep[] = $value;
            }

            $reindexingDetailResep = ArrayHelper::index($newDataResep, null, 'noresep');
            $reindexingRacikan = ArrayHelper::index($getResepturRacikan, null, 'reseptur_id');
            $reindexingResepturDetailRacikan = ArrayHelper::index($getReseptur, null, 'reseptur_id');
            foreach( $data as $key => $value) {
                $sort = ArrayHelper::getValue($reindexingDetailResep, $value['nomor'], []);
                $key_values = array_column($sort, 'tgl_update_resep');
                array_multisort($key_values, SORT_DESC, $sort);
                $data[$key]['detail_resep'] = ArrayHelper::getValue($reindexingDetailResep, $value['nomor'], []);
                $data[$key]['reseptur_racikan'] = ArrayHelper::getValue($reindexingRacikan, $value['reseptur_id'], []);
                $data[$key]['reseptur_detail_racikan'] = ArrayHelper::getValue($reindexingResepturDetailRacikan, $value['reseptur_id'], []);
                $data[$key]['last_update'] = (isset($sort[0]) && $sort[0]['kelompokpegawai_user_resep'] == DocoConstants::KELOMPOK_PEGAWAI_KEFARMASIAN) ? $sort[0]['tgl_update_resep'] : NULL;
            }
        }

        return [
            'data' => $data,
            '_meta' => [
                'totalCount' => $totalDataCount
            ]
        ];
    }
}
