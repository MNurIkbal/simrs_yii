<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\Repositories\RegionRepositories;
use Doco\Traits\RegionTrait;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\InfoTarifAkomodasiView;
use app\modules\v1\models\InfoTarifPenunjangView;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\RujukanDari;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Suku;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\KasuspenyakitruanganV;
use app\modules\v1\models\RuanganPelayananV;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PasienPulangRdRjView;
use app\modules\v1\models\Kabupaten;
use app\modules\v1\models\Kecamatan;
use app\modules\v1\models\Kelurahan;
use app\modules\v1\models\Loket;
use app\modules\v1\models\PaketTindakanLabView;
use app\modules\v1\models\TarifTindakanLabView;
use app\modules\v1\models\InfoPasienIbuBayi;
use app\modules\v1\models\PaketTindakanRadView;
use app\modules\v1\models\TarifTindakanRadView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\CekTagihanView;
use app\modules\v1\models\PerujukView;
use app\modules\v1\models\AsuransiPasien;
use app\modules\v1\models\KategoriTindakan;
use app\modules\v1\models\SyncPasien;
use app\modules\v1\models\SyncPendaftaranTransaksi;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\InfoPasienIbuBayiHeaderView;
use app\modules\v1\models\InfoPasienIbuBayiView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\payload\PendaftaranForm;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\models\PasienAsuransiView;
use app\modules\v1\models\InfoTarifKamarView;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\InfRencanaKontrol;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\JadwalPoliklinik;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\SyRuanganCaraBayarV;
use app\modules\v1\models\InfoMultipayerView;
use app\modules\v1\models\KetersediaanKamar;
use app\modules\v1\models\InfoPasienRujukRanapView;
use app\modules\v1\models\SyKeluargaPasienView;
use yii\base\DynamicModel;
use app\modules\v1\models\PenjaminGradeView;
use app\modules\v1\models\KlasifikasiKamar;
use app\modules\v1\models\JadwalCuti;
use app\modules\v1\models\TindakanPelayanan;
use Doco\models\LookupTransaksi;

use yii\helpers\Html;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use Doco\models\PeranPengguna;

use app\modules\v1\cache\Cache;
use Doco\components\DocoConstansId;
use app\modules\v1\models\MasterPaketMcuView;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\InfoRegisV;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TarifTotalKamarRsFn;
use app\modules\v1\payload\TarifPayloadTindakan;
use Doco\Services\RegistrationService;
use Doco\Libraries\Asuransi\Models\KonfigAsuransi;
use app\modules\v1\models\InfoPendaftaranOlView;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;
    use RegionTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["bpjsReferensiPoli"] = ["POST", "GET"];
        $verbs["get-pasien-rekam-medik"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['auto-reject-pendaftaran-ol'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['auto-reject-pendaftaran-ol'],
        ];

        return $behaviors;
    }

    public function actionGetCarabayarBpjs()
    {
        $model = new CaraBayar;
        $query = $model::find()->where(['groupcarabayar_id'=>DocoConstants::GROUP_BPJS])->one();
        return $query;
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
    * @author Rizqi Febian
    * @since 2018-04-03 13:40:02
    * @param
    * @return array $results :
    * @desc
    */
    public function listLookup($types){
        $results = [];
        $lookup = new Lookup;
        $getLookup = $lookup->find()->where(['lookup_type'=>$types, 'is_active' => true, 'is_deleted' => false])->orderBy(['lookup_type' => SORT_ASC, 'lookup_urutan' => SORT_ASC])->asArray()->all();
        foreach ($getLookup as $lookup) {
            $results[$lookup['lookup_type']][] = $lookup;
        }
        return $results;
    }

    public function actionListStatusPeriksa(){
        $results = [];
        $no_pendaftaran = Yii::$app->request->post('no_pendaftaran');

        $lookup = new Lookup;
        $getLookup = $lookup->find()->where(['lookup_type'=> 'status_periksa', 'is_active' => true, 'is_deleted' => false])
            ->andWhere(['is not', 'lookup_kode', null])
            ->asArray()
            ->all();

        $pendaftaran = Pendaftaran::find()->select(['status_periksa'])
            ->where(['no_pendaftaran' => $no_pendaftaran])
            ->one();

        return [
            'status_periksa' => $getLookup,
            'pendaftaran' => $pendaftaran
        ];
    }

    public function actionListStatusPeriksaRanap(){
        $results = [];
        $no_pendaftaran = Yii::$app->request->post('no_pendaftaran');

        $lookup = new Lookup;
        $getLookup = $lookup->find()->where(['lookup_type'=> 'status_ranap', 'is_active' => true, 'is_deleted' => false])
            ->asArray()
            ->all();

        $pendaftaran = Pendaftaran::find()->select(['status_periksa'])
            ->where(['no_pendaftaran' => $no_pendaftaran])
            ->one();

        return [
            'status_periksa' => $getLookup,
            'pendaftaran' => $pendaftaran
        ];
    }

    public function actionDataStatusPeriksa($singkatan){
        $items = Cache::getStatusPeriksa($singkatan);
        return $items;
    }

    public function actionListMaster()
    {
        $models = Yii::$app->request->post();
        foreach ($models as $key=>$className) {
            $class = "app\modules\\v1\models\\" . $className;
            $model = new $class;
            $q = $model->find();
            $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
        }
        return $results;
    }

    public function actionListCaraBayar($default='1')
    {
        $carabayar_id = Yii::$app->request->get('carabayar_id', null);
        $data = CaraBayar::find()->where(['is_active' => 't']);
        if(!is_null($carabayar_id)){
            $data->andWhere(['carabayar_id' => $carabayar_id]);
        }
        $data->orderBy('carabayar_id');
        if ($default=="1") {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        } else if($default == "2"){
            $items = $data->all();
        }else {
            $items = ArrayHelper::map($data->all(), 'carabayar_id', 'carabayar_nama');
        }

        return $items;
    }

    /**
    * @author Rizal
    * @since 2018-01-24 14:01:16
    * @param int instalasi_id
    * @return array list of ruangan
    * @desc needs for depdrop or dropdown
    */
    public function actionListPenjamin($carabayar_id=null) {
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
        $data->orderBy('penjamin_namalainnya');

        $items = ArrayHelper::map($data->all(), 'penjamin_id', 'penjamin_nama');
        return $items;
    }

    public function actionSearchDokter() {
        $model = new DokterView;
        $request = Yii::$app->request;
        $get = $request->get();
        $query = $this->getAllDataDokter();
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getAllDataDokter() {
        $dokter = DokterView::find()->select('distinct ON (pegawai_id) *');
        return $dokter;
    }

    public function actionGetListDokter()
    {
        $request = Yii::$app->request;
        $model = new DokterView;
        $query = $model::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->groupBy(['pegawai_id', 'nama_pegawai'])
            ->orderBy(['nama_pegawai'=> SORT_ASC ]);

        if ($request->get('term')) {
            $q = $request->get('term');
            $query->andWhere(['ilike', 'nama_pegawai', $q]);
        }

        $results = $query->asArray()->all();
        return $results;
    }

    /**
    * @author Rizal
    * @since 2018-01-29 11:50:50
    * @param
    * @return array map list data dokter rajal
    * @desc
    */
    public function actionListDokterRajal() {
        $data = Pegawai::find()
        ->where(['is_active' => 't'])
        ->andWhere(['kelompokpegawai_id' => '1' ])
        ->orderBy('nama_pegawai');
        $items = ArrayHelper::map($data->all(), 'pegawai_id', 'nama_pegawai');

        return $items;
    }

    /**
    * @author Rizal
    * @since 2018-01-29 11:50:50
    * @param
    * @return array map list data penyakit
    * @desc
    */
    public function actionListPenyakit($mapping=true) {
        $data = JenisKasusPenyakit::find()
        ->andWhere(['is_active' => 't'])
        ->orderBy('jeniskasuspenyakit_nama');
        if ($mapping) {
            $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');
        } else {
            $items = $data->all();
        }

        return $items;
    }

    /**
     * check date input
     * berfungsi unuk melakukan pengecekan pada input apakah dalam format tanggal atau bukan
     * @param [type] $date
     * @return boolean
     */
    private function isRealDate($date)
    {
        if(strpos($date,"-") !== false){
            if (false === strtotime($date)) {
                return false;
            }
            if (strlen(explode('-', $date)[0]) == 4) {
                list($year, $month, $day) = explode('-', $date);
            } else {
                list($day, $month, $year) = explode('-', $date);
            }
            return checkdate($month, $day, $year);
        }else{
            return false;
        }

    }


    /**
     * @author Arief Saputra
     * @see Fungsi get list data rekam medik
     * @return array
     *
     */
    public function actionGetPasienRajal()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new PendaftaranForm;
        $model->keyword = isset($post['keyword']) ? $post['keyword'] : null;
        if($model->validate()) {
            $term = $model->keyword;
            $result = $this->getDataPasien();
            if(!empty($model->keyword)){
                $term = strtolower($term);
                $result->andWhere(['like', 'LOWER(no_rekam_medik)', $term]);
                $result->orWhere(['like', 'LOWER(nama_pasien)', $term]);
                if ($this->isRealDate($term)) {
                    $result->orWhere(['tanggal_lahir' => date("Y-m-d", strtotime($term))]);
                }
            }

            $is_aps = 'false';
            if(!empty($post['is_aps'])){
                if($post['is_aps']){
                    $is_aps = 'true';
                }
                /** Find all norm pasien */
                $result->andWhere("is_aps = {$is_aps} OR is_aps is null OR is_aps = false");
            } else {
                $result->andWhere("is_aps = {$is_aps} OR is_aps is null");
            }

            $result->limit(50);
            return $result->asArray()->all();
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
            ];
        }
    }

    /**
     * @author Rizal faidin
     * @see Fungsi get list data rekam medik calon masuk ranap
     * @return array
     *
     */
    public function actionGetPasienRanap()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        if(!empty($post['keyword'])){
            $term = $post['keyword'];
            if (!empty($post['bblPasien']) && $post['bblPasien'] === 'true') {
                $result = InfoPasienIbuBayi::find()
                    ->select([
                        'pendaftaran_id',
                        'no_rekam_medik',
                        'tanggal_lahir',
                        'kelaspelayanan_id',
                        'pasien_id',
                        'nama_pasien',
                        'kelahiranbayi_id',
                        'jenis_kelaminid'
                    ])
                    ->distinct()
                    ->andWhere([
                        // 'pendaftaranbaru_id' => null,
                        'jenis_kelaminid' => DocoConstants::VAR_PR,
                    ])
                    ->andWhere(['not',
                        ['pasienadmisi_id' => null,]
                    ])
                    // ->andWhere(['not',
                    //     ['kelahiranbayi_id' => null,]
                    // ])
                    ->andWhere([
                        'pasienpulang_id' => null,
                        // 'kelahiranbayi_id' => null
                    ])
                    ->andWhere("
                        (LOWER(no_rekam_medik) ILIKE '%{$term}%'
                        OR
                        LOWER(nama_pasien) ILIKE '%{$term}%')"
                    );
                    if ($this->isRealDate($term)) {
                        $result->orWhere(['tanggal_lahir' => date("Y-m-d", strtotime($term))]);
                    }
            } else {
                $result = PasienPulangRdRjView::find()
                    ->select([
                        'pasienpulangrdrj_v.pendaftaran_id',
                        'pasienpulangrdrj_v.no_rekam_medik',
                        'pasien.tanggal_lahir',
                        'pasienpulangrdrj_v.kelaspelayanan_id',
                        'pasienpulangrdrj_v.jeniskasuspenyakit_id',
                        'pasienpulangrdrj_v.pasien_id',
                        'pasienpulangrdrj_v.nama_pasien'
                    ])
                    ->leftJoin(Pasien::tableName(). ' as pasien', 'pasienpulangrdrj_v.pasien_id = pasien.pasien_id');;
                $result->andWhere(['pasienpulangrdrj_v.is_ranap'=>false]);
                $result->andWhere(['pasienpulangrdrj_v.pasienadmisi_id'=>null]);
                $result->andWhere("
                    (LOWER(pasienpulangrdrj_v.no_rekam_medik) ILIKE '%{$term}%'
                    OR
                    LOWER(pasien.nama_pasien) ILIKE '%{$term}%')");
                if ($this->isRealDate($term)) {
                    $result->orWhere(['pasien.tanggal_lahir' => date("Y-m-d", strtotime($term))]);
                }
            }
        }
        return $result->asArray()->all();
    }

    public function actionGetPasienBayi()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = InfoPasienIbuBayiHeaderView::find();

        if(!empty($post['keyword'])){
            $term = $post['keyword'];
            $result->andWhere("
                (LOWER(no_rekam_medik) ILIKE '%{$term}%'
                OR
                LOWER(nama_pasien) ILIKE '%{$term}%')");

        }

        return $result->asArray()->all();
    }

    /**
     * @author Arief Saputra
     * @see Fungsi get list data rekam medik
     * @return array
     * DEPRECATED
     */
    // public function actionGetPasienRekamMedik()
    // {
    //     $request = Yii::$app->request;
    //     $post = $request->post();
    //     $result = $this->getDataPasienPulang();

    //     // $result->select(['no_rekam_medik','CONCAT(no_rekam_medik,\' - \',nama_pasien) as nama_pasien']);
    //     if(!empty($post['keyword'])){
    //         $term = $post['keyword'];
    //         $result->andWhere(['like', 'LOWER(pasienpulangrdrj_v.no_rekam_medik)', $term]);
    //         $result->orWhere(['like', 'LOWER(pasien.nama_pasien)', $term]);
    //     }

    //     return $result->asArray()->all();
    // }

    public function actionViewInfoPasien()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $result = $this->getDataPasien();

        $result->select(['no_rekam_medik','CONCAT(no_rekam_medik,\' - \',nama_pasien) as nama_pasien']);
        if(!empty($post['keyword'])){
            $term = $post['keyword'];
            $result->andWhere(['like', 'LOWER(no_rekam_medik)', $term]);
            $result->orWhere(['like', 'LOWER(nama_pasien)', $term]);
        }
        return $result->asArray()->all();
    }

    // Segera Di hapus
    public function actionGetInfoPasien($id)
    {
        // cari data pasien
        $result = $this->getDataPasien();
        $result->andWhere(['pasien_id'=>$id]);
        $result = $result->asArray()->one();

        // cek tagihan
        $tagihan = CekTagihanView::find()
            ->andWhere(['pasien_id'=>$result['pasien_id']])
            ->andWhere(['>', '(total_tagihan - total_sdh_bayar)', 0])
            ->asArray()->all();

        $result['has_tagihan'] = $tagihan ? true : false;

        return $result;
    }

    public function actionGetDataPasien($id = null, $no_rm = null)
    {
        $listMultiPayer = [];
        if (empty($id) && empty($no_rm)) return [];
        $result = $this->getDataPasien();
        if ($id) {
            $result->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $result->andWhere([
                'no_rekam_medik' => $no_rm
            ]);
        }
        $result = $result->asArray()->one();
        $result['nik_pasien'] = $this->helper->getNik($result);

        $kunjugan = InfoKunjunganRsView::find();

        if ($id) {
            $kunjugan->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $kunjugan->andWhere([
                'no_rekam_medik' => $no_rm
            ]);
        }

        $listKunjungan = $kunjugan->orderBy([
            'tgl_pendaftaran' => SORT_DESC
        ])->limit(3)->all();

        $multiPayer = InfoMultipayerView::find();

        if($id) {
            $multiPayer->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $multiPayer->andWhere([
                'no_rekam_medik' => $id
            ]);
        }

        $listMultiPayer = $multiPayer->orderBy([
            'pendaftaran_id' => SORT_DESC
        ])->limit(3)->all();

        return [
            'info_pasien' => $result,
            'kunjugan' => $listKunjungan,
            'multipayer' => $listMultiPayer
        ];
    }

    public function actionGetDataPasienSy($id = null, $no_rm = null)
    {
        if (empty($id) && empty($no_rm)) return [];
        $result = $this->getDataPasien();
        if ($id) {
            $result->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $result->andWhere([
                'no_rekam_medik' => $no_rm
            ]);
        }
        $result = $result->asArray()->one();
        $result['nik_pasien'] = $this->helper->getNik($result);

        $keluarga = SyKeluargaPasienView::find();

        $kunjugan = InfoKunjunganRsView::find();

        if ($id) {
            $keluarga->andWhere([
                'pasien_id' => $id
            ]);

            $kunjugan->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $keluarga->andWhere([
                'no_rekam_medik' => $no_rm
            ]);

            $kunjugan->andWhere([
                'no_rekam_medik' => $no_rm
            ]);
        }

        $keluarga = $keluarga->asArray()->one();

        $listKunjungan = $kunjugan->orderBy([
            'tgl_pendaftaran' => SORT_DESC
        ])->limit(3)->all();

        return [
            'info_pasien' => $result,
            'kunjugan' => $listKunjungan,
            'keluarga' => $keluarga
        ];
    }

    public function actionGetInfoPasienBayi($pendaftaran_id)
    {
        $result = InfoPasienIbuBayi::find();
        $result = $result->where(['pendaftaran_id' => $pendaftaran_id]);
        $result = $result->asArray()->all();

        $countBayi = count($result);
        return [
            'countBayi' => $countBayi,
            'result' => $result,
        ];
    }

    public function actionGetInfoPasienNew($pasien_id)
    {

        $result = $this->getDataPasienPulang();
        $result->andWhere(['pasienpulangrdrj_v.pasien_id'=>$pasien_id]);

        return $result->asArray()->one();
    }

    private function getDataPasien()
    {
        $result = PasienV::find();
        return $result;
    }

    public function actionGetDefaultStatusRekamMedik()
    {
        $statusrekammedik = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_BY_TYPE, $this->getLookupByType('status_rekammedik'), true, 'status_rekammedik');
        return $statusrekammedik;
    }

    private function getDataPasienPulang()
    {
        $result = PasienPulangRdRjView::find()
            ->select(['pasienpulangrdrj_v.*', 'pasien.*'])
            ->leftJoin(PasienV::tableName(). ' as pasien', 'pasienpulangrdrj_v.pasien_id = pasien.pasien_id');

        return $result;
    }

    /**
    * @author Arief Saputra
    * @since 2018-03-21 11:37:16
    * @param int instalasi_id
    * @return array list of ruangan
    * @desc
    */
    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = $this->getDataRuangan();
        $result->select(['ruangan_id','ruangan_nama']);
        if (isset($get['jeniskasuspenyakit_id'])) {
            $result->andWhere(['jeniskasuspenyakit_id' => $get['jeniskasuspenyakit_id']]);
        }
        if (isset($get['kelaspelayanan_id'])) {
            $result->andWhere(['kelaspelayanan_id' => $get['kelaspelayanan_id']]);
        }
        if (isset($get['instalasi_id'])) {
            $result->andWhere(['instalasi_id' => $get['instalasi_id']]);
        }
        if (isset($get['dropdown'])) {
            $result->select([
                'ruangan_id as id',
                'ruangan_nama as text',
            ])->asArray()->all();
        } else {
            $items = ArrayHelper::map($result->all(), 'ruangan_id', 'ruangan_nama');
        }


        return $items;
    }

    public function actionGetListRuanganKelas()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $items = [];
        $result = $this->getDataRuanganKelas();
        $result->select(['ruangan_id','ruangan_nama']);
        if (!empty($get['kelaspelayanan_id'])) {
            $result->andWhere(['kelaspelayanan_id' => $get['kelaspelayanan_id']]);
        }
        if (isset($get['instalasi_id'])) {
            $result->andWhere(['instalasi_id' => $get['instalasi_id']]);
        }
        if (isset($get['jeniskasuspenyakit_id']) && $get['jeniskasuspenyakit_id']) {
            $result->andWhere(['jeniskasuspenyakit_id' => $get['jeniskasuspenyakit_id']]);
        }

        $result->orderBy('ruangan_nama ASC');
        $items = ArrayHelper::map($result->all(), 'ruangan_id', 'ruangan_nama');

        return $items;
    }

    private function getDataRuangan()
    {
        $result = KasuspenyakitruanganV::find();
        return $result;
    }

    private function getDataRuanganKelas()
    {
        $result = RuanganPelayananV::find();
        return $result;
    }

    private function getKamarByRuangan()
    {
        // $result = KamarTempatTidur::find()->joinWith(['kamarRuangan']);
        // return $result;

        $result = KamarRuangan::find();
        return $result;
    }

    /**
    * @author Rizal
    * @since 2018-03-20 16:05:34
    * ALL ABOUT ANTRIAN
    */
    public function actionAntrianList($status_antrian=null, $tgl_antrian=null)
    {
        $tgl_antrian = date('Y-m-d', strtotime($tgl_antrian ? : date('Y-m-d')));

        $antrian = $this->getDataAntrian();
        $antrian->andWhere(['tgl_antrian'=>$status_antrian]);
        if ($status_antrian) {
            $antrian->andWhere(['status_antrian'=>$status_antrian]);
        }
        return $antrian->asArray()->all();
    }

    private function getDataAntrian()
    {
        $result = Antrian::find();
        return $result;
    }

    /*
    * author: Rizqi Febian edited rizal
    * date: 11-04-2018
    * updated: 2018-04-13 11:22:14
    * get data ruangan
    * params needed: use advanced-filter or manual
    */
    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new Ruangan;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionGetRuanganJson()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new Ruangan;
        $query = $model::find();
        $query->andWhere(['instalasi_id'=>json_decode($get['instalasi_id'])]);

        return $query->asArray()->all();
    }
    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data jenis kasus penyakit by ruangan
    * params needed: ruangan_id
    */
    public function actionGetRuanganPenyakit($id)
    {
        if(empty($id)){
            return [];
        }
        $request = Yii::$app->request;
        $where = " where kp.ruangan_id = {$id}";
        $query = "SELECT jk.* from kasuspenyakitruangan_mp kp
                  INNER JOIN jeniskasuspenyakit_m jk ON jk.jeniskasuspenyakit_id = kp.jeniskasuspenyakit_id".$where." and kp.is_deleted = false and kp.is_active = true";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        return ArrayHelper::map($data,'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
    }

    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data kelas pelayanan by ruangan
    * params needed: ruangan_id
    */
    public function actionGetRuanganKelasPelayanan($id){
        if(empty($id)){
            return [];
        }
        $query = "SELECT kr.kelaspelayanan_id,kr.kelaspelayanan_nama from kelasruangan_mp kp
                  INNER JOIN kelaspelayanan_m kr ON kp.kelaspelayanan_id = kr.kelaspelayanan_id
                  where kp.ruangan_id = {$id} and kp.is_deleted = false and kp.is_active = true and kr.is_deleted = false and kr.is_active = true
                  ORDER BY kr.kelaspelayanan_nama";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        return ArrayHelper::map($data,'kelaspelayanan_id','kelaspelayanan_nama');
    }
    /*
    * author: Rizqi Febian
    * date: 11-04-2018
    * get data kelas pelayanan by ruangan
    * params needed: ruangan_id
    */
    public function actionGetRuanganDokter($id){
        $where = "";
        if(!empty($id)){
            $where = "AND rp.ruangan_id = {$id}";
        }

        $request = Yii::$app->request;
        $query = "SELECT p.pegawai_id,p.nama_pegawai from ruanganpegawai_mp rp
                  INNER JOIN pegawai_m p ON rp.pegawai_id = p.pegawai_id
                  INNER JOIN kelompokpegawai_m ON p.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id ".$where." and rp.is_deleted = false
                  and rp.is_active = true and kelompokpegawai_m.kelompokpegawai_namalainnya='" . DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS . "'";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        $pegawaiCuti = (new RegistrationService)->getDokterCuti([
                            'id' => $id,
                            'data' => $data
                        ]);

        return ArrayHelper::map($pegawaiCuti,'pegawai_id','nama_pegawai');
    }

    public function actionListInstalasi($isPenunjang = 'false')
    {
        try {
            $model = new Instalasi;
            $model = $model->find()
                ->andWhere(['is_pelayanan'=>true]);
            if($isPenunjang != 'false'){
                $model->andWhere('is_penunjang = '.$isPenunjang);
            }
            $count = $model->count();
            $data = $model
                ->orderBy('instalasi_id')
                ->asArray()
                ->all();

            $results = [
                'data'=>$data,
                'count'=>$count,
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

    public function actionListRuangan($instalasi_id=null,$singkatan=null) {
        $request = Yii::$app->request;
        $data = Ruangan::find()->joinWith('instalasi');
        $data->where(['ruangan_m.is_active' => 't']);
        if ($instalasi_id) {
            $data->andWhere(['ruangan_m.instalasi_id' => $instalasi_id]);
        }
        if ($singkatan) {
            $data->andWhere(['instalasi_m.instalasi_singkatan' => $singkatan]);
        }

        if ($request->get('is_executive') && $request->get('is_executive') == true) {
            $data->andWhere(['is_executive' => TRUE]);
        }

        $data->orderBy('ruangan_m.ruangan_id');

        $results = [
            'data' => $data->all(),
            'count' => $data->count()
        ];
        return $results;
    }

    /*
    * author: Rizqi Febian
    * date: 17-04-2018
    * get data asal rujukan
    */
    public function actionListAsalRujukan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new AsalRujukan;
        $query = $model::find();
        $query->andWhere(['is_active' => true, 'is_deleted' => false]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }
    /*
    * author: Rizqi Febian
    * date: 17-04-2018
    * get data asal rujukan
    */
    public function actionGetListKelasPelayanan($id = null){
        $request = Yii::$app->request;
        $model = new KelasPelayanan;
        $query = $model::find();
        if($id){
            $query->where(['kelaspelayanan_id'=>$id]);
        }
        $query->andWhere(['is_active' => true, 'is_deleted' => false]);


        // return $this->getOrSetCache(DocoConstants::VAR_C_KP, $query, true);
        return $query->asArray()->all();
    }

    public function actionGetMappingListKelasPelayanan($id = null){
        $request = Yii::$app->request;
        $model = new KelasPelayanan;
        $query = $model::find();
        if($id){
            $query->where(['kelaspelayanan_id'=>$id]);
        }
        $query->andWhere(['is_active' => true, 'is_deleted' => false]);

        $items = ArrayHelper::map($query->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    public function actionGetKamarByRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $result = $this->getKamarByRuangan();
            if(isset($post['ruangan_id'])){
                $result->where(['ruangan_id' => $post['ruangan_id']]);
                $result->andWhere(['is_deleted' => false]);
                $result->andWhere(['is_active' => true]);
            }
            return $result->asArray()->all();
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionListTempatTidur()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $result = KamarTempatTidur::find();
            if(isset($post['kamarruangan_id'])){
                $result->where(['kamarruangan_id' => $post['kamarruangan_id']]);
                $result->andWhere(['is_deleted' => false]);
                $result->andWhere(['is_active' => true]);
            }
            return $result->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetTempatTidur()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->post('kamarruangan_id');

        $query = KamarTempatTidur::find()
                    ->select([
                        'kamartempattidur_m.kamartempattidur_id',
                        'kamartempattidur_m.no_tempattidur',
                        'kamartempattidur_m.status_isi',
                        'kettempattidur_m.kode_warna'
                    ])
                    ->leftJoin('kettempattidur_m', 'kettempattidur_m.kettempattidur_id = kamartempattidur_m.kettempattidur_id')
                    ->where(['kamartempattidur_m.kamarruangan_id' => $kamarruangan_id])
                    ->orderBy('kamartempattidur_m.no_tempattidur');


        $no_tempattidur = '';
        if($query) {
            foreach ($query->asArray()->all() as $value) {
                $class = ($value['status_isi'] == false) ? 'pilih-bed' : '';
                $disabled = ($value['status_isi'] == false) ? false : true;

                $no_tempattidur .= Html::button($value['no_tempattidur'], [
                    'class' => 'btn-xs '.$class.'',
                    'disabled' => $disabled,
                    'style' => "margin-bottom:8px;background-color:".$value['kode_warna'],
                    'value' => $value['kamartempattidur_id'],
                    'data-key' => $value['kamartempattidur_id'],
                    'data-label' => $value['no_tempattidur'],
                    'data-kamarruangan_id' => $kamarruangan_id
                ]);
                $no_tempattidur .= '&nbsp;';
            }
        }

        return $no_tempattidur;
    }

    // public function actionListKarcis()
    // {
    //     $request = Yii::$app->request;
    //     $tarif = new InfoTarifRsView;
    //     $result = $tarif->find()
    //         ->where(['kelompoktindakan_id' => 17, 'komponentarif_id' => 6, 'ruangan_id' => 5, 'kelaspelayanan_id' => 4]);

    //     return $result->all();
    // }

    public function actionListKarcis()
    {
        $request = Yii::$app->request;

        $model = new InfoTarifRsView;
        $query = $model::find();

        if(empty($request->get('kelaspelayanan_id'))) {
            $query->where('0 = 1');
        }
        else {
            $query->where([
                'kelompoktindakan_id' => 17,
                'komponentarif_id' => 6,
                'ruangan_id' => 5,
                'kelaspelayanan_id' => $request->get('kelaspelayanan_id')
            ]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    /*
    * author: Rizqi Febian
    * date: 24-04-2018
    * get data dokter by jam skrg
    * params needed: ruangan_id, type: 1 || 0 || 2, 1:Rajal 2:Penunjang 0:IGD DEPRECATED
    */
    public function actionGetRuanganJamDokter($id = null, $type = 'rajal', $isInstalasi = false, $withCodeBpjs = false, $withKuota = false)
    {
        $model = new PendaftaranForm;
        $model->ruangan_id = $id;
        $model->type = $type;
        $with_kuota = LookupTransaksi::find()->where("kode_transaksi = 'with_kuota'")->asArray()->one();
        $additionalValue = ArrayHelper::getValue($with_kuota, 'additional_value');

        $request = Yii::$app->request;
        $tgl_tujukan = $request->get('tgl_rujukan', null);

        if($model->validate()) {
            if(empty($id)){
                return [];
            }

            $request = Yii::$app->request;
            $isBpjs = filter_var($request->get('is_bpjs',false), FILTER_VALIDATE_BOOLEAN);
            $where = "";
            $today_id = date('N') + 74;
            $now = date('H:i');
            $where .= " ";

            if(!empty($tgl_rujukan)) {
                $tgl_rujukan = date("N", strtotime($tgl_rujukan));
                $today_id = $tgl_rujukan + 74;
            }
            
            if ($type == 'penunjang') {
                if($isInstalasi == true) {
                    $tipe = 'instalasi_id';
                } else {
                    $tipe = 'ruangan_id';
                }

                $query = "
                    select pegawai_id, nama_pegawai, kode_dokter_bpjs
                    from pegawai_v
                    WHERE {$tipe} = {$id} AND kelompokpegawai_id = 1
                    ORDER BY nama_pegawai
                ";
            } else {
                if ($additionalValue=='false') {
                    // $konfigNewPoli = AntrianPoliLogic::isKonfig();
                    // if($konfigNewPoli == true) {
                    if($isBpjs === true || $isBpjs === 'true'){

                        $query = "
                            select p.pegawai_id, p.nama_pegawai, p.kode_dokter_bpjs, d.jadwaldokter_id, kr.kuota_tersedia, CONCAT(TO_CHAR(d.jadwaldokter_mulai, 'HH24:MI'), '-', TO_CHAR(d.jadwaldokter_tutup, 'HH24:MI')) as jam_praktek
                            from jadwalbukapoli_m j
                                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                                LEFT JOIN kuotadokter_r kr ON kr.jadwaldokter_id = d.jadwaldokter_id AND kr.is_online = false
                            where j.ruangan_id = {$id}
                                -- and j.jam_tutup >=time '{$now}'
                                and d.jadwaldokter_tutup >= time '{$now}'
                                and j.hari = {$today_id}
                                and d.is_deleted = false and d.is_active = true
                                and p.is_deleted = false and p.is_active = true
                                and p.kode_dokter_bpjs IS NOT NULL
                            ";
                    } else {
                        $query = "
                            select p.pegawai_id, p.nama_pegawai, p.kode_dokter_bpjs, d.jadwaldokter_id, kr.kuota_tersedia, CONCAT(TO_CHAR(d.jadwaldokter_mulai, 'HH24:MI'), '-', TO_CHAR(d.jadwaldokter_tutup, 'HH24:MI')) as jam_praktek
                            from jadwalbukapoli_m j
                                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                                LEFT JOIN kuotadokter_r kr ON kr.jadwaldokter_id = d.jadwaldokter_id AND kr.is_online = false
                            where j.ruangan_id = {$id}
                                -- and j.jam_tutup >=time '{$now}'
                                and d.jadwaldokter_tutup >= time '{$now}'
                                and j.hari = {$today_id}
                                and d.is_deleted = false and d.is_active = true
                                and p.is_deleted = false and p.is_active = true
                            ";
                    }
                } else {
                    $query = "
                            SELECT DISTINCT p.pegawai_id, p.nama_pegawai, p.kode_dokter_bpjs
                            FROM jadwalbukapoli_m j
                            RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                            RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                            WHERE j.ruangan_id = {$id}
                                AND j.hari = {$today_id}
                                AND d.is_deleted = false AND d.is_active = true
                                AND p.is_deleted = false AND p.is_active = true;                    
                        ";
                }
            }

            $data = Yii::$app->db->createCommand($query)->queryAll();
            $pegawaiCuti = (new RegistrationService)->getDokterCuti([
                                'id' => $id,
                                'data' => $data
                            ]);
            $konfig_is_unik = (new DocoConstansId)->actionGetId('konfig_pendaftaran_unique_dokter');
            if($withCodeBpjs || $additionalValue){
                return [
                    'konfig_is_unik' => $konfig_is_unik,
                    'data'=>$pegawaiCuti,
                    'with_kuota'=>$additionalValue
                ];
            }
            return [
                'konfig_is_unik' => $konfig_is_unik,
                'with_kuota'=>$additionalValue,
                'data' => ArrayHelper::map($pegawaiCuti,'pegawai_id','nama_pegawai')
            ];
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
            ];
        }
    }
    /*
    * author: Rizqi Febian
    * date: 25-04-2018
    * get data rujukan dari
    * params needed: asalrujukan_id
    */
    public function actionGetRujukanDari($id){
        $request = Yii::$app->request;
        $data = PerujukView::find()->andWhere([
            'asalrujukan_id'=>$id,
            'is_active'=>true,
        ]);
        $data = $data->asArray()->all();
        return ArrayHelper::map($data,'perujuk_id','namaperujuk');
    }
    /*
    * author: Rizqi Febian
    * date: 25-04-2018
    * get data diagnosa
    * params needed: diagnosa name, type (9 / 10)
    */
    public function actionGetDiagnosa(){
        $data = DiagnosaView::find();
        $request = Yii::$app->request;
        $post = $request->post();
        $term = $post['term'];
        $type = $post['type'];
        if($term){
            $data->andFilterWhere(['or',
                ['ILIKE','diagnosa_nama',$term],
                ['ILIKE','diagnosa_kode',$term]]);
        }
        if ($type) {
            if ($type == '9') {
                $data->andWhere(['tabularlist_versi'=>'ICD IX']);
            } else if ($type == '10') {
                $data->andWhere(['tabularlist_versi'=>'ICD X']);
            }
        }
        return $data->asArray()->all();
    }
    /*
    * author: Rizqi Febian
    * desc: get data used in index informasi pencarian pasien
    */
    public function actionPackInformasiPencarianPasien($param = 'index')
    {
        $userIdentity = Yii::$app->request->get('userIdentity', []);
        if(!empty($userIdentity)){
            $peranPengguna = PeranPengguna::find()->select([
                'peranpengguna_menu'
            ])->where(['in','peranpenggunanama', $userIdentity['roles']])
            ->andWhere(['like','peranpengguna_menu','set-flag-hak-akses-update'])
            ->asArray()->one();
        }
        $isAksesUpdate = isset($peranPengguna['peranpengguna_menu']) ? true : false;

        if($param == 'index'){
            $data_jk = [];
            $data_propinsi = [];

            $data_jk = $this->listLookup(['jenis_kelamin']);
            $data_propinsi = $this->getPropinsi();
            $data_propinsi->select(['propinsi_id','propinsi_nama']);
            $result = [
                'lookup'=>$data_jk,
                'data_propinsi'=> $data_propinsi->asArray()->all(),
                'isAksesUpdate' => $isAksesUpdate,
            ];
        }else if($param == 'update'){
            $lookup = [];
            $data_propinsi = [];
            $data_kabupaten = [];
            $data_kecamatan = [];
            $data_kelurahan = [];
            $data_pekerjaan = [];

            $listRequests = [
                'jenis_identitas',
                'nama_depan',
                'jenis_kelamin',
                'status_perkawinan',
                'warga_negara',
                'agama',
                'golongan_darah',
                'nama_depan',
                'jenis_identitas',
                'hubungan_keluarga',
                'hubungan',
                'pengantar'
            ];
            $lookup = $this->listLookup($listRequests);
            $data_propinsi = $this->getPropinsi();
            $data_propinsi->select(['propinsi_id','propinsi_nama']);
            $listRequestMaster = [
                'pendidikan' => 'Pendidikan',
                'pekerjaan' => 'Pekerjaan',
                'suku' => 'Suku',
            ];
            $master = $this->getListMaster($listRequestMaster);
            $result = [
                'lookup'=>$lookup,
                'data_propinsi'=> $data_propinsi->asArray()->all(),
                'data_kabupaten'=> [],
                'data_kecamatan'=> [],
                'data_kelurahan'=> [],
                'data_pekerjaan'=>$master['pekerjaan'],
                'data_pendidikan'=>$master['pendidikan'],
                'data_suku'=>$master['suku'],
                'isAksesUpdate' => $isAksesUpdate,
            ];
        }
        return $result;
    }

    public function getPropinsi()
    {
        $where = ['is_active' => 't', 'is_deleted' => 'f'];
        $data = Propinsi::find()->where($where);
        return $data;
    }
    public function getKabupaten($id = null)
    {
        $where = ['kabupaten_m.is_active' => 't', 'kabupaten_m.is_deleted' => 'f'];
        if($id != null){
            $where = ['kabupaten_m.is_active' => 't', 'kabupaten_m.is_deleted' => 'f','propinsi_id'=>$id];
        }
        $data = Kabupaten::find()->where($where);
        return $data;
    }
    public function getKecamatan($id = null)
    {
        $where = ['kecamatan_m.is_active' => 't', 'kecamatan_m.is_deleted' => 'f'];
        if($id != null){
            $where = ['kecamatan_m.is_active' => 't', 'kecamatan_m.is_deleted' => 'f','kabupaten_id'=>$id];
        }
        $data = Kecamatan::find()->where($where);
        return $data;
    }
    public function getKelurahan($id = null)
    {
        $where = ['kelurahan_m.is_active' => 't', 'kelurahan_m.is_deleted' => 'f'];
        if($id != null){
            $where = ['kelurahan_m.is_active' => 't', 'kelurahan_m.is_deleted' => 'f','kecamatan_id'=>$id];
        }
        $data = Kelurahan::find()->where($where);
        return $data;
    }
    public function getPekerjaan(){
        $data = Pekerjaan::find()->where(['is_active'=>'t', 'is_deleted'=>'f']);
        return $data;
    }
    public function actionListKabupaten($propinsi = null) {
        $model = $this->getKabupaten();
        // return $propinsi;
        $model->joinWith(['propinsi']);
        $model->andFilterWhere(['ILIKE','propinsi_m.propinsi_nama',$propinsi ]);
        return $model->asArray()->all();
    }
    public function actionListKecamatan($kabupaten = null) {
        $model = $this->getKecamatan();
        $model->joinWith(['kabupaten']);
        $model->andFilterWhere(['ILIKE','kabupaten_m.kabupaten_nama',$kabupaten ]);
        return $model->asArray()->all();
    }

    /**
     * @todo get all data for pendaftaran frontend
     * @author Randy Vianda Putra <randy@docotel.com>
     * @updated Setyabudi
     */
    public function actionGetApi()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan = $cara_bayar = $default_alamat = [];
        try {
            $db = Yii::$app->db;
            // get all lookup by request
            $listRequestLookup = [
                'jenis_identitas',
                'nama_depan',
                'jenis_kelamin',
                'status_perkawinan',
                'warga_negara',
                'agama',
                'pengantar',
                'pengantar_sy',
                'golongan_darah',
                'status_rekammedik',
                'keadaan_masuk',
                'transportasi',
                'hubungan_keluarga',
                'hubungan',
                'title_pendaftaran',
                'bahasa_sehari',
                'alamat_depan',
                'referral_marketing', // list data marketing untuk referral
            ];
            $lookup = Cache::getLookup('lookup-pendaftaran-new', $listRequestLookup);

            // get all master by request
            $listRequestMaster = [
                'propinsi' => 'Propinsi',
                'pendidikan' => [
                    'model' => 'Pendidikan',
                    'select' => [
                        'pendidikan_id',
                        'pendidikan_nama'
                    ],
                    'order_by' => [
                        'pendidikan_urutan' => SORT_ASC,
                        'pendidikan_nama' => SORT_ASC
                    ]
                ],
                'pekerjaan' => [
                    'model' => 'Pekerjaan',
                    'select' => [
                        'pekerjaan_id',
                        'pekerjaan_nama'
                    ],
                    'order_by' => [
                        'pekerjaan_nama' => SORT_ASC
                    ]
                ],
                'suku' => [
                    'model' => 'Suku',
                    'select' => [
                        'suku_id',
                        'suku_nama'
                    ],
                    'order_by' => [
                        'suku_nama' => SORT_ASC
                    ]
                ],
                'propinsi' => [
                    'model' => 'Propinsi',
                    'select' => [
                        'propinsi_id',
                        'kode_propinsi',
                        'propinsi_nama'
                    ],
                    'order_by' => [
                        'propinsi_nama' => SORT_ASC
                    ]
                ],
                'pegawai' => [
                    'model' => 'Pegawai',
                    'select' => [
                        'pegawai_id',
                        'nama_pegawai'
                    ],
                    'order_by' => [
                        'nama_pegawai' => SORT_ASC
                    ]
                ]
            ];

            $master = Cache::getMaster('master-pendaftaran', $listRequestMaster);
            $default_asal_rujukan = DocoConstansId::actionGetId('asal_rujukan');
            $default_jenis_penyakit = DocoConstansId::actionGetId('kasus_penyakit');
            $default_penjamin = DocoConstansId::actionGetId('penjamin_umum');
            $default_alamat['provinsi_id'] = DocoConstansId::actionGetId('provinsi_id');
            $default_alamat['kabupaten_id'] = DocoConstansId::actionGetId('kabupaten_id');
            $default_alamat['kecamatan_id'] = DocoConstansId::actionGetId('kecamatan_id');
            $default_alamat['kelurahan_id'] = DocoConstansId::actionGetId('kelurahan_id');

            // get ruangan by instalasi
            if (isset($get['instalasi_id'])) {
                $ruangan = Cache::getRuanganByInstalasi($get['instalasi_id']);
            }

            // get cara bayar
            if (isset($get['default'])) {
                $cara_bayar = Cache::getCaraBayar($get['default']);
            }
            // get asal rujukan
            $asal_rujukan = Cache::getAsalRujukan();
            // get kelas pelayanan
            $kelas_pelayanan = Cache::getKelasPelayanan();
            $jeniskasus = $this->actionListPenyakit(false);
            $queryPenjamin = Cache::getPenjamin();
            $klasifikasiKamar = $this->actionKlasifikasiKamar(false);
            $penjaminIntegrasi = $this->getPenjaminTerintegrasi();

            $instalasi = Cache::getInstalasi('true');
            $rujukanDari = Cache::getRujukanDari();
            /** Konfig Loket */
            $loketInstalasi = null;
            if (!empty($get['loket_id'])) {
                $loketId = (int) $get['loket_id'];
                if ($loketId) {
                    $getInstalasi = $db->createCommand("
                        SELECT konf.instalasi_id FROM loket_mp map
                        JOIN konfigantrian_m konf ON map.konfigantrian_id = konf.konfigantrian_id
                        WHERE map.is_deleted = false
                        AND map.is_active = true
                        AND map.loket_id = {$loketId}
                    ")->queryOne();
                    $loketInstalasi = isset($getInstalasi['instalasi_id']) ? $getInstalasi['instalasi_id'] : null;
                }
            }
            $pendaftaranol = null;
            $pendaftaranol_id = isset($get['pendaftaranol_id']) ? $get['pendaftaranol_id'] : null;
            if ($pendaftaranol_id) {
                $pendaftaranol = $db->createCommand("
                    SELECT
                        *
                    FROM infopendaftaranol_v ol
                    WHERE ol.pendaftaranol_id = {$pendaftaranol_id}
                ")->queryOne();
            }

            $janjiPoli = null;
            $janji_id = isset($get['janji_id']) ? $get['janji_id'] : null;
            if($janji_id) {
                // $janjiPoli = (new InfRencanaKontrol)->janjiDaftar($janji_id);
                $janjiPoli = InfRencanaKontrol::find()->select(['buatjanjipoli_id', 'pasien_id', 'no_pendaftaran', 'carabayar_id', 'nama_pasien', 'pegawai_id', 'no_rekam_medik', 'penjamin_id', 'ruangan_id', 'tgl_jadwal', 'tgl_pendaftaran', 'tanggal_lahir','tgl_buatjanji', 'status_janji'])->where(['buatjanjipoli_id' => $janji_id])->one();
            }

            $konfigSystem = KonfigSystem::find()->one();

            $rujukRanap = null;
            $pendaftaran_id = isset($get['pendaftaran_id']) ? $get['pendaftaran_id'] : null;
            if ($pendaftaran_id) {
                $rujukRanap['kamar'] = [];
                $pasienRujukRanap = InfoPasienRujukRanapView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
                $rujukRanap['pendaftaran'] = $pasienRujukRanap;
                $ketersediaanKamar = KetersediaanKamar::find()->where(['pendaftaran_id' => $pendaftaran_id])
                    ->orderBy(['ketersediaankamar_id' => SORT_DESC])->one();
                if(!empty($ketersediaanKamar)) {
                    $where = "WHERE kamartempattidur_id = ".$ketersediaanKamar->kamartempattidur_id."";
                    $idRuangan = (int)$ketersediaanKamar->ruangan_id;
                    $idPenjamin = (int)$pasienRujukRanap->penjamin_id;
                    $idKelasPelayanan = (int)$ketersediaanKamar->kelaspelayanan_id;
                    $tipeTarif = 'kamar';
                    $rujukRanap['kamar'] = Yii::$app->db->createCommand('
                    SELECT DISTINCT
                        ruangan_id,
                        ruangan_nama,
                        kelaspelayanan_nama,
                        jeniskasuspenyakit_nama,
                        kamarruangan_nokamar,
                        kelaspelayanan_id,
                        kamarruangan_id,
                        kamartempattidur_id,
                        no_tempattidur,
                        harga_tariftindakan
                    FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:tipe_tarif)
                    '.$where.'
                    ')
                    ->bindParam(':ruangan_id',$idRuangan)
                    ->bindParam(':penjamin_id', $idPenjamin)
                    ->bindParam(':kelaspelayanan_id', $idKelasPelayanan)
                    ->bindParam(':tipe_tarif', $tipeTarif)
                    ->queryAll();
                }
            }

            $result = [
                'lookup' => $lookup,
                'master' => $master,
                'ruangan' => $ruangan,
                'cara_bayar' => $cara_bayar,
                'asal_rujukan' => $asal_rujukan,
                'kelas_pelayanan' => $kelas_pelayanan,
                'jeniskasus' => $jeniskasus,
                'penjamin' => $queryPenjamin,
                'instalasi' => $instalasi,
                'pendaftaranol' => $pendaftaranol ?: null,
                'rujukan_dari' => $rujukanDari,
                'loket_instalasi' => $loketInstalasi,
                'default_asal_rujukan' => $default_asal_rujukan,
                'default_jenis_penyakit' => $default_jenis_penyakit,
                'default_penjamin' => $default_penjamin,
                'janjiPoli' => $janjiPoli ?: null,
                'konfigSystem' => $konfigSystem,
                'default_alamat' => $default_alamat,
                'rujukRanap' => $rujukRanap,
                'klasifikasiKamar' => $klasifikasiKamar,
                'penjaminIntegrasi' => $penjaminIntegrasi
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

    public function actionGetApiLaporan()
    {
        $get = Yii::$app->request->get();
        $jenis = '';
        try {
            $jenis = isset($get['jenis']) ? $get['jenis'] : '';
            $master = [];
            if ($jenis == 'igd') {
                // get all master by request
                $listRequestMaster = [
                    'ruangan' => ['Ruangan', ['instalasi_id'=>DocoConstants::INST_ID_RD]],
                    'carabayar' => 'CaraBayar',
                    'pegawai' => ['DokterView', ['instalasi_id'=>DocoConstants::INST_ID_RD]],
                ];
                $master = $this->getListMaster($listRequestMaster);
            }else if($jenis == 'rajal'){
                $listRequestMaster = [
                    'ruangan' => [
                        'Ruangan',
                        [
                            'instalasi_id' => DocoConstants::INST_ID_RJ
                        ]
                    ],
                    'carabayar' => 'CaraBayar',
                    'pegawai' => [
                        'DokterView',
                        [
                            'instalasi_id'=>DocoConstants::INST_ID_RJ
                        ]
                    ],
                    'status_periksa' => [
                        'Lookup',
                        [
                            'lookup_type' => 'status_periksa'
                        ]
                    ],
                    'carakeluar' => 'CaraKeluar',
                ];
                $master = $this->getListMaster($listRequestMaster);
            }
            else if($jenis == 'ranap'){
                $listRequestMaster = [
                    'ruangan' => [
                        'Ruangan',
                        [
                            'instalasi_id' => DocoConstants::INST_ID_RI
                        ]
                    ],
                    'carabayar' => 'CaraBayar',
                    'pegawai' => [
                        'DokterView',
                        [
                            'instalasi_id'=>DocoConstants::INST_ID_RI
                        ]
                    ],
                ];
                $master = $this->getListMaster($listRequestMaster);
            }

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

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key=>$value) {
            $condition = false;
            if (is_array($value)) {
                $class = "app\modules\\v1\models\\" . $value[0];
                $condition = $value[1];
            } else {
                $class = "app\modules\\v1\models\\" . $value;
            }
            $model = new $class;
            $q = $model->find();
            if ($condition) {
                $q->andWhere($condition);
            }
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            // $results[$key] = $this->getOrSetCache(DocoConstants::VAR_CACHE_MASTER, $q, true, $key);
            $results[$key] = $q->asArray()->all();
        }
        return $results;
    }

    private function getRuanganByJson($listInstalasi)
    {
        $listId = json_decode($listInstalasi);
        $model = new Ruangan;
        $query = $model::find();
        $query->andWhere(['instalasi_id' => $listId, 'is_active' => true, 'is_deleted' => false]);

        return $query->asArray()->all();
    }

    /**
    * @author Rizal
    * @since 2018-05-03 15:48:54
    * @param loginpemakai_id
    * @return
    * @desc
    */
    public function actionGetCurrentLoket($loginpemakai_id)
    {
        try {
            return Loket::find()
                ->andWhere(['loginpemakai_id'=>$loginpemakai_id])
                ->asArray()->one();
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
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionPackInformasiPasien(){
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $data['carabayar'] = $this->actionListCaraBayar();
            $data['ruangan'] = $this->actionListRuangan('', $post['singkatan']);
            $data['listCaraBayar'] = $this->actionListCaraBayar(2);
            $data['listStatusPeriksa'] = $this->actionDataStatusPeriksa($post['singkatan']);
        } catch (\Exception $e) {
            $data = [];
            // return $e->getMessage();
        }

        return $data;
    }

    /**
     * @todo cron otomatis tolak reservasi online
     * @author Aris Munandar <aris.m@docotel.com>
     */
    public function actionAutoRejectPendaftaranOnline()
    {
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d');
        $status = DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES;
        $statustolak = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK;
        //$sql = "SELECT * FROM pendaftaranol_t WHERE status_daftar_ol = '{$status}' AND tgl_pendaftaranol <= '{$dateNow}'";
        //$data = $connection->createCommand($sql)->queryAll();
        //if (!empty($data)) {
            $sql_status = "UPDATE pendaftaranol_t
                SET
                    status_daftar_ol = '{$statustolak}'
                WHERE
                    status_daftar_ol = '{$status}'
                    AND
                    tgl_pendaftaranol <= '{$dateNow}'
            ";
            $update_status = $connection->createCommand($sql_status);
            $update_status->execute();
        //}

        return [
            'message' => 'Status berhasil di tolak'
        ];
    }

     /**
     * @todo cron otomatis batal dalam 1 hari pemesanan kamar
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSetBatalKamar()
    {
        $connection = Yii::$app->db;
        $tanggal = DocoHelpers::getTanggalIndonesia();
        $dateNow = date('Y-m-d');
        $status = DocoConstants::VAR_BK;
        $sql = "SELECT * FROM infopemesanankamar_v WHERE statusbooking = '{$status}'";
        $data = $connection->createCommand($sql)->queryAll();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $kamartempattidur_id = $value['kamartempattidur_id'];
                $kamarruangan_id = $value['kamarruangan_id'];
                $jenis_kelamin = !empty($value['jeniskelamin']) ? $value['jeniskelamin'] : $value['jk'];
                $expire = strtotime($value['tgl_expired']);
                $today = strtotime("today midnight");
                if ($today > $expire) {
                    if (!empty($jenis_kelamin)) {
                        if ($jenis_kelamin == DocoConstants::VAR_LK) {
                            // status kamar laki2
                            $status_terisi = DocoConstants::VAR_KTTIL;
                            $status_kosong = DocoConstants::VAR_KTTKL;
                        } elseif ($jenis_kelamin == DocoConstants::VAR_PR) {
                            // status kamar perempuan
                            $status_terisi = DocoConstants::VAR_KTTIP;
                            $status_kosong = DocoConstants::VAR_KTTKP;
                        }

                        if (($value['kamarruangan_jenis'] == DocoConstants::VAR_JKF) OR ($value['kamarruangan_jenis'] == DocoConstants::VAR_JKC)) {
                            $kettempattidur_id = DocoConstants::VAR_KTTK;
                        } else {
                            $kettempattidur_id = $status_kosong;
                        }
                    }
                    $sql_tempat_tidur = "UPDATE kamartempattidur_m
                        SET
                            status_isi = false,
                            kettempattidur_id = {$kettempattidur_id}
                        WHERE
                            kamartempattidur_id = {$kamartempattidur_id}
                    ";
                    $update_tempat_tidur = $connection->createCommand($sql_tempat_tidur);
                    $update_tempat_tidur->execute();
                    $status_batal = DocoConstants::VAR_DTLK;
                    $bookingkamar_id = $value['bookingkamar_id'];
                    $sql_booking = "UPDATE bookingkamar_t
                        SET
                            status_booking = '{$status_batal}'
                        WHERE
                            status_booking = '{$status}'
                        AND bookingkamar_id = '{$bookingkamar_id}'
                    ";
                    $update_booking = $connection->createCommand($sql_booking);
                    $update_booking->execute();
                }
            }
        }

        return [
            'message' => 'Pemesanan kamar berhasil di batalkan'
        ];
    }
    public function actionGetTarifPaket()
    {
        $request = Yii::$app->request;
        $namaPaket = $request->get('tipepaket_nama');
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
        $listPaket = $request->get('list_paket');
        $kode = $request->get('kode');
        $limit = $request->get('limit', 10);
        $payload = new TarifPayloadTindakan;
        $payload->attributes = $request->get();

        if (!$payload->validate()) {
            return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }

        switch ($payload->instalasi_id) {
            case $this->constans->actionGetId('LAB'):
                $type = 'penunjang';
                $kategori = 'paket_lab';
                break;
            case $this->constans->actionGetId('RAD'):
                $type = 'penunjang';
                $kategori = 'paket_rad';
                break;
            case $this->constans->actionGetId('IBS'):
                $type = 'penunjang';
                $kategori = 'paket_operasi';
                break;
            case $this->constans->actionGetId('MCU'):
                $type = 'pelayanan';
                $kategori = 'paket';
                break;
            default:
                $type = 'pelayanan';
                $kategori = null;
                break;
        }

        $model = (new TarifTotalRsFn([
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

        if (!empty($namaPaket)) {
            $query->andWhere(['ILIKE', 'LOWER(tipepaket_nama)', strtolower($namaPaket)]);
        }

        if(!empty($namaJenis)){
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        if(!empty($listPaket)){
            $query->andWhere(['IN', 'tariftindakan_id', $listPaket]);
        }

        if (!empty($kode)) {
            $query->andWhere(['ILIKE', 'LOWER(kode)', strtolower($kode)]);
        }

        $result = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
            ],
        ]);

        return $result;
    }

    public function actionGetTarifTindakan()
    {
        $request = Yii::$app->request;
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
        $namaPemeriksaan = $request->get('daftartindakan_nama');
        $listTindakan = $request->get('list_tindakan');
        $kode = $request->get('kode');
        $limit = $request->get('limit', 10);
        $payload = new TarifPayloadTindakan;
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
            default:
                $type = 'pelayanan';
                $kategori = null;
                break;
        }

        $model = (new TarifTotalRsFn([
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

        if(!empty($namaJenis)){
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        if(!empty($namaPemeriksaan)){
            $query->andWhere(['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)]);
        }

        if(!empty($listTindakan)){
            $query->andWhere(['IN', 'tariftindakan_id', $listTindakan]);
        }

        if(!empty($kode)){
            $query->andWhere(['ILIKE', 'LOWER(kode)', strtolower($kode)]);
        }

        $result = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
            ],
        ]);

        return $result;
    }

    public function actionGetDataAsuransi()
    {
        $payload = Yii::$app->request->get();
        $result = [];
        try {
            $whereClause = [
                'pasien_id'=>isset($payload['pasien_id']) ? $payload['pasien_id'] : '',
                'penjamin_id'=>$payload['penjamin_id']
            ];
            $noAsuransi = $payload['no_asuransi'];

            $onlyOneRecord = true;
            if (!empty($noAsuransi)) {
                if (!isset($payload['onlyOneRecord']) || (isset($payload['onlyOneRecord']) && !$payload['onlyOneRecord'])) {
                    $onlyOneRecord = false;
                }
                unset($whereClause['pasien_id']);
                $whereClause['nokartuasuransi'] = $payload['no_asuransi'];
            }

            $query = AsuransiPasien::find()
                ->andWhere($whereClause)
                ->orderBy('asuransipasien_id DESC');
            if (isset($payload['groupedByNoAsuransi']) && $payload['groupedByNoAsuransi']) {
                $query = $query
                ->select([
                    'nokartuasuransi',
                    'namapemilikasuransi',
                    'nomorpokokperusahaan',
                    'asuransipasien_m.pasien_id',
                    'tgl_konfirmasi',
                    'status_konfirmasi',
                    'kelastanggunganasuransi_id',
                    'namaperusahaan',
                    "CONCAT(nokartuasuransi, ' - ', namapemilikasuransi) as value"
                ])
                ->joinWith(['pasien' => function($query){
                    $query->select(['pasien_id','no_rekam_medik', 'nama_pasien', 'namadepan', 'is_deleted']);
                }])
                ->andWhere([
                    'pasien_m.is_deleted' => false
                ]);
            }
            if ($onlyOneRecord) {
                $query = $query->asArray()->one();
            } else {
                $query = $query->asArray()->all();
            }
            return $query;
        } catch (\yii\db\Exception $e) {
            $result = [];
        }
        return $result;
    }

    public function actionListKabupatenNew($propinsi) {
        $data = Kabupaten::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'propinsi_id' => $propinsi ]);
        // $items = ArrayHelper::map($data->all(), 'kabupaten_id', 'kabupaten_nama');
        return $data->asArray()->all();
    }

    public function actionListKecamatanNew($kabupaten) {
        $data = Kecamatan::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'kabupaten_id' => $kabupaten ]);

        // $items = ArrayHelper::map($data->all(), 'kecamatan_id', 'kecamatan_nama');

        return $data->asArray()->all();
    }

    public function actionListKelurahanNew($kecamatan) {
        $data = Kelurahan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $data = $data->andWhere(['kecamatan_id'=>$kecamatan]);

        // $items = ArrayHelper::map($data->all(), 'kelurahan_id', 'kelurahan_nama');

        return $data->asArray()->all();
    }


    private function getAllInstalasi(){
        $model = new Instalasi;
        $query = $model->find()
                ->where(['is_active' => true, 'is_deleted' => false])
                ->orderBy(['instalasi_nama'=> SORT_DESC ]);
        $result = $query->all();

        return $result;
    }

    private function getAllRuangan(){
        $model = new Ruangan;
        $query = $model->find()
                ->where(['is_active' => true, 'is_deleted' => false, 'is_modul'=> true])
                ->orderBy(['ruangan_nama'=> SORT_ASC ]);
        $result = $query->all();

        return $result;
    }

    private function getAllPenjamin(){
        $model = new Penjamin;
        $query = $model->find()
                ->where(['is_active' => true, 'is_deleted' => false])
                ->orderBy(['penjamin_nama'=> SORT_ASC ]);
        $result = $query->all();

        return $result;
    }

    private function getAllKategoriTindakan(){
        $model = new KategoriTindakan;
        $query = $model->find()
                ->where(['is_active' => true, 'is_deleted' => false])
                ->orderBy(['kategoritindakan_nama'=> SORT_ASC ]);
        $result = $query->all();

        return $result;
    }

    public function actionListFilterTarifPelayanan(){

        try {
            $request = Yii::$app->request;
            $get = $request->get();

            $getAllInstalasi =  $this->getAllInstalasi();
            $arrInstalasi = ArrayHelper::map($getAllInstalasi, 'instalasi_id', 'instalasi_nama');

            $getAllRuangan =  $this->getAllRuangan();
            $arrRuangan = ArrayHelper::map($getAllRuangan, 'ruangan_id', 'ruangan_nama');

            $getAllPenjamin =  $this->getAllPenjamin();
            $arrPenjamin = ArrayHelper::map($getAllPenjamin, 'penjamin_id', 'penjamin_nama');

            $getAllKategoriTindakan =  $this->getAllKategoriTindakan();
            $arrKategoriTindakan = ArrayHelper::map($getAllKategoriTindakan, 'kategoritindakan_id', 'kategoritindakan_nama');

            $getAllKelasPelayanan = $this->actionGetListKelasPelayanan();
            $arrKelasPelayanan = ArrayHelper::map($getAllKelasPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');

            return [
                'instalasi' => $arrInstalasi,
                'ruangan' => $arrRuangan,
                'penjamin' => $arrPenjamin,
                'kategori_tindakan' => $arrKategoriTindakan,
                'kelas_pelayanan' => $arrKelasPelayanan,
            ];

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null, $name = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
        }

        if($name) {
            $result->andWhere(['lookup_name' => $name]);
        }

        $result->andWhere(['is_active' => TRUE]);

        return $result;
    }

    /**
     * @author ali.padilah@docotel.com DUPLICATE FROM MASTER
     * @modify ali.padilah@docotel.com
     *
     */
    public function actionGetGroupKonfig($jenisantrian_id = null)
    {
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

    public function actionGetPasienAutofill($nopesertabpjs)
    {
        $response_field = [
            'nik' => '',
            'namapasien' => '',
            'tanggallahir' => '',
            'jeniskelamin' => '',
            'alamatpasien' => '',
            'provinsi' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kelurahan' => ''
        ];
        $mapping_sex = ['L'=>15,'P'=>16];
        try {
            $model = new Bpjs;
            $req_peserta = $model->peserta($nopesertabpjs, date('Y-m-d'), false);
            if(isset($req_peserta['response']['peserta'])){
                $peserta = $req_peserta['response']['peserta'];
                $response_field['namapasien'] = isset($peserta['nama'])?$peserta['nama']:'';
                $response_field['jeniskelamin'] = isset($peserta['sex']) ? @$mapping_sex[$peserta['sex']] : '';
                $response_field['tanggallahir'] = isset($peserta['tglLahir']) ? date('d-m-Y',strtotime($peserta['tglLahir'])) : '';
                if(isset($peserta['nik']) && strlen($peserta['nik']) == 16){
                    $response_field['nik'] = $peserta['nik'];
                    $parsedNik = $this->parsingNik($peserta['nik']);
                    $response_field['provinsi'] = isset($parsedNik['kode_provinsi']) ? $parsedNik['kode_provinsi'] : '';
                    $response_field['kabupaten'] = isset($parsedNik['kode_kabupaten']) ? $parsedNik['kode_kabupaten'] : '';
                    $response_field['kecamatan'] = isset($parsedNik['kode_kecamatan']) ? $parsedNik['kode_kecamatan'] : '';
                    $response_field['tanggallahir'] = isset($parsedNik['tanggal_lahir']) ? $parsedNik['tanggal_lahir'] : '';
                    $response_field['jeniskelamin'] = isset($parsedNik['jenis_kelamin']) ? $parsedNik['jenis_kelamin'] : '';
                }
            }
            return $response_field;
        } catch (\yii\db\Exception $e) {
            return $response_field;
        } catch (\Exception $e) {
            return $response_field;
        }
    }

    protected function parsingNik($nik)
    {
        $parsed_response = [
            'kode_provinsi' => '',
            'kode_kabupaten' => '',
            'kode_kecamatan' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => ''
        ];
        try {
            if(strlen($nik) == 16){
                $parsed_response['kode_provinsi'] = substr($nik,0,2);
                $parsed_response['kode_kabupaten'] = substr($nik,2,2);
                $parsed_response['kode_kecamatan'] = substr($nik,4,2);
                $parsed_response['jenis_kelamin'] = substr($nik,6,2) - 40 >= 0 ? 16 : 15;
                $day_date = ($parsed_response['jenis_kelamin']) == 16 ? substr($nik,6,2) - 40 : substr($nik,6,2);
                $year_date_s = substr($nik,10,2);
                $year_date = $year_date_s > 24 ? '19'.$year_date_s : '20'.$year_date_s;
                $full_date = sprintf("%02d", $day_date).'-'.sprintf("%02d",substr($nik,8,2)).'-'.$year_date;
                $parsed_response['tanggal_lahir'] = date('d-m-Y',strtotime($full_date));

            }
            return $parsed_response;
        } catch (\Exception $e) {
            return $parsed_response;
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data total unsync
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetTotalDataUnsyc()
    {
        try {
            $statusSync = [];
            $statusSync['pasien'] = SyncPasien::find()->where(['is_sync' => false])->count();
            $statusSync['pendaftaran'] = SyncPendaftaranTransaksi::find()->where(['is_sync' => false])->count();
            return $statusSync;
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

    public function actionAutoRejectPendaftaranOl()
    {
        $data = PendaftaranOnline::find()
            ->andWhere([
                'status_daftar_ol'=>DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES,//564
            ])
            ->andWhere("
                (tgl_pendaftaranol < '" .  date('Y-m-d') . " 00:00:00') OR
                (tgl_pendaftaranol = '" .  date('Y-m-d') . " 00:00:00' AND jam_tutup <= '".date('H:i:s')."')
            ")
            ->all();

        $count = 0;
        foreach ($data as $datum) {
            $datum->status_daftar_ol = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK; //566
            $count = $datum->save() ? $count + 1 : $count;
        }

        return [
            'message' => 'Auto-Tolak Reservasi Online berhasil. Data ter-update : ' . $count
        ];
    }

    /**
     * @todo Ambil Konfigurasi System
     * @author Anggoro <tri.anggoro@docotel.com>
     * @date 2019-07-03
     */
    public function actionGetKonfigSystem()
    {
        $konfig = KonfigSystem::find()->limit(1)->one();
        return $konfig;
    }

    public function actionGetDataBayiNew($pendaftaran_id)
    {
        $result = InfoPasienIbuBayi::find();
        $result = $result->where(['pendaftaran_id' => $pendaftaran_id]);
        $result = $result->asArray()->one();

        return $result;
    }

    public function actionGetDataBayi($pendaftaran_id)
    {
        $model = new InfoPasienIbuBayi;
        $query = $model::find()->where(['pendaftaran_id' => $pendaftaran_id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSearchPendaftaran($term) {
        $model = new InfoDataPendaftaranView;
        $data = $model->find()
            ->where(['like', 'LOWER(no_pendaftaran)', strtolower($term)])
            ->orWhere(['like', 'LOWER(no_rekam_medik)', strtolower($term)])
            ->andWhere(['NOT',
                ['status_periksa' => [
                    DocoConstants::STATUS_PERIKSA_PLG,
                    DocoConstants::STATUS_PERIKSA_BTL_PRKS,
                    DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
                    DocoConstants::STATUS_RANAP_BATAL_RAWAT ]
                ]
                    ])
            ->andWhere(['is_stopakomodasi' => false ])
            ->limit(50);
        return ['data' => $data->all()];
    }

    public function actionGetPendaftaran($pendaftaran_id)
    {
        $result = InfoDataPendaftaranView::find()->where(['pendaftaran_id' => $pendaftaran_id])
    ->one();

        return $result;
    }

    public function actionValidateKunjungan() {
        $request = Yii::$app->request;
        $params = $request->post('params');
        $instalasi = $request->post('instalasi');
        $konfig = Cache::getKonfigSystem();
        $validasi = true;
        $result = [];

        switch ($instalasi) {
            case 'rajal':
                $validasi = $konfig['is_validasipendaftaranrj'] ?: false;
                break;
            case 'ranap':
                $validasi = $konfig['is_validasipendaftaranri'] ?: false;
                break;
            case 'igd':
                $validasi = $konfig['is_validasipendaftaranrd'] ?: false;
                break;
        }

        if($validasi) {
            $result = Pendaftaran::getKunjunganPasien($params);
        }

        return $result;
    }


    public function actionCekBayiKembar($nama_pasien)
    {
        $result = Pasien::find();
        $result = $result->where(['LIKE', 'nama_pasien', $nama_pasien]);
        $result = $result->andWhere(['tanggal_lahir' => date('Y-m-d')]);
        $result = $result->count();

        return $result;
    }

    public function actionCekNamaWilayah($wilayah_id, $tipe)
    {
        if($tipe == 1) {
            $query = Kabupaten::find()->where(['propinsi_id' => $wilayah_id])->one();
        }
        elseif($tipe == 2) {
            $query = Kecamatan::find()->where(['kabupaten_id' => $wilayah_id])->one();
        }
        else {
            $query = Kelurahan::find()->where(['kecamatan_id' => $wilayah_id])->one();
        }

        return $query;
    }

    public function actionGetPasienAsuransi($q, $penjamin_id = null)
    {
        $query = PasienAsuransiView::find()->select([
            'asuransipasien_id',
            'no_rekam_medik',
            'pasien_id',
            'nama_pasien',
            'nokartuasuransi',
            'tanggal_lahir',
            'namapemilikasuransi',
            'nomorpokokperusahaan',
            'namaperusahaan',
            'tgl_konfirmasi',
            'kelastanggunganasuransi_id',
            'masaberlakukartu',
            'nama_asuransi'
        ]);
        $payloadRequest = Yii::$app->request->get();
        if (isset($payloadRequest['is_ranap']) && $payloadRequest['is_ranap']) {
            $query = $query->andWhere([
                'nokartuasuransi' => $q
            ]);
        } else {
            $query = $query->andwhere([
                'ILIKE', 'nokartuasuransi', $q
            ]);
        }

        return [
            'data' => $query->andWhere([
                'penjamin_id' => (int) $penjamin_id
            ])
            ->orderBy(['asuransipasien_id' => SORT_DESC])
            ->all()
        ];
    }

    /**
     * Retrieve data kamar ruangan based on ruangan and kelaspelayanan
     *
     * @param String/Integer $ruanganId
     * @param String/Integer $kelasPelayananId
     * @author Tsani Nashrullah <tsani@docotel.com>
     * @return JSON
     **/
    public function actionKamarRuangan()
    {
        $payload = Yii::$app->request->get();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $query = $this->getKamarByRuangan()
            ->select(['kamarruangan_id', 'kamarruangan_nokamar'])
            ->where([
                'is_deleted' => false,
                'is_active' => true,
            ]);
            if (!empty($payload['ruanganId'])) {
                $query->andWhere(['ruangan_id' => $payload['ruanganId']]);
            }
            if (!empty($payload['kelasPelayananId'])) {
                $query->andWhere(['kelaspelayanan_id' => $payload['kelasPelayananId']]);
            }
            if (!empty($payload['klasifikasiKamarId'])) {
                $query->andWhere(['klasifikasikamar_id' => $payload['klasifikasiKamarId']]);
            }
            return [
                'data' => $query->asArray()->all(),
            ];
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    /**
     * Retrieve data Bed by ruangan
     *
     * @param String/Integer $jenis_id Jenis Kasus Penyakit
     * @param String/Integer $kelas_id Kelas Pelayanan
     * @param String/Integer $ruangan_id Ruangan
     * @param String/Integer $penjamin_id Penjamin
     * @param String/Integer $status_kamar Status Isi : [0 => 'Semua', 1 => 'Kosong', 2 => 'Telah Terisi']
     * @param Integer $page Page of request
     * @return type
     * @throws conditon
     **/
    public function actionListBedByRuangan()
    {
        $request = Yii::$app->request;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payload = $request->get();
        if (isset($payload['penjamin_id']) && !empty($payload['penjamin_id']) && isset($payload['gender']) && !empty($payload['gender'])) {
            $model = new InfoTarifAkomodasiView;
            $query = $model::find();
            $genderCode = (int)$payload['gender'] === DocoConstants::VAR_LK ? DocoConstants::VAR_JKLK : DocoConstants::VAR_JKW;

            $query->andWhere(['penjamin_id' => $payload['penjamin_id']])
                ->andWhere(['in', 'kamarruangan_jenis_id', [
                    $genderCode,
                    DocoConstants::VAR_JKF,
                    DocoConstants::VAR_JKC,
                ]]);
            if (isset($payload['jeniskasuspenyakit_id']) && !empty($payload['jeniskasuspenyakit_id']) && strtolower($payload['jeniskasuspenyakit_id']) !== strtolower('Semua')) {
                $query->andWhere(['jeniskasuspenyakit_id' => $payload['jeniskasuspenyakit_id']]);
            }
            if (isset($payload['kelaspelayanan_id']) && !empty($payload['kelaspelayanan_id']) && strtolower($payload['kelaspelayanan_id']) !== strtolower('Semua')) {
                $query->andWhere(['kelaspelayanan_id' => $payload['kelaspelayanan_id']]);
            }
            if (isset($payload['ruangan_id']) && !empty($payload['ruangan_id']) && strtolower($payload['ruangan_id']) !== strtolower('Semua')) {
                $query->andWhere(['ruangan_id' => $payload['ruangan_id']]);
            }
            if (isset($payload['kamarruangan_id']) && !empty($payload['kamarruangan_id']) && strtolower($payload['kamarruangan_id']) !== strtolower('Semua')) {
                $query->andWhere(['kamarruangan_id' => $payload['kamarruangan_id']]);
            }
            switch ($payload['status_kamar']) {
                case '1':
                    // only empty
                    $query->andWhere(['status_isi' => false]);
                    break;
                case '2':
                    // only assigned
                    $query->andWhere(['status_isi' => true]);
                    break;
            }
            $page = $payload['page'] ? $payload['page'] : 1;

            return [
                'data' => $query->asArray()->all(),
                'list-ruangan' => $query
                    ->select(['ruangan_id', 'ruangan_nama', 'kelaspelayanan_nama', 'jeniskasuspenyakit_nama', 'kamarruangan_nokamar', 'kelaspelayanan_id', 'kamarruangan_id'])
                    ->distinct()
                    ->limit(11)
                    ->offset(($page - 1) * 10)
                    ->orderBy(['ruangan_nama' => SORT_ASC, 'kamarruangan_nokamar' => SORT_ASC])
                    ->all()
            ];
        } else {
            return [
                'data' => [],
                'list-ruangan' => []
            ];
        }
    }

    /**
     * Retrieve data Bed by ruangan default
     *
     * @param String/Integer $jenis_id Jenis Kasus Penyakit
     * @param String/Integer $kelas_id Kelas Pelayanan
     * @param String/Integer $ruangan_id Ruangan
     * @param String/Integer $penjamin_id Penjamin
     * @param String/Integer $status_kamar Status Isi : [0 => 'Semua', 1 => 'Kosong', 2 => 'Telah Terisi']
     * @param Integer $page Page of request
     * @return type
     * @throws conditon
     **/
    public function actionListBedByRuanganDefault()
    {
        $request = Yii::$app->request;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payload = $request->get();
        $idRuangan = $payload['ruangan_id'];
        $idPenjamin = $payload['penjamin_id'];
        $idKelasPelayanan = $payload['kelaspelayanan_id'];

        if ($payload['ruangan_id'] == 'Semua') {
            $idRuangan = 0;
        }

        if ($payload['ruangan_id'] == '') {
            $idRuangan = 0;
        }

        if ($payload['penjamin_id'] == 'Semua') {
            $idPenjamin = 0;
        }

        if ($payload['penjamin_id'] == '') {
            $idPenjamin = 0;
        }

        if ($payload['kelaspelayanan_id'] == 'Semua') {
            $idKelasPelayanan = 0;
        }

        if ($payload['kelaspelayanan_id'] == '') {
            $idKelasPelayanan = 0;
        }

        $idRuangan = (int)$idRuangan;
        $idPenjamin = (int)$idPenjamin;
        $idKelasPelayanan = (int)$idKelasPelayanan;
        $tipeTarif = 'kamar';
        $gender = (int)$payload['gender'] === DocoConstants::VAR_LK ? DocoConstants::VAR_JKLK : DocoConstants::VAR_JKW;
        $where = "WHERE kamarruangan_jenis IN (".$gender.",".DocoConstants::VAR_JKF.",".DocoConstants::VAR_JKC.")";
        $where = $where." AND instalasi_id NOT IN ( ".DocoConstants::INST_ID_BEDAH.",".DocoConstants::INST_ID_MCU." )";

        if (isset($payload['jeniskasuspenyakit_id']) && !empty($payload['jeniskasuspenyakit_id']) && strtolower($payload['jeniskasuspenyakit_id']) !== strtolower('Semua')) {
            $where = $where." AND jeniskasuspenyakit_id = ".$payload['jeniskasuspenyakit_id']."";
        }

        if (isset($payload['kamarruangan_id']) && !empty($payload['kamarruangan_id']) && strtolower($payload['kamarruangan_id']) !== strtolower('Semua')) {
            $where = $where." AND kamarruangan_id = ".$payload['kamarruangan_id']."";
        }

        if (isset($payload['klasifikasikamar_id']) && !empty($payload['klasifikasikamar_id']) && strtolower($payload['klasifikasikamar_id']) !== strtolower('Semua')) {
            $where = $where." AND klasifikasikamar_id = ".$payload['klasifikasikamar_id']."";
        }

        if(isset($payload['is_pasien_titipan']) && !empty($payload['is_pasien_titipan']) && $payload['is_pasien_titipan'] == true) {
            $payload['status_kamar'] = 0;
        }

        switch ($payload['status_kamar']) {
            case '1':
                // only empty
                $where = $where." AND status_isi = false";
            break;
            case '2':
                // only assigned
                $where = $where." AND status_isi = true";
            break;
            default:
                $where = $where;
            break;
        }

        $listRuangan = Yii::$app->db->createCommand('
            SELECT DISTINCT
                ruangan_id,
                ruangan_nama,
                kelaspelayanan_nama,
                jeniskasuspenyakit_nama,
                kamarruangan_nokamar,
                kelaspelayanan_id,
                kamarruangan_id,
                harga_tariftindakan,
				klasifikasikamar_id,
				klasifikasikamar_nama
            FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:tipe_tarif)
            '.$where.'
        ')
        ->bindParam(':ruangan_id',$idRuangan)
        ->bindParam(':penjamin_id', $idPenjamin)
        ->bindParam(':kelaspelayanan_id', $idKelasPelayanan)
        ->bindParam(':tipe_tarif', $tipeTarif)
        ->queryAll();

        $listAllRuangan = Yii::$app->db->createCommand('
            SELECT *
            FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:tipe_tarif)
            '.$where.'')
        ->bindParam(':ruangan_id',$idRuangan)
        ->bindParam(':penjamin_id', $idPenjamin)
        ->bindParam(':kelaspelayanan_id', $idKelasPelayanan)
        ->bindParam(':tipe_tarif', $tipeTarif)
        ->queryAll();

        return [
            'data' => $listAllRuangan,
            'list-ruangan' => $listRuangan
        ];
    }

    /**
     * Checking ruangan if it has akomodasi by penjamin and kelaspelayanan_id
     *
     * @param Integer $ruangan_id ruangan
     * @param Integer $kelaspelayanan_id kelas pelayanan
     * @param Integer $penjamin_id penjamin
     * @return JSON
     **/
    public function actionCekAkomodasiRuangan()
    {
        $payload = Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payloadValidation = [
            'ruangan_id' => ArrayHelper::getValue($payload, 'ruangan_id'),
            'kelaspelayanan_id' => ArrayHelper::getValue($payload, 'kelaspelayanan_id'),
            'penjamin_id' => ArrayHelper::getValue($payload, 'penjamin_id'),
            'kamarruanganId' => ArrayHelper::getValue($payload, 'kamarruanganId'),
        ];
        $validation = DynamicModel::validateData($payloadValidation, [
            [['ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'kamarruanganId'], 'required'],
        ]);

        if ($validation->hasErrors()) {

            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $validation->errors,
                'data' => []
            ];
        } else {
            try {
                $response = [
                    'data' => (new Ruangan)->recordWithAkomodasi($payload['kamarruanganId'], $payload['penjamin_id'], $payload['kelaspelayanan_id']),
                    'message' => ''
                ];
                if (empty($response['data'])) {
                    \Yii::$app->response->statusCode = 400;
                    $response['message'] = 'Tarif akomodasi pada ruangan yang dipilih belum tersedia.';
                } else {
                    \Yii::$app->response->statusCode = 200;
                    $response['message'] = 'Berhasil mendapatkan akomodasi.';
                }
                return $response;
            } catch (Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Terjadi kesalahan',
                    'manRead' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'Line' => $e->getLine(),
                ];
            }
        }
    }

    /**
     * Checking ruangan if it has akomodasi by penjamin and kelaspelayanan_id
     *
     * @param Integer $ruangan_id ruangan
     * @param Integer $kelaspelayanan_id kelas pelayanan
     * @param Integer $penjamin_id penjamin
     * @return JSON
     **/
    public function actionCekAkomodasiRuanganDefault()
    {
        $payload = Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $payloadValidation = [
            'ruangan_id' => ArrayHelper::getValue($payload, 'ruangan_id'),
            'kelaspelayanan_id' => ArrayHelper::getValue($payload, 'kelaspelayanan_id'),
            'penjamin_id' => ArrayHelper::getValue($payload, 'penjamin_id'),
            'kamarruanganId' => ArrayHelper::getValue($payload, 'kamarruanganId'),
            'kamartempattidurId' => ArrayHelper::getValue($payload, 'kamartempattidurId', ''),
        ];
        $validation = DynamicModel::validateData($payloadValidation, [
            [['ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'kamarruanganId'], 'required'],
        ]);
        $type = 'kamar';

        if ($validation->hasErrors()) {
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => $validation->errors,
                'data' => []
            ];
        } else {
            try {
                $model = (new TarifTotalKamarRsFn([
                    'extParam' => [
                        $payload['ruangan_id'],
                        $payload['penjamin_id'],
                        $payload['kelaspelayanan_id'],
                        $type
                    ]
                ]));
                $query = $model::find()
                ->andWhere([
                    'kamarruangan_id' => $payload['kamarruanganId']
                ]);

                if ( !empty($payload['kamartempattidurId']) ) {
                    $query->andWhere([
                        'kamartempattidur_id' => $payload['kamartempattidurId']
                    ]);
                }

                $query = $query->one();

                if (empty($query)) {
                    \Yii::$app->response->statusCode = 400;
                    $response['message'] = 'Tarif akomodasi pada ruangan yang dipilih belum tersedia.';
                } else {
                    \Yii::$app->response->statusCode = 200;
                    $response['message'] = 'Berhasil mendapatkan akomodasi.';
                }
                return $response;
            } catch (Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Terjadi kesalahan',
                    'manRead' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'Line' => $e->getLine(),
                ];
            }
        }
    }

    /**
     * Get record bayi by noRekamMedik
     *
     * @param Integer $pasienId
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionBayiByPasien()
    {
        $noRekamMedik = Yii::$app->request->get('noRekamMedik');
        $record = InfoPasienIbuBayi::find()
            ->select([
                'pasien_id',
                'no_pendaftaran',
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasienpulang_id',
                'no_rekam_medik',
                'nama_pasien AS nama_ibu',
                'jeniskelamin_id_bayi as jenis_kelamin',
                'alamat_pasien',
                'rt',
                'rw',
                'berat_badan',
                'tinggi_badan',
                'bayi_urut as nomor_bayi',
                'propinsi_id',
                'propinsi_nama',
                'kabupaten_id',
                'kabupaten_nama',
                'kecamatan_id',
                'kecamatan_nama',
                'kelurahan_id',
                'kelurahan_nama',
                'kelahiranbayi_id',
                'golongandarah_id',
                'no_pendaftaranbayi',
                'is_registered' => new \yii\db\Expression("CASE WHEN pendaftaranbaru_id IS NOT NULL THEN true ELSE false END"),
                'kondisi_bayi',
                'no_telepon_pasien',
                'no_tempattidur',
                'kamarruangan_nokamar',
                'jeniskasuspenyakit_id',
                'penanggungjawab_nama',
                'kelaspelayanan_id',
                'ruangan_id',
                'ruangan_nama',
                'kelaspelayanan_nama',
                'kamartempattidur_id',
                'kamarruangan_id',
                'jeniskelamin_id_bayi',
                'goldarah_by',
                'tgl_lahir_by',
                'ruangan_by',
                'kelas_id_by',
                'kelas_nama_by',
                'ruangan_id_by',
                'agama'
            ])
            ->andWhere([
                'no_rekam_medik' => $noRekamMedik,
                'pasienpulang_id' => null
            ])
            ->andWhere(['not',
                ['kelahiranbayi_id' => null]
            ])
            ->orderBy('bayi_urut')
            ->asArray()
            ->all();
        // $record['infoRs'] = $profileRs;
        return $record;
    }

    public function actionGetDetailKunjungan()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik');
        $model = new InfoKunjunganRsView;
        $query = $model::find()->where(['no_rekam_medik' => $no_rekam_medik]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionCekSep()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $nosep = trim($post['nosep']);
        $pendaftaran_id = trim($post['pendaftaran_id']);

        if(empty($pendaftaran_id)) {
            $model = Bpjs::find()->where(['nosep' => $nosep])->one();
        } else {
            $pendaftaranRanap = Pendaftaran::find()->select([
                'COALESCE(prev_pendaftaran_id, '.new \yii\db\Expression("(additional_data::json->>'pendaftaranasal_id')::integer").' ) AS prev_pendaftaran_id'
            ])->where([
                'pendaftaran_id' => $pendaftaran_id
            ])->scalar();

            if(!empty($pendaftaranRanap)) {
                $model = Bpjs::find()->where(['nosep' => $nosep])
                    ->andWhere(['not', ['pendaftaran_id' => $pendaftaranRanap]])
                    ->one();
            } else {
                $model = Bpjs::find()->where(['nosep' => $nosep])
                    ->andWhere(['not', ['pendaftaran_id' => $pendaftaran_id]])
                    ->one();
            }
        }

        if($model) {
            return false;
        }

        return true;
    }

    public function actionGetNoAsuransi()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik', null);
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = empty($request->get('penjamin_id')) || $request->get('penjamin_id') == "null" ? null : $request->get('penjamin_id');
        $dataPasien = PasienV::find()->where(['no_rekam_medik' => $no_rekam_medik])->one();
        $result = [];
        if($dataPasien) {
            if(!empty($dataPasien) && $carabayar_id != DocoConstants::PENJAMIN_UMUM) {
                $result = AsuransiPasien::find()->where([
                    'pasien_id' => $dataPasien->pasien_id,
                    'carabayar_id' => $carabayar_id,
                    'penjamin_id' => $penjamin_id,
                ])->orderBy(['asuransipasien_id' =>SORT_DESC])->one();
            }

            return [
                'nama_pasien' => $dataPasien->nama_pasien,
                'data_asuransi' => $result
            ];
        }

        return $result;
    }

    /**
     * [actionGetConfigKelasPelayanan get konfig kelas pelayanan untuk kebutuhan di pendaftaran]
     * @author Budi
     * @param  [type] $id [description]
     * @return [array]
     */
    public function actionGetConfigKelasPelayanan($id, $asalrujukan_id = 1){
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if($asalrujukan_id == 1 && $ruangan_id != DocoConstants::RUANGAN_PENDAFTARAN_RANAP) {
            $cache = Cache::getConfigKelasPelayanan();
            $result = ArrayHelper::map($cache,'kelaspelayanan_id','kelaspelayanan_nama');
        } else {
            $result = $this->actionGetRuanganKelasPelayanan($id);
        }

        return $result;
    }

    public function actionDetailPaketMcu()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tipepaket_id = $get['tipepaket_id'];
        $result = [];
        try {
            $model = new MasterPaketMcuView;
            $query = $model::find();
            if($tipepaket_id) {
                $query->select(['detail']);
                $query->where(['tipepaket_id' => $tipepaket_id]);
            }

            $result = new ActiveDataProvider([
                'query' => $query->asArray(),
            ]);
        } catch (\yii\db\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public function actionGetProfileRs()
    {
        $profilRs = Cache::getProfileRs();
        $kota = '-';
        $namaRs = '-';
        if (!empty($profilRs['nama_rumahsakit'])) {
            $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
            if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
                $pattern = "KOTA ADM. ";
            }
            elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
                $pattern = "KAB. ADM. ";
            }
            elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
                $pattern = "KAB. ";
            }
            elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
                $pattern = "KOTA ";
            }

            $kota = str_replace($pattern,"", $profilRs['kota']);
        }

        return [
            'namaRs' => $namaRs,
            'kota' => $kota,
        ];
    }

    /*
    * author: Sigit
    * date: 18-11-2020
    * description: Get nomor urut berdasarkan jadwal dokter / jadwla poliklinik
    * params needed: ruangan_id (int), dokter_id (int)
    */
    public function actionGetNomorUrut($ruangan_id = null, $dokter_id = null, $antrianExcepted = '', $date = null, $no_antrian_user= null)
    {
        try {
            $konfig = Cache::getKonfigSystem();
            $kuota = 100;
            $getHari = (empty($date)) ? 'NOW' : $date;
            $idListHari = [
                'Mon' => 75,
                'Tue' => 76,
                'Wed' => 77,
                'Thu' => 78,
                'Fri' => 79,
                'Sat' => 80,
                'Sun' => 81
            ];
            $hari_id = $idListHari[date("D", strtotime($getHari))];
            $kuota_antrian = $konfig['kuota_antrian'];
            $prefix = KonfigAntrian::find()->where(['jenisantrian_id' => DocoConstants::VAR_JA_P, 'is_active' => true, 'is_deleted' => false])->one()->kode_antrian;

            if ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                $model = JadwalPoliklinik::find()
                ->select([
                    'SUM(jadwalbukapoli_m.maxantrian_poli) AS maxantrian_poli',
                    'SUM(jadwalbukapoli_m.kuota_online) AS kuota_online'
                ])
                ->leftJoin(JadwalDokter::tableName(), JadwalDokter::tableName().'.jadwalbukapoli_id = '.JadwalPoliklinik::tableName().'.jadwalbukapoli_id')
                ->where([
                    JadwalPoliklinik::tableName().'.hari' => $hari_id,
                    JadwalPoliklinik::tableName().'.is_active' => true,
                    JadwalPoliklinik::tableName().'.is_deleted' => false,
                    JadwalDokter::tableName().'.is_active' => true,
                    JadwalDokter::tableName().'.is_deleted' => false
                ]);

                if ($ruangan_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.ruangan_id' => $ruangan_id]);
                }

                if ($dokter_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.pegawai_id' => $dokter_id]);
                }

                $model = $model->one();

                if ($model) {
                    $kuota = $model['maxantrian_poli'] + $model['kuota_online'];
                }
            } elseif ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) {
                $model = JadwalDokter::find()
                ->select([
                    'SUM(jadwaldokter_m.maximumantrian) AS maximumantrian',
                    'SUM(jadwaldokter_m.kuota_online) AS kuota_online',
                    'SUM(jadwaldokter_m.kuota_total) AS kuota_total'
                ])
                ->leftJoin(JadwalPoliklinik::tableName(), JadwalPoliklinik::tableName().'.jadwalbukapoli_id = '.JadwalDokter::tableName().'.jadwalbukapoli_id')
                ->where([
                    JadwalDokter::tableName().'.is_active' => true,
                    JadwalDokter::tableName().'.is_deleted' => false,
                    JadwalPoliklinik::tableName().'.hari' => $hari_id,
                    JadwalPoliklinik::tableName().'.is_active' => true,
                    JadwalPoliklinik::tableName().'.is_deleted' => false
                ]);

                if ($ruangan_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.ruangan_id' => $ruangan_id]);
                }

                if ($dokter_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.pegawai_id' => $dokter_id]);
                }

                $model = $model->one();
                if ($model) {
                    if($konfig['is_reservasi'] == true) {
                        $kuota = $model['maximumantrian'] + $model['kuota_online'];
                    } else {
                        $kuota = $model['kuota_total'];
                    }
                }
            }

            return $this->generateNomorUrut($prefix, $kuota, $ruangan_id, $dokter_id, $antrianExcepted, $date, $no_antrian_user);

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

    /*
    * author: iqbal
    * date: 11-12-2022
    * description: Get nomor urut berdasarkan jadwal dokter / jadwla poliklinik tambahan carabayar case di MHBG
    * params needed: ruangan_id (int), dokter_id (int), carabayar_id(int)
    */
    public function actionGetNomorUrutType($ruangan_id = null, $dokter_id = null, $antrianExcepted = '', $date = null, $no_antrian_user= null, $carabayar_id= null)
    {
        try {
            $db = Yii::$app->db;
            $konfig = Cache::getKonfigSystem();
            $kuota = 100;
            $getHari = (empty($date)) ? 'NOW' : $date;
            $idListHari = [
                'Mon' => 75,
                'Tue' => 76,
                'Wed' => 77,
                'Thu' => 78,
                'Fri' => 79,
                'Sat' => 80,
                'Sun' => 81
            ];
            $hari_id = $idListHari[date("D", strtotime($getHari))];
            $kuota_antrian = $konfig['kuota_antrian'];
            $queryPrefix = KonfigAntrian::find()->where([
                'jenisantrian_id' => DocoConstants::VAR_JA_P,
                'is_active' => true,
                'is_deleted' => false]);
            if($carabayar_id == DocoConstants::PENJAMIN_UMUM){
                $queryPrefix->andWhere([
                    'groupcarabayar_id' => DocoConstants::GROUP_UMUM
                ]);
            }else{
                $queryPrefix->andWhere(['not in', 'groupcarabayar_id', [DocoConstants::GROUP_UMUM]]);
            }

            $prefix = $queryPrefix->one()->kode_antrian;
            if ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK) {
                $model = JadwalPoliklinik::find()
                ->select([
                    'SUM(jadwalbukapoli_m.maxantrian_poli) AS maxantrian_poli',
                    'SUM(jadwalbukapoli_m.kuota_online) AS kuota_online'
                ])
                ->leftJoin(JadwalDokter::tableName(), JadwalDokter::tableName().'.jadwalbukapoli_id = '.JadwalPoliklinik::tableName().'.jadwalbukapoli_id')
                ->where([
                    JadwalPoliklinik::tableName().'.hari' => $hari_id,
                    JadwalPoliklinik::tableName().'.is_active' => true,
                    JadwalPoliklinik::tableName().'.is_deleted' => false,
                    JadwalDokter::tableName().'.is_active' => true,
                    JadwalDokter::tableName().'.is_deleted' => false
                ]);

                if ($ruangan_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.ruangan_id' => $ruangan_id]);
                }

                if ($dokter_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.pegawai_id' => $dokter_id]);
                }

                $model = $model->one();

                if ($model) {
                    $kuota = $model['maxantrian_poli'] + $model['kuota_online'];
                }
            } elseif ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER) {
                $model = JadwalDokter::find()
                ->select([
                    'SUM(jadwaldokter_m.maximumantrian) AS maximumantrian',
                    'SUM(jadwaldokter_m.kuota_online) AS kuota_online',
                    'SUM(jadwaldokter_m.kuota_total) AS kuota_total'
                ])
                ->leftJoin(JadwalPoliklinik::tableName(), JadwalPoliklinik::tableName().'.jadwalbukapoli_id = '.JadwalDokter::tableName().'.jadwalbukapoli_id')
                ->where([
                    JadwalDokter::tableName().'.is_active' => true,
                    JadwalDokter::tableName().'.is_deleted' => false,
                    JadwalPoliklinik::tableName().'.hari' => $hari_id,
                    JadwalPoliklinik::tableName().'.is_active' => true,
                    JadwalPoliklinik::tableName().'.is_deleted' => false
                ]);

                if ($ruangan_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.ruangan_id' => $ruangan_id]);
                }

                if ($dokter_id != 'Loading ...') {
                    $model->andWhere([JadwalDokter::tableName().'.pegawai_id' => $dokter_id]);
                }

                $model = $model->one();
                if ($model) {
                    if($konfig['is_reservasi'] == true) {
                        $kuota = $model['maximumantrian'] + $model['kuota_online'];
                    } else {
                        $kuota = $model['kuota_total'];
                    }
                }
            }

            //get toal Antrian By ruangan dan dokter
            $startDate = date('Y-m-d 00:00:00', strtotime($date));
            $endDate = date('Y-m-d 23:59:59', strtotime($date));
            $jenisAntrian = DocoConstants::VAR_JA_P;
            $sqlRuangan = ($ruangan_id != 'Loading ...') ?  "AND (ruangan_id={$ruangan_id})":'';
            $sqlDokter = ($dokter_id != 'Loading ...') ?  "AND (pegawai_id={$dokter_id})":'';
            $totalAntrian = $db->createCommand("
                    SELECT count(no_antrian) AS total_antrian
                    FROM antrian_t
                    WHERE (jenisantrian_id={$jenisAntrian})
                    AND (pendaftaran_id IS NOT NULL)
                    AND (tgl_antrian BETWEEN '{$startDate}' AND '{$endDate}')
                    AND (is_deleted=false)
                    {$sqlRuangan}
                    {$sqlDokter}
                ")->queryOne();
            $resultTotanAntrian = isset($totalAntrian) ?$totalAntrian['total_antrian']:0;
            $kuota = $kuota - $resultTotanAntrian;

            return $this->generateNomorUrut($prefix, $kuota, $ruangan_id, $dokter_id, $antrianExcepted, $date, $no_antrian_user, $carabayar_id);

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

    /*
    * author: Sigit
    * date: 18-11-2020
    * description: Generate nomor urut berdasarkan jadwal dokter / jadwal poliklinik
    * params needed: prefix (string), kuota (int), ruangan_id (int), dokter_id (int), no_antrian (string)
    */
    private function generateNomorUrut($prefix, $kuota, $ruangan_id, $dokter_id, $antrianExcepted = '', $date = null, $no_antrian_user=null,$carabayar_id=null)
    {
        $noAntrian = array();
        $nomorUrut = array();
        $date = (empty($date)) ? 'NOW' : $date;

        if ($kuota != 0) {
            /** Baca batal pendaftaran_ol */
            $subQryPenOl =  (new \yii\db\Query())->from('pendaftaranol_t')
                ->select(['antrian_id'])
                ->where(['"tgl_pendaftaranol"::date'=>  date('Y-m-d', strtotime($date))])
                ->andWhere([
                    'status_daftar_ol' => DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK,
                    'ruangan_id' => $ruangan_id
                ]);

            $kuotaFromBatalOnline = (new \yii\db\Query())->from('antrian_t')
                ->select(['antrian_id'])
                ->where(['IN', 'antrianasal_id', $subQryPenOl])
                ->count();

            if(!empty($kuotaFromBatalOnline)  && $kuotaFromBatalOnline != 0) {
                $kuota = $kuota + $kuotaFromBatalOnline;
            }

            for ($i=1; $i < $kuota+1; $i++) {
                $nomorUrutAntrian = str_pad($i, 3, '0', STR_PAD_LEFT);
                $nomorUrut[$prefix.$nomorUrutAntrian] = $nomorUrutAntrian;
            }
        }

        $pickedNomorUrut = Antrian::find()
        ->select([
            'no_antrian'
        ])
        ->where([
            'jenisantrian_id' => DocoConstants::VAR_JA_P
        ]);

        if(!is_null($carabayar_id)){

            if($carabayar_id == DocoConstants::PENJAMIN_UMUM){
                $pickedNomorUrut->andWhere([
                    'carabayar_id' => $carabayar_id
                ]);
            }else{
                $pickedNomorUrut->andWhere(['not in', 'carabayar_id', [DocoConstants::PENJAMIN_UMUM]]);
            }

            $pickedNomorUrut->andWhere(['IS NOT', 'pendaftaran_id', null]);
        }

        $pickedNomorUrut->andWhere(['BETWEEN', 'tgl_antrian', date('Y-m-d 00:00:00', strtotime($date)), date('Y-m-d 23:59:59', strtotime($date))]);


        if ($ruangan_id != 'Loading ...') {
            $pickedNomorUrut->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($dokter_id != 'Loading ...') {
            $pickedNomorUrut->andWhere(['pegawai_id' => $dokter_id]);
        }

        if(!is_null($no_antrian_user)){
            $pickedNomorUrut->andWhere(['<>', 'no_antrian', $no_antrian_user]);
        }

        $pickedNomorUrut = $pickedNomorUrut->all();

        if (!empty($pickedNomorUrut)) {
            foreach ($pickedNomorUrut as $key => $value) {
                if ($value['no_antrian'] != $antrianExcepted) {
                    $noAntrian[$value['no_antrian']] = str_replace($prefix, '', $value['no_antrian']);
                }
            }
        }
        // before
        return array_diff($nomorUrut, $noAntrian);
        // after
        // $nomor_urut_available = array_diff($nomorUrut, $noAntrian);
        // return array_diff($nomorUrut, $noAntrian);
    }

    public function actionGetPenanggungJawab()
    {
        $response = [];
        $request = Yii::$app->request;
        $post = $request->post();

        if(empty($post['penanggungjawab_id'])) {
            $whereClause = [
                'pasien_id'=> $post['pasien_id'],
            ];
        } else {
            $whereClause = [
                'penanggungjawab_id'=> $post['penanggungjawab_id'],
            ];
        }

        $response = PenanggungJawab::find()
        ->where($whereClause)
        ->one();
        return $response;
    }

    public function actionListBagian() {
        $request = Yii::$app->request;
        $carabayar_id = $request->get('carabayar_id', null);
        $model = SyRuanganCaraBayarV::find()
            ->select(['ruangancarabayar_id', 'ruangcarabayar_nama'])
            ->where(['carabayar_id' => $carabayar_id])
            ->orderBy('ruangcarabayar_nama');

        return $model->asArray()->all();
    }

    /**
     * @function : pack data styp
     */
    public function actionPackDataSty()
    {
        $request = Yii::$app->request;
        $data = $bagian = $kabupaten = $kecamatan = $rujukandari = [];

        $bagian = Cache::getListBagianSty();
        $rujukandari = Cache::getRujukanStyp();

        $prosedurMasuk = array_filter($bagian, function($v, $k) {
            return $v['instalasi_id'] == 1 || $v['instalasi_id'] == 2;
        }, ARRAY_FILTER_USE_BOTH);
        $kabupaten = $this->actionListKabupatenNew($request->get('propinsi', null));
        $kecamatan = $this->actionListKecamatanNew($request->get('kabupaten', null));

        $data = [
            'bagian' => $bagian,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'prosedurMasuk' => $prosedurMasuk,
            'rujukandari' => $rujukandari
        ];

        return $data;
    }

    /**
     * @method get last penanggung
     *
     * @param int pasien_id
     */
    public function actionLastPenanggung($id)
    {

        $data = PenanggungJawab::find()
            ->where(['pasien_id' => $id])
            ->orderBy(['penanggungjawab_id' => SORT_DESC])
            ->asArray()
            ->one();

        return $data;
    }

    public function actionGetKunjunganSebelumnya()
    {
        $request = Yii::$app->request;
        $id = $request->get('pasien_id', null);
        $pendaftaran_id = $request->get('pendaftaran_id', null);

        if($id) {
            $condition = [
                'pasien_id' => $id
            ];
        } else {
            $condition = [
                'pendaftaran_id' => $pendaftaran_id
            ];
        }

        return InfoRegisV::find()
            ->where($condition)
            ->orderBy([
                'pendaftaran_id' => SORT_DESC
            ])
            ->asArray()
            ->one();
    }

    public function actionGetDataLookupTransaksi()
    {
        return Cache::Lookuptransaksi();
    }

    public function actionGetGradePenjamin()
    {
        $request = Yii::$app->request;
        $model = PenjaminGradeView::find();
        $penjaminId = $request->get('penjamin_id');

        if(!empty($penjaminId)) {
            $model = $model->where([
                'penjamin_id' => $penjaminId
            ]);
        }

        return $model->orderBy(['grade' => SORT_ASC])->all();
    }

    public function actionKlasifikasiKamar($mapping=true) {
        $data = KlasifikasiKamar::find()
            ->where(['is_active' => true])
            ->orderBy(['klasifikasikamar_nama' => SORT_ASC]);

        if ($mapping) {
            $items = ArrayHelper::map($data->all(), 'klasifikasikamar_id', 'klasifikasikamar_nama');
        } else {
            $items = $data->all();
        }

        return $items;
    }

    public function actionValidatePembayaran()
    {
        $request = Yii::$app->request;
        $pendaftaranId = (int) $request->get('pendaftaran_id');
        $msg = '';

        $konfigSystem = Cache::getKonfigSystem();

        $dataPendaftaran = Pendaftaran::find()
        ->select('status_bayar')
        ->where(['pendaftaran_id' => $pendaftaranId])
        ->asArray()
        ->one();

        if($dataPendaftaran['status_bayar'] == DocoConstants::LUNAS) {
            $msg = 'Pasien ini telah melakukan pembayaran';
        }else{
            $getDataPembayaran = TindakanPelayanan::find()
            ->where(['pendaftaran_id' => $pendaftaranId])
            ->andWhere(new \yii\db\Expression('pembayaran_id IS NOT NULL'))
            ->count();

            if($getDataPembayaran > 0) {
                $msg = 'Pasien ini telah melakukan pembayaran';
            }
        }

        return [
            'msg' => $msg,
            'is_konfig' => $konfigSystem['is_validasi_pembayaran']
        ];
    }

    public function actionFilterRuangan()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get('instalasi_id');
        $model = Ruangan::find()->select([
            'ruangan_id AS id',
            'ruangan_nama AS text',
        ]);
        if ($instalasi_id) {
            $model->andWhere(['instalasi_id' => $instalasi_id]);
        }
        $model->andWhere([
            'is_active' => true,
            'is_deleted' => false,
        ])->orderBy(['ruangan_nama' => SORT_ASC]);
        $ruangan = $model->asArray()->all();

        return $ruangan;
    }

    protected function defaultBedByRuanganFilter($request, &$where, &$ruangan_id, &$penjamin_id, &$kelaspelayanan_id)
    {
        $ruangan_id = $request->get('ruangan_id', '');
        $penjamin_id = $request->get('penjamin_id', '');
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', '');
        $gender = $request->get('gender', null);
        $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id', null);
        $kamarruangan_id = $request->get('kamarruangan_id', null);
        $klasifikasikamar_id = $request->get('klasifikasikamar_id', null);
        $is_pasien_titipan = $request->get('is_pasien_titipan', null);
        $status_kamar = $request->get('status_kamar', null);

        $ruangan_id = ($ruangan_id == 'Semua' || $ruangan_id == '') ? 0 : $ruangan_id;
        $penjamin_id = ($penjamin_id == 'Semua' || $penjamin_id == '') ? 0 : $penjamin_id;
        $kelaspelayanan_id = ($kelaspelayanan_id == 'Semua' || $kelaspelayanan_id == '') ? 0 : $kelaspelayanan_id;

        $ruangan_id = (int)$ruangan_id;
        $penjamin_id = (int)$penjamin_id;
        $kelaspelayanan_id = (int)$kelaspelayanan_id;
        $gender = (int)$gender === DocoConstants::VAR_LK ? DocoConstants::VAR_JKLK : DocoConstants::VAR_JKW;
        $where = "WHERE kamarruangan_jenis IN (".$gender.",".DocoConstants::VAR_JKF.",".DocoConstants::VAR_JKC.")";
        $where = $where." AND instalasi_id NOT IN ( ".DocoConstants::INST_ID_BEDAH.",".DocoConstants::INST_ID_MCU." )";

        if (isset($jeniskasuspenyakit_id) && !empty($jeniskasuspenyakit_id) && strtolower($jeniskasuspenyakit_id) !== strtolower('Semua')) {
            $where = $where." AND jeniskasuspenyakit_id = ".$jeniskasuspenyakit_id."";
        }
        if (isset($kamarruangan_id) && !empty($kamarruangan_id) && strtolower($kamarruangan_id) !== strtolower('Semua')) {
            $where = $where." AND kamarruangan_id = ".$kamarruangan_id."";
        }
        if (isset($klasifikasikamar_id) && !empty($klasifikasikamar_id) && strtolower($klasifikasikamar_id) !== strtolower('Semua')) {
            $where = $where." AND klasifikasikamar_id = ".$klasifikasikamar_id."";
        }
        if(isset($is_pasien_titipan) && !empty($is_pasien_titipan) && $is_pasien_titipan == true) {
            $status_kamar = 0;
        }

        switch ($status_kamar) {
            case '1':
                $where = $where." AND status_isi = false";
                break;
            case '2':
                $where = $where." AND status_isi = true";
                break;
            default:
                $where = $where;
                break;
        }
    }

    public function actionGetDefaultBedByRuangan()
    {
        $request = Yii::$app->request;
        $all_data = $request->get('all_data', true);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $where      = null;
        $idRuangan  = null;
        $idPenjamin = null;
        $idKelasPelayanan = null;

        $this->defaultBedByRuanganFilter($request, $where, $idRuangan, $idPenjamin, $idKelasPelayanan);

        $tipeTarif = 'kamar';
        $order     = " ORDER BY kamarruangan_id ASC, kamartempattidur_id ASC";

        $data = Yii::$app->db->createCommand('
            SELECT *
            FROM tariftotalkamarrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id,:tipe_tarif)
            '.$where.''.$order.'')
        ->bindParam(':ruangan_id',$idRuangan)
        ->bindParam(':penjamin_id', $idPenjamin)
        ->bindParam(':kelaspelayanan_id', $idKelasPelayanan)
        ->bindParam(':tipe_tarif', $tipeTarif);

        return [
            'data' => $all_data ? $data->queryAll() : $data->queryOne()
        ];
    }

    public function actionGetInfoPasienPendaftaran()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $no_rm = $request->get('no_rm');
        $is_ranap = $request->get('is_ranap', FALSE);
        $listMultiPayer = [];
        $latest_kunjungan_rujuk_ranap = [];
        if (empty($id) && empty($no_rm)) return [];
        $result = $this->getDataPasien();
        if ($id) {
            $result->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $result->andWhere([
                'no_rekam_medik' => $no_rm
            ]);
        }
        $result = $result->asArray()->one();
        
        if ($is_ranap) {
            $latest_kunjungan_rujuk_ranap = InfoKunjunganRsView::find();

            if ($id) {
                $latest_kunjungan_rujuk_ranap->andWhere([
                    'pasien_id' => $id
                ]);
            } else if ($no_rm) {
                $latest_kunjungan_rujuk_ranap->andWhere([
                    'no_rekam_medik' => $no_rm
                ]);
            }

            $latest_kunjungan_rujuk_ranap->andWhere([
                'carakeluar_id' => DocoConstants::CARA_KELUAR_RUJUK_RAWAT_INAP,
            ]);

            $latest_kunjungan_rujuk_ranap = $latest_kunjungan_rujuk_ranap->orderBy([
                'tgl_pendaftaran' => SORT_DESC
            ])->asArray()->one();
        }
        
        $multiPayer = InfoMultipayerView::find();

        if($id) {
            $multiPayer->andWhere([
                'pasien_id' => $id
            ]);
        } else if ($no_rm) {
            $multiPayer->andWhere([
                'no_rekam_medik' => $id
            ]);
        }

        $listMultiPayer = $multiPayer->orderBy([
            'pendaftaran_id' => SORT_DESC
        ])->limit(3)->all();
        return [
            'info_pasien' => $result,
            'kunjugan' => [],
            'latest_kunjungan_rujuk_ranap' => $latest_kunjungan_rujuk_ranap,
            'multipayer' => $listMultiPayer
        ];
    }

    public function actionGetInfoKunjunganPasien()
    {
        $request = Yii::$app->request;

        if (empty($request->get('no_rekam_medik'))) return []; 

        $kunjugan = InfoKunjunganRsView::find();
        $kunjugan->andWhere([
            'no_rekam_medik' => $request->get('no_rekam_medik')
        ]);
        $listKunjungan = $kunjugan->orderBy([
            'tgl_pendaftaran' => SORT_DESC
        ])->limit(3)->all();

        return $listKunjungan;
    }

    public function actionCekEligiblePeserta()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_kartu');
            $penjaminId = $request->get('penjamin_id');
            $penjamin = Penjamin::find()->select(['konfigasuransi_id'])->where(['penjamin_id' => $penjaminId])->one();
            
            $isIntegrasi = false;
            if(isset($penjamin->konfigasuransi_id)) {
                $cekIntegrasi = KonfigAsuransi::find()->select(['provider_id'])->where(['konfigasuransi_id' => $penjamin->konfigasuransi_id, 'is_active' => true])->one();
                $isIntegrasi = empty($cekIntegrasi) ? false : true;
                // return [
                //     'status' => 200,
                //     'code' => 999,
                //     'message' => 'Integrasi Asuransi Belum Tersedia !'
                // ];
            }

            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_kartu' => $noKartu
            ];

            $response = $insuranceClient->CekEligiblePeserta($data);

            return [
                'status' => 200,
                'code' => 0,
                'response' => !empty($response) ? $response : [],
                'message' => "Cek Eligible Success !",
                'isIntegrasi' => $isIntegrasi
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionIdentitasPasienAsuransi()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_kartu');
            $penjaminId = $request->get('penjamin_id');
            $norm = $request->get('norm');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_kartu' => $noKartu
            ];

            $pasienAsuransi = $insuranceClient->CekEligiblePeserta($data);
            $pasien = PasienV::find()->where(['no_rekam_medik' => $norm])
                ->andWhere(['IS NOT', 'no_rekam_medik', NULL])
                ->asArray()->one();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'data' => [
                    'pasienAsuransi' => $pasienAsuransi,
                    'pasien' => $pasien
                ],
                'message' => 'Get data success'
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionReferensiBenefit()
    {
        try {
            $request = Yii::$app->request;
            $noKartu = $request->get('no_kartu');
            $penjaminId = $request->get('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'no_kartu' => $noKartu
            ];
            
            $benefit = $insuranceClient->ReferensiBenefitPeserta($data);

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'data' => $benefit,
                'message' => 'Get data success'
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCekPasienAsuransiNik()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $nik = $request->get('nik');
            $tanggalLahir = $request->get('tanggal_lahir');

            if (empty($nik) || empty($penjaminId) || empty($tanggalLahir)) {
                Yii::$app->response->statusCode = 422;
                return [
                    'message' => Yii::t('app', 'NIK, Penjamin dan Tanggal Lahir tidak boleh kosong.')
                ];
            }

            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'nik' => $nik,
                'birth_date' => date('d/m/Y', strtotime($tanggalLahir))
            ];

            $dataReferensi = $insuranceClient->ReferensiKepesertaan($data);

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => "Cek Pasien Asuransi NIK Success !",
                'data' => $dataReferensi
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCekPenjaminTerintegrasi()
    {
        $request = Yii::$app->request;
        try {
            $penjaminId = $request->get('penjamin_id');
            $dataPenjamin = $this->getPenjaminTerintegrasi($penjaminId);

            if (empty($dataPenjamin)) {
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => 'Data Penjamin Terintegrasi Tidak Ditemukan !',
                    'data' => null
                ];
            }

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => "Data Penjamin Terintegrasi Ditemukan !",
                'penjaminId' => $penjaminId,
                'data' => $dataPenjamin
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    private function getPenjaminTerintegrasi($penjaminId = null)
    {
        $result = Penjamin::find()->select([
            'penjamin_m.penjamin_id',
            'penjamin_m.penjamin_nama',
        ])
        ->join('JOIN', 'konfigasuransi_k kk', 'kk.konfigasuransi_id = penjamin_m.konfigasuransi_id')
        ->where([
            'kk.is_deleted' => false,
            'kk.is_active' => true
        ]);
        
        if ($penjaminId) {
            $result->andWhere(['penjamin_m.penjamin_id' => $penjaminId]);
            return $result->one();
        }
        
        $result = $result->asArray()->all();

        $penjaminId = [];
        foreach ($result as $key => $value) {
            $penjaminId[] = $value['penjamin_id'];
        }

        return $penjaminId;
    }

    public function actionCekKodeBookingNonMjkn()
    {
        $request = Yii::$app->request;
        $kodeBooking = $request->get('kode_booking');
        $result = [];
        if(!$kodeBooking) {
            return [];
        }

        $now = date('Y-m-d');
        $data = InfoPendaftaranOlView::find()->where(['no_pendaftaranol' => $kodeBooking])->one();
        $noRekamMedik = ArrayHelper::getValue($data,'no_rekam_medik');
        $tanggalReservasi = ArrayHelper::getValue($data,'tgl_kunjungan');
        $tanggalReservasiText = date('d M Y', strtotime($tanggalReservasi));
        $tanggalReservasi = date('Y-m-d', strtotime($tanggalReservasi));
        $statusDaftarOL = ArrayHelper::getValue($data,'status_daftar_ol');
        $statusDaftar = ArrayHelper::getValue($data,'status_daftar');
        $result['data'] = $data;
        $result['list_penjamin'] = $this->actionGetPenjaminTerintegrasi();
        $result['default_penjamin_mqare'] = (new DocoConstansId)->actionGetId('default_penjamin_mqare');

        if(empty($noRekamMedik)) {
            $result = [
                'status' => 500,
                'message' => "Proses Pendaftaran Mandiri Tidak dapat Dilanjutkan Karena Pasien Belum Memiliki No. RM.
                 Silahkan Lakukan Proses Pendaftaran Reservasi Via Staf Pendaftaran."
            ];
        }

        if($now !== $tanggalReservasi) {
            $result = [
                'status' => 500,
                'message' => "Proses Pendaftaran Mandiri Tidak dapat Dilanjutkan Karena Tanggal Reservasi Poli Tidak Sesuai.
                 Reservasi dilakukan untuk tanggal ".$tanggalReservasiText.""
            ];
        }

        if(in_array($statusDaftarOL, [DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI, DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK])) {
            $result = [
                'status' => 500,
                'message' => "Proses Pendaftaran Mandiri Tidak dapat Dilanjutkan Karena Reservasi Telah ".$statusDaftar.""
            ];
        }

        return $result;
    }

    public function actionGetPenjaminTerintegrasi()
    {
        try {
            $result = Yii::$app->db->createCommand("
                SELECT
                    penjamin_m.penjamin_id,
                    penjamin_m.penjamin_nama,
                    kk.konfigasuransi_id,
                    kk.provider_id
                FROM penjamin_m
                JOIN konfigasuransi_k kk ON kk.konfigasuransi_id = penjamin_m.konfigasuransi_id
                WHERE penjamin_m.konfigasuransi_id IS NOT NULL
                AND kk.is_deleted IS FALSE
                AND kk.is_active IS TRUE
            ")->queryAll();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Get Penjamin Success !',
                'data' => $result
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => 'Connection Failed & Please Check Your Parameters !'
            ];
        }
    }
}
