<?php

/**
 * @author Randy Vianda Putra
 * @todo Allow all Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\ConfigTrait;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PerdaTarif;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\OperasiView;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\JenisAnastesi;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoActiveController;
use app\modules\v1\models\DaftartindakanV;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\InfoRencanaOperasi;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\InfoMonitoringBpjsView;
use app\modules\v1\models\LookupTransaksi;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = 'app\modules\v1\models\CaraBayar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"]  = ["POST", "GET"];
        $verbs["ajax"]   = ["POST", "GET"];
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

    public function getRuangan($instalasi_id = null)
    {   
        $return = [];
        try {
            $model = Ruangan::find()->where(['is_deleted'=>false, 'is_active'=>true]);
            if($instalasi_id){
                $model->andWhere(['instalasi_id'=>$instalasi_id]);
            }
            $return = $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            $return = [];
        }
        
        return $return;
    }

    public function getInstalasi($id = null)
    {
        $return = [];
        try {
            $model = Instalasi::find()->where(['is_deleted'=>false, 'is_active'=>true]);
            $return = $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            $return = [];
        }
        return $return;
    }

    public function actionGetRuanganInstalasi()
    {
        $ruangan = $this->getRuangan();
        $instalasi = $this->getInstalasi();

        return [
            'ruangan'=>$ruangan,
            'instalasi'=>$instalasi
        ];
    }

    public function actionGetRuangan($id)
    {
        $ruangan = $this->getRuangan($id);
        return ['ruangan'=>$ruangan];
    }

    public function actionGetLookupByType($type = null, $name = null)
    {
        $model = Lookup::find();
        if($type){
            $model->andWhere(['lookup_type' => strtolower($type)]);
        }
        if($name){
            $model->andWhere(['lookup_name' => strtolower($name)]);
        }
        $model->orderBy(['lookup_urutan'=>SORT_ASC]);
        return $model->asArray()->all();
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $find = PegawaiView::find();
        if(isset($post['term'])){
            $find->andWhere(['ILIKE', 'nama_pegawai', $post['term']]);
        }
        if(isset($post['ruangan_id'])){
            $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
        }
        return $find->asArray()->all();
    }

    public function actionGetNoRequest()
    {
        $request = Yii::$app->request;
        $model = InfoRencanaOperasi::find();
        
        if ($no_req = $request->get('q')) {
            $model->andWhere(['ILIKE','no_orderkeunitlain',$no_req]);
        }

        $filter_date = date('Y-m-d');
        if ($tanggal_operasi = $request->get('tanggal')) {
            $tanggal_operasi = date('Y-m-d',strtotime($tanggal_operasi));
            $filter_date = $tanggal_operasi;
        }
        
        $model->andWhere([
            'DATE(tgl_permintaan)' => $filter_date
        ]);

        return [
            'data' => $model->asArray()->limit(10)->all()
        ];
    }

    public function actionGetDokterOperator($q = null)
    {
        $request = Yii::$app->request;
        $jwt = Yii::$app->jwt->instalasi_id;
        $model = DokterView::find();
        
        if ($q) {
            $model->andWhere(['ILIKE','nama_pegawai',$q]);
        }

        if ($jwt) {
            $model->andWhere(['instalasi_id' => $jwt]);
        }

        return [
            'data' => $model->asArray()->all()
        ];
    }
    
    public function actionGetTimOperasi()
    {
        try {
            $data = $this->getLookupByType('tim_operasi');
            return $data->asArray()->all();
        } catch (\Exception $e) {
            return [];
        }
    }
    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $find = TarifPenunjangView::find();
        if(isset($post['term'])){
            $find->andWhere(['ILIKE', 'daftartindakan_nama', $post['term']]);
        }
        if(isset($post['ruangan_id'])){
            $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
        }
        if(isset($post['penjamin_id'])){
            $find->andWhere(['penjamin_id' => $post['penjamin_id']]);
        }
        if(isset($post['kelaspelayanan_id'])){
            $find->andWhere(['kelaspelayanan_id' => $post['kelaspelayanan_id']]);
        }
        $find->andWhere(['komponentarif_id'=>6]);
        $find->select(['daftartindakan_id', 'daftartindakan_nama', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id','harga_tariftindakan', 'persencyto_tindakan']);
        $find->groupBy(['daftartindakan_id', 'daftartindakan_nama', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id','harga_tariftindakan', 'persencyto_tindakan']);
        return $find->asArray()->all();
    }
    public function actionListJenisOperasi()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = OperasiView::find();
        $model->select(['golonganoperasi_id', 'golonganoperasi_nama', 'daftartindakan_id','operasi_id']);
        if(isset($get['daftartindakan_id'])){
            $model->andWhere(['daftartindakan_id' => $get['daftartindakan_id']]);
        }
        return $model->asArray()->all();
    }
    public function getJenisAnastesi()
    {
        try {
            $model = JenisAnastesi::find()->where(['is_deleted'=>'false', 'is_active'=>'true']);
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetJenisAlat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = InfoObatAlkesView::find();
            $model->andWhere(['ILIKE', 'jenisobatalkes_nama', 'alkes']);
            if(isset($post['obatalkes_nama'])){
                $model->andWhere(['ILIKE', 'obatalkes_nama', $post['obatalkes_nama']]);
            }
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetDaftarTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try{
            $model = DaftarTindakanV::find();
            if(isset($post['daftartindakan_nama'])){
                $model->andWhere(['ILIKE','daftartindakan_nama', $post['daftartindakan_nama']]);
            }
            $model->limit(50);
            return $model->asArray()->all();
        } catch(\yii\db\Exception $e){
            return [];
        }
    }
    public function actionGetTindakanTarif()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try{
            $find = InfoTarifRsView::find();
            if(isset($post['term'])){
                $find->andWhere(['ILIKE', 'daftartindakan_nama', $post['term']]);
            }
            if(isset($post['ruangan_id'])){
                $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
            }
            if(isset($post['penjamin_id'])){
                $find->andWhere(['penjamin_id' => $post['penjamin_id']]);
            }
            if(isset($post['kelaspelayanan_id'])){
                $find->andWhere(['kelaspelayanan_id' => $post['kelaspelayanan_id']]);
            }
            $find->andWhere(['komponentarif_id'=>6]);
            $find->limit(50);
            return $find->asArray()->all();
        } catch(\yii\db\Exception $e){
            return [];
        }
    }
    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = Pegawai::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        $model->select(['pegawai_id', 'nama_pegawai']);
        if(isset($post['nama_pegawai'])){
            $model->andWhere(['ILIKE', 'nama_pegawai', $post['nama_pegawai']]);
        }
        $model->groupBy(['pegawai_id', 'nama_pegawai']);
        return $model->asArray()->all();
    }

    public function actionGetNewDokter()
    {
        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];
            $page = Yii::$app->request->get('page', 0);
            $limit = Yii::$app->request->get('limit', 5);
            $offset = Yii::$app->request->get('offset', 0);

            $model = Pegawai::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
            $model->select(['pegawai_id', 'nama_pegawai']);

            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'nama_pegawai', $q]);
            }
            $model->groupBy(['pegawai_id', 'nama_pegawai']);

            return $model->offset($offset)->limit($limit)->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetKonfigDokterMultiple(){
        try {
            $model = LookupTransaksi::find()
                ->select(['additional_value'])
                ->where(['kode_transaksi' => DocoConstants::EKLAIM_DOKTER_MULTIPLE])
                ->asArray()->one();
            $result = ArrayHelper::getValue($model, 'additional_value');
            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionPegawaiMedis()
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

    public function getMultiLookup($listRequest)
    {
        $data = [];
        foreach ($listRequest as $key => $value) {
            $data[$value] = $this->getLookupByType($value)->asArray()->all();
        }
        return $data;
    }
    
    public function actionGetPenjamin($id = null)
    {
        $model = Penjamin::find(true);
        $model->select(['penjamin_id', 'penjamin_nama', 'carabayar_id']);

        if ($id) {
            $model->where(['carabayar_id' => $id]);
        }

        return $model->asArray()->all();
    }

    public function actionGetCaraBayar()
    {
        $model = CaraBayar::find(true);
        $model->select(['carabayar_id', 'carabayar_nama']);
        $model->andWhere([
            'is_active' => true
        ]);
        return $model->asArray()->all();
    }
    
    public function actionGetKamar($ruangan_id = null)
    {
        $model = KamarRuangan::find(true);
        if($ruangan_id){
            $model->andWhere(['ruangan_id'=>$ruangan_id]);
        }
        return $model->asArray()->all();
    }

    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi_id = $get['instalasi_id'];
        $result = Ruangan::find()->where(['is_active' => true, 'instalasi_id' => $instalasi_id]);
        return $result->asArray()->all();
    }

    public function actionGetDiagnosa()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $find = DiagnosaView::find();
        $find->select(['diagnosa_id', 'diagnosa_nama', 'diagnosa_kode', 'tabularlist_versi']);
        if(isset($post['diagnosa_nama'])){
            $find->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($post['diagnosa_nama'])],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($post['diagnosa_nama'])]
            ])->limit(30)->all();
            // $find->andWhere(['ILIKE', 'diagnosa_kode', $post['diagnosa_nama']]);
        }
        if(isset($post['tabularlist_versi'])){
            $find->andWhere(['ILIKE', 'tabularlist_versi', $post['tabularlist_versi']]);
        }
        $find->groupBy(['diagnosa_id', 'diagnosa_nama', 'diagnosa_kode', 'tabularlist_versi']);
        $find->orderBy(['diagnosa_kode'=>SORT_ASC]);
        $find->limit(50);
        return $find->asArray()->all();
    }
    
    public function actionGetPerdaTarif($isBpjs = false)
    {
        $model = PerdaTarif::find();
        if($isBpjs){
            $model->andWhere(['ILIKE','perda_tentang', 'INA-CBG']);
        }
        return $model->asArray()->all();
    }

    public function actionGetCaraKeluar($isBpjs = false)
    {
        $model = CaraKeluar::find();
        if($isBpjs){
            $model->andWhere(['ILIKE', 'catatan', 'INA-CBG']);
        }
        return $model->asArray()->all();
    }

    public function actionGetListCarabayar($penjamin_id = null)
    {
        $data = Penjamin::find()->with(
            [
                'caraBayar' => function($model) {
                    $model->select(['carabayar_nama', 'carabayar_id']);
                }
            ]
        );

        if ($penjamin_id) {
            $data->where(
                [
                    'penjamin_m.is_active'   => true,
                    'penjamin_m.penjamin_id' => $penjamin_id
                ]
            );
        }
        
        $data->orderBy('penjamin_nama');
        $header   = $data->one();

        return $header;

        $result[] = $header->caraBayar;

        return $result;
    }

    public function actionGetKamarNew($ruangan_id = null)
    {   
        $return = [];
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        try {
            $model = KamarRuangan::find()->where(['is_deleted'=>false, 'is_active'=>true]);
            if($ruangan_id){
                $model->andWhere(['ruangan_id'=>$ruangan_id]);
            }
            $return = $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            $return = [];
        }
        
        return $return;
    }

    public function actionGetDataMonitoring($pendaftaran_id, $pasienadmisi_id)
    {
        $model = InfoMonitoringBpjsView::find();
        if($pendaftaran_id && $pasienadmisi_id){
            $model->andWhere([
                'pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => $pasienadmisi_id]);
        }
        
        return $model->asArray()->one();
    }

    public function actionGetDataDiagnosa()
    {
        $request = Yii::$app->request;
        $type = $request->get('type');
        $q = $request->get('q');

        if($type == 10){
            $model = DiagnosaView::find()->where(['tabularlist_versi' => 'ICD X']);
        }
        else {
            $model = DiagnosaView::find()->where(['tabularlist_versi' => 'ICD IX']);
        }
        
        $model->andFilterWhere(['or', 
                ['LIKE', 'diagnosa_kode', strtoupper($q)],
                ['LIKE', 'UPPER(diagnosa_nama)', strtoupper($q)],
            ]);

        return $model->asArray()->all();
    }

    public function actionGetListDokter()
    {
        $dokter = [];
        $model = new Pegawai;
        $query = $model::find()->select(['pegawai_id', 'nama_pegawai'])->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER])->all();

        if ($query) {
            foreach ($query as $key => $value) {
                if (!isset($dokter[$value->pegawai_id])) {
                    $dokter[] = [
                        'id' => $value->pegawai_id,
                        'text' => $value->pegawai_id.$value->nama_pegawai
                    ];
                }
            }
        }
        return $dokter;
    }

    public function actionGetInstalasiEklaim()
    {
        try {
            $instalasiData = Lookup::find()
            ->where(['lookup_type' => 'instalasi_eklaim'])
            ->asArray()
            ->all();

            return [
                'message' => 'Get data successfully',
                'data' => $instalasiData
            ];

        } catch (\Throwable $th) {
            
            return [
                'message' => $th->getMessage(),
                'data' => []
            ];
        }
    }
}
