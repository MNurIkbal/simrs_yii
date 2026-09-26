<?php

/**
 * @author Rizal
 * @todo 
 * @copyright 22 Nov 2018 

 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\ConfigTrait;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PenjaminView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\MasterKamarRuanganView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\AsesmenAwalGizi;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\KlasifikasiTekananDarah;
use Doco\components\DocoConstansId;
class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = '';

    public function actionTest()
    {
       return 'test';
    }

    public function actionGetBundleData()
    {
        $request = Yii::$app->request;
        try {
            Yii::$app->cache->delete('var_cache_master');
            Yii::$app->cache->delete('var_cache_lookup');
            Yii::$app->cache->delete('var_cache_lookup_keperawatan');
            // get all master by request
            $listRequestMaster = [
                'dokter' => 'DokterView',
                'carabayar' => 'CaraBayar',
                'penjamin' => 'Penjamin',
                'kamar' => 'KamarRuangan',
                'kelaspelayanan' => 'KelasPelayanan',
                'jeniskasuspenyakit' => 'JenisKasusPenyakit',
            ];
            Yii::$app->cache->delete('var_cache_master');
            if (Yii::$app->request->get('flag', null) == 'index-permintaan') {
                $listRequestMaster['ruangan'] = ['Ruangan', ['in', 'instalasi_id', [DocoConstants::INST_ID_RI, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RJ]]];
            } else {
                $listRequestMaster['ruangan'] = ['Ruangan', ['instalasi_id' => DocoConstants::INST_ID_RI]];
            }
            $master = $this->getListMaster($listRequestMaster);

            $listRequestLookup = [
                'status_ranap'
            ];
            $lookup = $this->listLookup($listRequestLookup);
            if(!empty($lookup)){
                foreach ($lookup['status_ranap'] as $key=>$value) {
                    if ($value['lookup_id'] == 453) {
                        unset($lookup['status_ranap'][$key]);
                    }
                }
            }

            $listRequestLookupKeperawatan = [
                'status_asmen_gizi'
            ];
            $lookupPerawat = $this->listLookupKeperawatan($listRequestLookupKeperawatan);

            $konfig_print_gizi = (new DocoConstansId)->actionGetId('konfig_label_gizi');

            $result = [
                'master' => $master,
                'lookup' => $lookup,
                'lookupKeperawatan' => $lookupPerawat,
                'konfig_print_gizi' => $konfig_print_gizi
            ];
            return $result;
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetBundleSga()
    {
        $request = Yii::$app->request;
        try {
            $listRequestLookupKeperawatan = [
                ['asmen_dari', 'lookup_urutan'],
                ['gizi_perubahan', 'lookup_urutan'],
                ['gizi_kategori', 'lookup_urutan'],
                ['gizi_asupanmkn', 'lookup_urutan'],
                ['gastrointestinal_mual', 'lookup_urutan'],
                ['gastrointestinal_muntah', 'lookup_urutan'],
                ['gastrointestinal_diare', 'lookup_urutan'],
                ['gastrointestinal_anoreksia', 'lookup_urutan'],
                ['gizi_fungsional', 'lookup_urutan'],
                ['gizi_metabolik', 'lookup_urutan'],
                ['gizi_sga', 'lookup_urutan'],
            ];
            $lookupPerawat = $this->listLookupKeperawatan($listRequestLookupKeperawatan);
            
            $model = AsesmenAwalGizi::find()
                ->andWhere(['pendaftaran_id'=>$request->get('id')])
                ->orderBy(['asesmenawalgizi_id'=>SORT_DESC])
                ->asArray()->one();

            $result = [
                'lookupKeperawatan' => $lookupPerawat,
                'data_model' => $model
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

    public function actionGetBundleAsuhan()
    {
        $request = Yii::$app->request;
        try {
            $listRequestLookup = [
                'denyut_jantung'
            ];
            $lookup = $this->listLookup($listRequestLookup);

            $listRequestMaster = [
                'jenisdiet'=>'JenisDiet',
                'bmi'=> ['BodyMassIndex', null, 'bmi_minimum'],
                'klasifikasitekanandarah'=> ['KlasifikasiTekananDarah', null, 'klasifikasitekanadarah_id'],
            ];
            $master = $this->getListMaster($listRequestMaster);

            $model = AsesmenAwalGizi::find()
                ->andWhere(['pendaftaran_id'=>$request->get('id')])
                ->orderBy(['asesmenawalgizi_id'=>SORT_DESC])
                ->asArray()->one();

            $result = [
                'lookup' => $lookup,
                'master' => $master,
                'data_model' => $model
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
    private function listLookup($types){
        $results = [];
        try{
            foreach ($types as $key=>$type) {
                $lookup = new Lookup;
                $q = $lookup->find()->where(['lookup_type'=>$type, 'is_active' => true, 'is_deleted' => false]);
                $results[$type] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP, $q, true, $type);
            }
        } catch(\Exception $e) {
            $this->logError($e);
        }
        return $results;
    }
    
    private function listLookupKeperawatan($listCondition){
        $results = [];
        try{
            foreach ($listCondition as $key=>$value) {
                $lookup = new LookupKeperawatan;
                if (is_array($value)) {
                    $q = $lookup->find()
                        ->where(['lookup_type'=>$value[0], 'is_active' => true, 'is_deleted' => false])
                        ->orderBy([$value[1] => SORT_ASC]);
                    $results[$value[0]] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_PERAWAT, $q, true, $value[0]);
                } else {
                    $q = $lookup->find()->where(['lookup_type'=>$value, 'is_active' => true, 'is_deleted' => false]);
                    $results[$value] = $this->getOrSetCache(DocoConstants::VAR_CACHE_LOOKUP_PERAWAT, $q, true, $value);
                }
            }
        } catch(\Exception $e) {
            $this->logError($e);
        }
        return $results;
    }

    private function getListMaster(array $listRequest)
    {
        try{
            $results = [];
            foreach ($listRequest as $key=>$request) {
                $class = "app\modules\\v1\models\\" . (is_array($request) ? $request[0] : $request);
                $model = new $class;
                $q = $model->find()->andWhere([
                    'is_active'=>true,
                    'is_deleted'=>false,
                ]);
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
        } catch(\Exception $e) {
            $this->logError($e);
            return [];
        }
    }


    public function actionGetCaraBayar()
    {
        $request = Yii::$app->request;
        $penjamin_id = $request->get('penjamin_id', null);
        $model = new PenjaminView;
        $query = $model::find()
            ->select('carabayar_id, carabayar_nama');
        if ($penjamin_id) {
            $query->andWhere(['penjamin_id'=>$penjamin_id]);
        }
        $query->groupBy('carabayar_id, carabayar_nama');
        return $query->asArray()->all();
    }

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $carabayar_id = $request->get('carabayar_id', null);
        
        $model = new Penjamin;
        $query = $model::find();
        if ($carabayar_id) {
            $query->andWhere(['carabayar_id'=>$carabayar_id]);
        }
        return $query->asArray()->all();
    }


    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->get('kamarruangan_id', null);
        $model = new MasterKamarRuanganView;
        $query = $model::find()
            ->select('ruangan_id, ruangan_nama');
        if ($kamarruangan_id) {
            $query->andWhere(['kamarruangan_id'=>$kamarruangan_id]);
        }
        $query->groupBy('ruangan_id, ruangan_nama');
        return $query->asArray()->all();
    }

    public function actionGetKamarRuangan()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        
        $model = new MasterKamarRuanganView;
        $query = $model::find();
        if ($ruangan_id) {
            $query->andWhere(['ruangan_id'=>$ruangan_id]);
        }
        return $query->asArray()->all();
    }

    public function actionGetPasienGizi()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id');
            
            $model = InfoPasienGiziView::find()
                ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
                ->asArray()->one();

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
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author rizal faidin <rizal@docotel.com>
     */
    public function actionGetDiagnosa()
    {
        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];
            $type = $get['type'];
            $tabularlist_versi = '';

            if ($type == 9) {
                $tabularlist_versi = DocoConstants::ICD_9;
            } else if($type == 10) {
                $tabularlist_versi = DocoConstants::ICD_10;
            }

            $model = DiagnosaView::find()->andWhere(['is_deleted' => false, 'is_active' => true]);

            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'diagnosa_nama', $q]);
                $model->orWhere(['ilike', 'diagnosa_kode', $q]);
            }

            if ($tabularlist_versi != '') {
                $model->andWhere(['tabularlist_versi' => $tabularlist_versi]);
            }

            return $model->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

}
