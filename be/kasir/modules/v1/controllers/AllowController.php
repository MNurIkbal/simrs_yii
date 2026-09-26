<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\Traits\Select2Trait;
use Doco\components\DocoMessages;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Shift;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoListPenjamin;
use app\modules\v1\models\TarifTotalRs;
use app\modules\v1\payload\TarifPayload;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\TagihanPasienPulangView;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\InfoTagihanPasien;
use Doco\components\DocoConstansId;
use app\modules\v1\models\Bank;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;
    use Select2Trait;

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


    /**
    * @author Rizal
    * @since 2018-01-24 14:01:16 
    * @param int ruangan_id default null
    * @return array list of ruangan
    * @desc needs for depdrop or dropdown
    */
    public function actionListInstalasi($ruangan_id=null) {
        $data = Instalasi::find()->joinWith('ruangan');
        $data->where(['instalasi_m.is_active' => true]);
        if ($ruangan_id) {
            $data->andWhere(['ruangan_m.ruangan_id' => $ruangan_id]);
        }
        $data->orderBy('instalasi_m.instalasi_id');

        $results = [
            'data' => $data->asArray()->all(),
            'count' => $data->count()
        ];
        return $results;
    }

    /**
    * @author Rizal
    * @since 2018-01-24 14:01:16 
    * @param int instalasi_id
    * @return array list of ruangan
    * @desc needs for depdrop or dropdown
    */
    public function actionListRuangan($instalasi_id = null, $singkatan = null) {
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
            'data' => $data->asArray()->all(),
            'count' => $data->count()
        ];
        return $results;
    }

    /**
    * @author Rizal
    * @since 2018-03-27 13:18:42 
    * @param int instalasi_id
    * @return array list of pegawai ruangan
    * @desc needs for depdrop or dropdown
    */
    public function actionListPegawaiRuangan($ruangan_id=null,$pegawai_id=null) {
        $data = PegawaiView::find();
        // $data = RuanganPegawai::find();
        // $data->select('ruanganpegawai_mp.pegawai_id, ruanganpegawai_mp.ruangan_id, ruangan_m.ruangan_nama, pegawai_m.nama_pegawai');
        // $data->joinWith(['ruangan'=>function ($a) {
        //     $a->select(['ruangan_m.ruangan_id']);
        // }]);
        // $data->joinWith(['pegawai'=>function ($a) {
        //     $a->select(['pegawai_m.pegawai_id']);
        // }]);
        // $data->where(['ruanganpegawai_mp.is_active' => 't']);
        // if ($ruangan_id) {
        //     $data->andWhere(['ruanganpegawai_mp.ruangan_id' => $ruangan_id]);
        // }
        // if ($pegawai_id) {
        //     $data->andWhere(['ruanganpegawai_mp.pegawai_id' => $pegawai_id]);
        // }
        // $data->orderBy('ruangan_m.ruangan_id');
        if ($ruangan_id) {
            $data->andWhere(['ruangan_id' => $ruangan_id]);
        }
        if ($pegawai_id) {
            $data->andWhere(['pegawai_id' => $pegawai_id]);
        }
        $results = [
            'data' => $data->asArray()->all(),
            'count' => $data->count()
        ];
        return $results;
    }


    public function actionListNilaiUang()
    {
        $lookup = new Lookup;
        $q = $lookup->find()
        ->where(['lookup_type'=>DocoConstants::LT_N_UANG])
        ->orderBy('lookup_urutan ASC');
        $results = $this->getOrSetCache(DocoConstants::VC_N_UANG, $q, true);
        return $results;
    }

    public function actionGetAllList()
    {
        return [
            'list-cara-bayar' => ArrayHelper::map($this->getCaraBayar()->asArray()->all(),'carabayar_nama','carabayar_nama'),
            'list-instalasi' => ArrayHelper::map($this->getInstalasi()->asArray()->all(),'instalasi_nama','instalasi_nama'),
            'list-status-bayar' => ArrayHelper::map($this->getLookupByType('status_bayar')->select(['statusbayar_id'=>'lookup_name','statusbayar_nama'=>'lookup_name'])->asArray()->all(),'statusbayar_id','statusbayar_nama'),
        ];
    }

    /**
     * @see Fungsi get data carabayar_m
     * @return array, activeQueryRecords
     *
     */
    private function getCaraBayar()
    {
        $result = Carabayar::find();
        $result->andWhere(['is_active' => TRUE]);
        
        return $result;
    }

    /**
    * @param 
    * @return array list of instalasi
    * @desc 
    */
    private function getInstalasi()
    {
        try {
            $model = new Instalasi;
            $model = $model->find()
                ->andWhere(['is_pelayanan'=>true,'is_active'=>true]);

            return $model;
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
    * @author Rizal F. <rizal@docotel.com>
    * @since 
    * @param 
    * @return 
    */
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
            
            $statusPeriksa = $this->getLookupByType('status_periksa')->select([
                    'statusperiksa_id' => 'lookup_name',
                    'statusperiksa_nama' => 'lookup_name'
                ])
                ->asArray()
                ->all();

            $master['status_periksa'] = $statusPeriksa;

            $result = [
                'master' => $master
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
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => TRUE]);
        }

        return $result;
    }

    public function actionGetPasienByName($q)
    {
        $model = Pasien::find();
        $model->andFilterWhere(['ILIKE', 'nama_pasien', $q]);
        return [
            'data' => $model->asArray()->all()
        ];
    }

    public function actionGetDataResep($id = null)
    {
        $term = Yii::$app->request->get('q');
        $resep = PenjualanResep::find();
        $resep->joinWith([
            'pendaftaran'
        ]);
        if ($id) {
            $resep->where('penjualanresep_t.reseptur_id = :id', ['id' => $id]);
        }
        $resep->andWhere('pendaftaran_t.status_bayar = :status_bayar', ['status_bayar' => DocoConstants::BELUM_LUNAS]);

        if (!empty($term)) {
          $resep->andFilterWhere(['ILIKE', 'penjualanresep_t.noresep', $term]);
            // $resep->andFilterWhere(['penjualanresep_t.noresep' => $term]);
        }

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tanggal'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal']); // Unset Advanced Filter  date range
            }
        }
        
        $resep->andWhere(['between', 'penjualanresep_t.tglpenjualan', $start, $end]); 

        return $resep->limit(10)->asArray()->all();
    }

    private function getListMaster(array $listRequest)
    {
        foreach ($listRequest as $key=>$className) {
            $class = "app\modules\\v1\models\\" . $className;
            $model = new $class;
            $q = $model->find();
            $q->andWhere(['is_active' => true, 'is_deleted' => false]);
            $results[$key] = $this->getOrSetCache($class, $q, true, $key);
        }
        return $results;
    }

    public function actionGetKonfigSystem()
    {
        $result = ['is_pembulatankeatas'=>'true', 'satuanpembulatan'=>0];
        try {
            $getKonfig = KonfigSystem::find();
            $result = $this->getOrSetCache(DocoConstants::VAR_K_S, $getKonfig, false);
        } catch (Exception $e) {
            return $result;
        }
        return $result;
    }
    public function actionGetRuangan($listInstalasi = ''){
        $request = Yii::$app->request;
        $post = $request->post();
        $get = $request->get();
        try {
            $model = new Ruangan;
            $query = $model->find()->select([
                'ruangan_id',
                'instalasi_id',
                'ruangan_nama',
            ]);

            if(!empty($listInstalasi)){
                $listInstalasi = explode(',', $listInstalasi);
                $query->where(['IN','instalasi_id',$listInstalasi]);
            }
            return $query->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetInstalasi($listInstalasi = ''){
        $request = Yii::$app->request;
        $post = $request->post();
        $get = $request->get();
        try {
            $model = new Instalasi;
            $query = $model->find();

            if(!empty($listInstalasi)){
                $listInstalasi = explode(',', $listInstalasi);
                $query->where(['IN','instalasi_id',$listInstalasi]);
            }
            return $query->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetCaraBayar()
    {
        $request = Yii::$app->request;
        try {
            return $this->getCaraBayar()->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        try {
            return Cache::getPenjamin();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = DokterView::find();
        $model->select(['pegawai_id', 'nama_pegawai']);
        if(isset($post['nama_pegawai'])){
            $model->andWhere(['ILIKE', 'nama_pegawai', $post['nama_pegawai']]);
        }
        $model->groupBy(['pegawai_id', 'nama_pegawai']);
        return $model->asArray()->all();
    }
    public function actionGetShift()
    {
        $request = Yii::$app->request;
        $model = Shift::find();
        return $model->asArray()->all();
    }

    public function actionGetDataPegawai()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = Pegawai::find();
        $model->select(['pegawai_id', 'nama_pegawai', 'nomorindukpegawai']);
        if(isset($get['q'])){
            $model->andWhere(['ILIKE', 'nama_pegawai', $get['q']]);
        }
        
        return $model->asArray()->all();
    }

    public function actionGetDataPendaftaran()
    {
        $params = Yii::$app->request;
        $term = strtoupper($params->get('term'));
        $page = $params->get('page',0);
        $limit = $params->get('limit',10);
        $offset = $params->get('offset',($page-1)*10);
        $konfig = Cache::getKonfigSistem();
        $is_pembulatankeatas = $konfig['is_pembulatankeatas'];
        $satuanpembulatan = $konfig['satuanpembulatan'];
        
        $sql = "SELECT
                    tagihanpasienpulang_v.no_pendaftaran,
                    tagihanpasienpulang_v.no_rekam_medik,
                    tagihanpasienpulang_v.pendaftaran_id,
                    tagihanpasienpulang_v.nama_pasien,
                    tagihanpasienpulang_v.total_tagihan,
                    tagihanpasienpulang_v.piutang_sudahbayar,
                    tagihanpasienpulang_v.tanggal_lahir,
                    tagihanpasienpulang_v.umur,
                    tagihanpasienpulang_v.tgl_pendaftaran,
                    tagihanpasienpulang_v.total_piutang,
                    tagihanpasienpulang_v.adm_persen,
                    tagihanpasienpulang_v.tarif_max,
                    Sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_ranap,
                    tagihanpasienpulang_v.uang_muka,
                    {$is_pembulatankeatas} as is_pembulatankeatas,
                    {$satuanpembulatan} as satuanpembulatan

                FROM
                tagihanpasienpulang_v
                JOIN tindakanpelayanan_t ON tagihanpasienpulang_v.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                
                WHERE UPPER(no_pendaftaran) LIKE '%{$term}%'
                OR UPPER(nama_pasien) LIKE '%{$term}%'
                OR UPPER(no_rekam_medik) LIKE '%{$term}%'
                GROUP BY
                tagihanpasienpulang_v.no_pendaftaran,
                tagihanpasienpulang_v.no_rekam_medik,
                tagihanpasienpulang_v.pendaftaran_id,
                tagihanpasienpulang_v.nama_pasien,
                tagihanpasienpulang_v.total_tagihan,
                tagihanpasienpulang_v.piutang_sudahbayar,
                tagihanpasienpulang_v.tanggal_lahir,
                tagihanpasienpulang_v.umur,
                tagihanpasienpulang_v.tgl_pendaftaran,
                tagihanpasienpulang_v.total_piutang,
                tagihanpasienpulang_v.adm_persen,
                tagihanpasienpulang_v.tarif_max,
                tagihanpasienpulang_v.uang_muka
                ";
        
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        // $data = $sql->offset($offset)->limit($limit)->asArray()->all();
        
        return $data;
    }

    public function actionGetStatusLunas()
    {
        $lookup = new Lookup;
        $q = $lookup->find()
        ->where(['lookup_type'=>DocoConstants::VAR_LU_SB])
        ->orderBy('lookup_urutan ASC');

        $results = $this->getOrSetCache(DocoConstants::VAR_LU_SB, $q, true);

        return ArrayHelper::map($results, 'lookup_id', 'lookup_name');
    }

    public function actionGetListPenjamin()
    {
        $request = Yii::$app->request;
        try{
            $model = new InfoListPenjamin;
            $query = $model::find();
            if( $request->get('pendaftaran_id') ){
                $query->where(['pendaftaran_id' => $request->get('pendaftaran_id')]);
            }
            return $query->asArray()->all();
        } catch(\yii\db\Exception $e){
            return [];
        }
    }

    public function actionGetTarifTindakan()
    {
        $request = Yii::$app->request;
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
        $namaPemeriksaan = $request->get('daftartindakan_nama');
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

        if(!empty($namaJenis)){
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        if(!empty($namaPemeriksaan)){
            $query->andWhere(['ILIKE', 'LOWER(daftartindakan_nama)', strtolower($namaPemeriksaan)]);
        }

        $result = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $result;
    }

    public function actionGetTarifPaket()
    {
        $request = Yii::$app->request;
        $namaPaket = $request->get('tipepaket_nama');
        $namaJenis = $request->get('jenispemeriksaanlab_nama');
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

        if (!empty($namaPaket)) {
            $query->andWhere(['ILIKE', 'LOWER(tipepaket_nama)', strtolower($namaPaket)]);
        }

        if(!empty($namaJenis)){
            $query->andWhere(['ILIKE', 'LOWER(jenispemeriksaanlab_nama)', strtolower($namaJenis)]);
        }

        $result = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $result;
    }

    public function actionGetDataBank()
    {
        $request = Yii::$app->request;
        try {
            $model = new Bank;
            $query = $model->find()
                ->where(['is_active' => true])
                ->orderBy(['nama_bank' => SORT_ASC]);
            return $query->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionGetKelas()
    {
        return Cache::getKelasPelayanan(); 
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = $resultData = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $type = $request->get('type', []);
        $additionalPayload = $request->get('additionalPayload', []);
        $carabayar_id = $instalasi_id = null;
        if(isset($additionalPayload['carabayar_id']) && !empty($additionalPayload['carabayar_id'])) {
            $carabayar_id = $additionalPayload['carabayar_id'];
        }
        if(isset($additionalPayload['instalasi_id']) && !empty($additionalPayload['instalasi_id'])) {
            $instalasi_id = $additionalPayload['instalasi_id'];
        }
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        switch ($type) {
            case 'instalasi':
                $listInstalasi = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
                $result = Instalasi::find()
                    ->select(['instalasi_id as id', 'instalasi_nama as text'])
                    ->where(['is_pelayanan' => true, 'is_active' => true])
                    ->andWhere(['IN','instalasi_id', $listInstalasi]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(instalasi_nama)', strtolower($term)]);
                }
                $result->orderBy(['instalasi_nama' => SORT_ASC]);
                break;
            
            case 'ruangan': 
                $result = Ruangan::find()
                    ->select(['ruangan_id as id', 'ruangan_nama as text'])
                    ->where(['instalasi_id' => $instalasi_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['ruangan_nama' => SORT_ASC]);
                break;

            case 'carabayar':
                $result = CaraBayar::find()
                    ->select(['carabayar_id as id', 'carabayar_nama as text'])
                    ->where(['is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
                }
                $result->orderBy(['carabayar_nama' => SORT_ASC]);
                break;
            
            case 'penjamin': 
                $result = Penjamin::find()
                    ->select(['penjamin_id as id', 'penjamin_nama as text'])
                    ->where(['is_active' => true]);

                    if(!empty($carabayar_id)) {
                        $result->where(['carabayar_id' => $carabayar_id]);
                    }

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['penjamin_nama' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }
}
