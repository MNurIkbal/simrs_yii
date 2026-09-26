<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\Services\InternalService;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;


use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\TarifTindakanView;
use app\modules\v1\models\PerdaTarif;
use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\DaftarTindakanV;
use app\modules\v1\models\TipePaket;
use app\modules\v1\models\MasterTarifTindakanView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\MasterPaketMcuView;
use app\modules\v1\models\PaketDetailV;
use app\modules\v1\models\PaketPelayanan;
use app\modules\v1\models\PaketPelayananV;

use app\modules\v1\payload\TarifPayload;
use app\modules\v1\models\MasterKamarRuanganView;
use app\modules\v1\models\InfoHistoryTarifView;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoConstansId;
use Doco\models\LookupTransaksi;
use yii\web\UploadedFile;

class TarifTindakanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TarifTindakan';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["get-data-laporan-excel"] = ["GET"];
        $verbs["save-tarif"] = ["POST"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["update-tarif"] = ["PUT"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET", "DELETE"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        $request = Yii::$app->request;

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);

        // action get data kamar
        $actions['get-data-kamar'] = $this->getDataKamar();
        $actions['get-list-komponen'] = [
            'class' => 'Doco\actions\GetDataAction',
            'model' => new KomponenTarif,
            'selected' => [
                'komponentarif_id AS id',
                'komponentarif_nama as text',
                'komponentarif_id',
                'komponentarif_nama',
                'komponentarif_kode',
            ],
            'field_search' => [
                'komponentarif_nama',
                'komponentarif_kode',
            ],
            'other_where' => [
                ['not in','komponentarif_id', [DocoConstants::KOMPONEN_TARIF]],
            ]
        ];

        return $actions;
    }

    public function data()
    {
        $model = new TarifTindakanView;
        return $model;
    }

    public function masterData()
    {
        $model = new MasterTarifTindakanView;
        return $model;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = $this->masterData();
            $query = $model::find();
            $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }
                if (isset($advancedFilters['perdanama_sk']) ) {
                    $query->andFilterWhere(['perdatarif_id' =>  $advancedFilters['perdanama_sk'] ]);
                }
                if (isset($advancedFilters['kelaspelayanan_nama']) ) {
                    $query->andFilterWhere(['kelaspelayanan_id' =>  $advancedFilters['kelaspelayanan_nama'] ]);
                }
                if(isset($advancedFilters['kamarruangan_id'])) {
                    $kamarruangan_id = $advancedFilters['kamarruangan_id'];
                    if($kamarruangan_id == 0) {
                    $query->andWhere(['IS NOT', 'kamarruangan_id', NULL]);
                    unset($_GET['advanced-filter']['kamarruangan_id']);
                    }
                }
                if(isset($advancedFilters['jenis_tarif'])) {
                    $jenis_tarif = $advancedFilters['jenis_tarif'];
                    if($jenis_tarif == 2) {
                        $query->andWhere(['IS NOT', 'kamarruangan_id', NULL]);
                    }
                    elseif($jenis_tarif == 3) {
                        $query->andWhere(['IS NOT', 'dokter_id', NULL]);
                    }
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetData($id) 
    {
        $request = Yii::$app->request;
        $model = $this->data();
        $query = $model::find();
        if($id){
        $query->where(['tariftindakan_id'=>$id]);
        }
        $data = $query->one();
        $param = [
            'kelaspelayanan_id'=>$data['kelaspelayanan_id'],
            'penjamin_id'=>$data['penjamin_id'],
            'perdatarif_id'=>$data['perdatarif_id'],
            'is_deleted'=>0,
        ];
        $result = [
            'header'=> $data,
            'komponen'=>$this->getChild($param, $data['tariftindakan_id']),
        ];
        return $result;
    }

    public function getChild($params, $id)
    {
        try{
            $model = new TarifTindakan;
            $query = $model::find()->with(['komponentarif'])->where($params);
            $result = $query->asArray()->all();
        } catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    private function getTarifDataView($tariftindakan_id ,$komponentarif_id = false)
    {
        $model = $this->masterData();
        $query = $model::find();
        $query->where(['tariftindakan_id' => $tariftindakan_id ]);

        if ($komponentarif_id) {
            $query->andWhere(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
        }
        return $query->one();
    }

    private function getTarifDataKomponen($data ,$komponentarif_id = false, $is_komponentarif_id = false)
    {
        $daftarTindakanId = ArrayHelper::getValue($data, 'daftartindakan_id');
        $tipePaketId = ArrayHelper::getValue($data, 'tipepaket_id',null);
        $kamarRuanganId = ArrayHelper::getValue($data, 'kamarruangan_id');
        $kelasPelayananId = ArrayHelper::getValue($data, 'kelaspelayanan_id');
        $caraBayarId = ArrayHelper::getValue($data, 'carabayar_id');
        $penjaminId = ArrayHelper::getValue($data, 'penjamin_id');
        $perdaTarifId = ArrayHelper::getValue($data, 'perdatarif_id');
        $dokterId = ArrayHelper::getValue($data, 'dokter_id', null);
        $ruanganId = ArrayHelper::getValue($data, 'ruangan_id', null);

        $model = $this->masterData();
        $query = $model::find()->where([
            'kelaspelayanan_id'=> $kelasPelayananId,
            'carabayar_id'=> $caraBayarId,
            'penjamin_id'=> $penjaminId,
            'perdatarif_id'=> $perdaTarifId,
        ]);

        if (!empty($daftarTindakanId) ) {
            $query->andWhere(['daftartindakan_id'=> $daftarTindakanId ]);
        }

        if (!empty($tipePaketId) ) {
            $query->andWhere(['tipepaket_id'=> $tipePaketId]);
        }else{
            $query->andWhere(['tipepaket_id'=> null]);
        }

        if (!empty($kamarRuanganId) ) {
            $query->andWhere(['kamarruangan_id'=> $kamarRuanganId]);
        }

        if ($komponentarif_id) {
            $query->andWhere(['!=' ,'komponentarif_id', DocoConstants::KOMPONEN_TARIF ]);
        }

        if ($is_komponentarif_id) {
            $query->andWhere(['komponentarif_id' =>  DocoConstants::KOMPONEN_TARIF ]);
        }

        if(!empty($dokterId)) {
            $query->andWhere(['dokter_id' => $dokterId]);
        }
        else {
            $query->andWhere(['IS', 'dokter_id', NULL]);
        }

        if(!empty($ruanganId)) {
            $query->andWhere(['ruangan_id' => $ruanganId]);
        }
        // else {
        //     $query->andWhere(['IS', 'ruangan_id', NULL]);
        // }

        if ( isset($data['is_active']) ) {
            $query->andWhere(['is_active'=> $data['is_active'] ]);
        }

        return $query->asArray()->all();
    }

    public function actionGetDataTarifTindakan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $queriesHeader = $this->getTarifDataView($id, true);
            $totalTarif = ArrayHelper::getValue($queriesHeader, 'harga_tariftindakan', 0);
            $daftartindakan_id = !empty($queriesHeader->daftartindakan_id) ? $queriesHeader->daftartindakan_id : null;
            $isAkomodasi = false;
            if($daftartindakan_id) {
                $daftarTindakan = DaftarTindakan::findOne($daftartindakan_id);
                $isAkomodasi = $daftarTindakan->is_akomodasi;
            }
            $queriesKomponen = $this->getTarifDataKomponen($queriesHeader, true, false);
            $arrKomponen = [];
            foreach ($queriesKomponen as $key => $value) {
                $komponenTarifId = ArrayHelper::getValue($value, 'komponentarif_id');
                $persentaseKomponen = ArrayHelper::getValue($value, 'persentase_komponen');
                $hargaTarifTindakan = ArrayHelper::getValue($value, 'harga_tariftindakan', 0);
                $tipePaketId = ArrayHelper::getValue($value, 'tipepaket_id', null);
                $value['harga_tariftindakan'] = str_replace('.00', '', $hargaTarifTindakan);
                if(empty($persentaseKomponen)) {
                    if(empty($tipePaketId)){
                        $persentaseKomponen = round($hargaTarifTindakan/$totalTarif, 2) * 100;
                        $value['harga_tariftindakan'] = str_replace('.00', '', ($persentaseKomponen/100) * $totalTarif);
                    }
                }
                $value['persentase_komponen'] = $persentaseKomponen;
                if($daftartindakan_id) {
                    $arrKomponen[$komponenTarifId] = $value;
                }else{
                    $arrKomponen[] = $value;
                }
            }
            $result = [
                'header' => $queriesHeader,
                'komponen' => $arrKomponen,
                'total' => str_replace('.00', '', $totalTarif),
                'is_akomodasi' => $isAkomodasi
            ];
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionDelete($id)
    {
        try {
            $result = (new TarifTindakan)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getTarifTindakanView($data = null, $komponentarif = false)
    {
        try {
            $modelData = new TarifTindakan;
            $queries = $modelData::find();
            $queries->select([
                'tariftindakan_m.tariftindakan_id',
                'tariftindakan_m.daftartindakan_id',
                'daftartindakan_m.daftartindakan_kode',
                'daftartindakan_m.daftartindakan_nama',
                'tariftindakan_m.tipepaket_id',
                'tipepaket_m.tipepaket_nama',
                'tariftindakan_m.kelaspelayanan_id',
                'kelaspelayanan_m.kelaspelayanan_nama',
                'penjamin_m.carabayar_id',
                'carabayar_m.carabayar_nama',
                'tariftindakan_m.penjamin_id',
                'penjamin_m.penjamin_nama',
                'tariftindakan_m.komponentarif_id',
                'tariftindakan_m.harga_tariftindakan',
                'perdatarif_m.perdanama_sk',
                'tariftindakan_m.is_active',
                'tariftindakan_m.perdatarif_id',
                ]);

            $queries->from('tariftindakan_m');
            $queries->leftJoin('penjamin_m', 'penjamin_m.penjamin_id = tariftindakan_m.penjamin_id');
            $queries->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id');
            $queries->leftJoin('tipepaket_m', 'tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id');
            $queries->leftJoin('carabayar_m', 'carabayar_m.carabayar_id = penjamin_m.carabayar_id');
            $queries->leftJoin('kelaspelayanan_m', 'kelaspelayanan_m.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id');
            $queries->leftJoin('perdatarif_m', 'perdatarif_m.perdatarif_id = tariftindakan_m.perdatarif_id');
            if ( $komponentarif ) {
                $queries->where(['tariftindakan_m.komponentarif_id'=> DocoConstants::KOMPONEN_TARIF]);
            }
            
            $queries->andWhere(['tariftindakan_m.is_deleted'=> false]);
            if ($data) {
                $queries->andWhere(['tariftindakan_m.kelaspelayanan_id'=> $data['kelaspelayanan_id'],
                    'penjamin_m.carabayar_id'=> $data['carabayar_id'],
                    'tariftindakan_m.penjamin_id'=> $data['penjamin_id'],
                    'tariftindakan_m.perdatarif_id'=> $data['perdatarif_id'],
                ]);
                if (isset($data['daftartindakan_id'])) {
                    $queries->andWhere(['tariftindakan_m.daftartindakan_id'=> $data['daftartindakan_id'] ]);
                }
                if (isset($data['tipepaket_id'])) {
                    $queries->andWhere(['tariftindakan_m.tipepaket_id'=> $data['tipepaket_id'] ]);
                }
            }
            return $queries;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionSaveTarif()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $payload = new TarifPayload;
        $payload->attributes = $request->post();
        $daftartindakan_id = !empty($payload->daftartindakan_id) ? $payload->daftartindakan_id : null;
        $is_akomodasi = false;
        if($daftartindakan_id) {
            $daftarTindakan = DaftarTindakan::findOne($daftartindakan_id);
            $is_akomodasi = $daftarTindakan->is_akomodasi;
        }

        $payload->kamar_ruangan_id = $is_akomodasi ? $post['kamar_ruangan_id'] : null;
        $payload->scenario = TarifPayload::PAKET;

        /** Set Scenario */
        if($is_akomodasi) {
            $payload->scenario = TarifPayload::AKOMODASI;
        }
        elseif(!empty($payload->daftartindakan_id)) {
            $payload->scenario = TarifPayload::TINDAKAN;
        } 

        if (empty($payload->list_komponen) || !is_array($payload->list_komponen)) {
            return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Komponen tidak boleh kosong'
            ]);
        }

        $payload->is_akomodasi = $is_akomodasi;
        $kompTotal = $this->constans->actionGetId('komponen_total');
        $insertTarif = [];
        $totalTarif = 0;
        if ($payload->validate()) {
            $validUnique = $this->checkUniqueTarif($payload);
            if (is_array($validUnique)) return $validUnique;
            if ($payload->scenario == TarifPayload::PAKET) {
               $paketMapping = PaketPelayanan::find()->select([
                  'daftartindakan_id',
                  'paketdetail_id',
                  'ruangan_id'
              ])->andWhere([
                  'tipepaket_id' => $payload->tipepaket_id
              ])->asArray()->all();
                $listMapp = $listPaketDetail = [];  
                foreach ($paketMapping as $value) {
                    $daftartindakan_id = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : $value['paketdetail_id'];
                    $listMapp[$value['ruangan_id']][$daftartindakan_id] = $daftartindakan_id;
                }
                $tindakanValid = [];
                foreach ($payload->list_komponen as $value) {
                    $tindakanId = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                    $komponen = !empty($value['komponen']) ? $value['komponen'] : null;
                    $ruanganId = !empty($value['ruangan_id']) ? (int)$value['ruangan_id'] : null;
                    /** validasi required untuk daftartindakan dan komponen */
                    if (empty($tindakanId) || empty($komponen) || !is_array($komponen)) {
                        return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                            'text' => 'Data list komponen tidak valid.'
                        ]);
                    }
                    if (isset($listMapp[$ruanganId][$tindakanId])) {
                        $tindakanValid[] = $tindakanId;
                        $generateTarif = $this->generateTarif($payload, $komponen, $tindakanId, $ruanganId);
                        if(isset($generateTarif['status'])) {
                            return $generateTarif;
                        }
                        if (isset($generateTarif['data']) && isset($generateTarif['total_tarif'])) {
                            $totalTarif += $generateTarif['total_tarif'];
                            $insertTarif = ArrayHelper::merge($insertTarif,$generateTarif['data']);
                        } else {
                            $insertTarif = $generateTarif;
                        }
                    } 
                }
            } else {
                $generateTarif = $this->generateTarif($payload, $payload->list_komponen, $payload->daftartindakan_id);
                if (isset($generateTarif['data']) && isset($generateTarif['total_tarif'])) {
                    $totalTarif = $generateTarif['total_tarif'];
                    $insertTarif = ArrayHelper::merge($insertTarif,$generateTarif['data']);
                } else {
                    $insertTarif = $generateTarif;
                }
            }
            $transaction = $connection->beginTransaction();
            try {
                if ($payload->scenario === TarifPayload::PAKET) {
                    $attrParent = $this->parseAttributeSave($payload, null, $kompTotal, $totalTarif, $ruanganId);
                    $modelParent = new TarifTindakan;
                    $modelParent->attributes = $attrParent;
                    if ($modelParent->save()) {
                        $parentId = $modelParent->tariftindakan_id;
                        foreach ($insertTarif as $key => $value) {
                            $insertTarif[$key]['tarifparent_id'] = $parentId;
                        }
                    } else {
                        return $this->helper->callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $modelParent->errors
                        ]);
                    }
                }

                TarifTindakan::batchInsert($insertTarif);
                $transaction->commit();
                return $this->helper->callBack(DocoMessages::KEY_SUC_SYSTEM);
            } catch (\yii\db\Exception $e) {
                Yii::$app->response->statusCode = 500;
                $transaction->rollBack();
                $this->logError($e);
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                Yii::$app->response->statusCode = 422;
                $transaction->rollBack();
                $this->logError($e);
                return ['message' => $e->getMessage()];
            }
        }
        else {
            return $this->helper->callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
    }

    public function actionUpdateTarif($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $payload = new TarifPayload;
        $payload->attributes = $request->post();
        $daftartindakan_id = !empty($payload->daftartindakan_id) ? $payload->daftartindakan_id : null;
        $isPersentase = !empty($payload->is_persentase) ? $payload->is_persentase : false;
        $isPersentase = ($isPersentase == 1) ? true : false;
        $is_akomodasi = false;
        if($daftartindakan_id) {
            $daftarTindakan = DaftarTindakan::findOne($daftartindakan_id);
            $is_akomodasi = $daftarTindakan->is_akomodasi;
        }

        $payload->kamar_ruangan_id = isset($post['kamar_ruangan_id']) ? $post['kamar_ruangan_id'] : null;
        $payload->scenario = TarifPayload::PAKET;

        /** Set Scenario */
        if($is_akomodasi) {
            $payload->scenario = TarifPayload::AKOMODASI;
        }
        elseif(!empty($payload->daftartindakan_id)) {
            $payload->scenario = TarifPayload::TINDAKAN;
        }
        
        if (empty($payload->list_komponen) || !is_array($payload->list_komponen)) {
            return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
            'text' => 'Komponen tidak boleh kosong'
            ]);
        }
        $payload->tarif_id = $id;
        $payload->is_akomodasi = $is_akomodasi;
        $kompTotal = $this->constans->actionGetId('komponen_total');
        $insertTarif = [];
        $totalTarif = 0;
        $totalTarifKomponen = 0;
        foreach ($payload->list_komponen as $key => $value) {
            if(!empty($value['komponen'])) {
                foreach ($value['komponen'] as $k => $val) {
                    $totalTarifKomponen += (int) $val['nominal'];
                }
            }
            else {
                $totalTarifKomponen += (int) $value['nominal'];
            }
        }
        $newKomponen[] = [
            'komponen_id' => $kompTotal,
            'nominal' => $totalTarifKomponen
        ];
        $payload->list_komponen = array_merge($payload->list_komponen, $newKomponen);
        if ($payload->validate()) {
            $daftartindakan_id = $payload->daftartindakan_id;
            $kelaspelayanan_id = $payload->kelaspelayanan_id;
            $penjamin_id = $payload->penjamin_id;
            $perdatarif_id = $payload->perdatarif_id;
            $kamar_ruangan_id = $payload->kamar_ruangan_id;
            $tipepaket_id = $payload->tipepaket_id;
            $dokter_id = $payload->dokter_id;
            $validUnique = $this->checkUniqueTarif($payload, $id);
            if (is_array($validUnique)) return $validUnique;
            if ($payload->scenario == TarifPayload::PAKET) {
                $paketMapping = PaketPelayanan::find()->select([
                    'daftartindakan_id',
                    'paketdetail_id',
                    'ruangan_id'
                ])->andWhere([
                    'tipepaket_id' => $payload->tipepaket_id
                ])->asArray()->all();
                $listMapp = [];
                foreach ($paketMapping as $value) {
                    // $listMapp[$value['daftartindakan_id']] = $value['daftartindakan_id'];
                    $daftartindakan_id = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : $value['paketdetail_id'];
                    $listMapp[$value['ruangan_id']][$daftartindakan_id] = $daftartindakan_id;
                }
                $tindakanValid = [];
                
                foreach ($payload->list_komponen as $value) {
                    $tindakanId = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                    $ruanganId = !empty($value['ruangan_id']) ? $value['ruangan_id'] : null;
                    $komponen = !empty($value['komponen']) ? $value['komponen'] : [];
                    $validKomponen = true;
                    if(empty($tindakanId) || empty($komponen) || !is_array($komponen)) {
                        $validKomponen = false;
                    }
                    if (isset($listMapp[$ruanganId][$tindakanId])) {
                        // if (in_array($tindakanId, $listMapp[$ruanganId])) {
                        //     return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        //       'text' => 'Tindakan yang diinput tidak boleh sama.'
                        //     ]);
                        // }
                        $tindakanValid[] = $tindakanId;
                        $generateTarif = $this->generateTarif($payload, $komponen, $tindakanId, false);
                        if (isset($generateTarif['data']) && isset($generateTarif['total_tarif'])) {
                            $totalTarif += $generateTarif['total_tarif'];
                            $insertTarif = ArrayHelper::merge($insertTarif,$generateTarif['data']);
                        } else {
                            $insertTarif = $generateTarif;
                        }
                    }
                }
                // if (count($tindakanValid) !== count($listMapp)) {
                //     return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION, [
                //         'text' => 'Jumlah Tindakan tidak sesuai dengan paket'
                //     ]);
                // }
            } else {
                $generateTarif = $this->generateTarif($payload, $payload->list_komponen, $payload->daftartindakan_id, false);
                if (isset($generateTarif['data']) && isset($generateTarif['total_tarif'])) {
                    $totalTarif = $generateTarif['total_tarif'];
                    $insertTarif = ArrayHelper::merge($insertTarif,$generateTarif['data']);
                } else {
                    $insertTarif = $generateTarif;
                }
            }
            $transaction = $connection->beginTransaction();
            try {
                $modelParent = TarifTindakan::find()->andWhere([
                    'tariftindakan_id' => $payload->tarif_id
                ])->one();
                if (!empty($modelParent)) {
                    $modelParent->daftartindakan_id = empty($payload->tipepaket_id) ? $payload->daftartindakan_id : null;
                    $modelParent->tipepaket_id = empty($payload->daftartindakan_id) ? $payload->tipepaket_id : null;
                    $modelParent->kelaspelayanan_id = $payload->kelaspelayanan_id;
                    $modelParent->penjamin_id = $payload->penjamin_id;
                    $modelParent->perdatarif_id = $payload->perdatarif_id;
                    $modelParent->persencyto_tindakan = $payload->persencyto_tindakan;
                    $modelParent->persen_penyulit = $payload->persen_penyulit;
                    $modelParent->harga_tariftindakan = $totalTarif;
                    $modelParent->is_active = $payload->is_active;
                    $modelParent->kamarruangan_id = $payload->kamar_ruangan_id;
                    $modelParent->dokter_id = $dokter_id;
                    $modelParent->is_persentase = $isPersentase;
                    if ($modelParent->save()) {
                        $parentId = $payload->tarif_id;
                        if ($payload->scenario === TarifPayload::PAKET) {
                            foreach ($insertTarif as $key => $value) {
                                $insertTarif[$key]['tarifparent_id'] = $parentId;
                            }
                            if(!empty($dokter_id)) {
                                $deleteAll = Yii::$app->db->createCommand("
                                    DELETE FROM tariftindakan_m WHERE 
                                    tipepaket_id = {$payload->tipepaket_id} AND 
                                    kelaspelayanan_id = {$payload->kelaspelayanan_id} AND 
                                    penjamin_id = {$payload->penjamin_id} AND 
                                    perdatarif_id = {$payload->perdatarif_id} AND 
                                    tariftindakan_id != {$parentId} AND 
                                    dokter_id = {$payload->dokter_id}
                                ")->execute();
                            }
                            else {
                                $deleteAll = Yii::$app->db->createCommand("
                                    DELETE FROM tariftindakan_m WHERE 
                                    tipepaket_id = {$payload->tipepaket_id} AND 
                                    kelaspelayanan_id = {$payload->kelaspelayanan_id} AND 
                                    penjamin_id = {$payload->penjamin_id} AND 
                                    perdatarif_id = {$payload->perdatarif_id} AND 
                                    tariftindakan_id != {$parentId} AND 
                                    dokter_id IS NULL
                                ")->execute();
                            }
                        } else {
                            $whereKamarRuangan = ($kamar_ruangan_id) ? "AND kamarruangan_id = {$kamar_ruangan_id}" : "";
                            $whereDokter = ($dokter_id) ? "AND dokter_id = {$dokter_id}" : "AND dokter_id IS NULL";
                            Yii::$app->db->createCommand("
                                DELETE FROM tariftindakan_m 
                                WHERE daftartindakan_id = {$daftartindakan_id}
                                AND kelaspelayanan_id = {$kelaspelayanan_id}
                                AND penjamin_id = {$penjamin_id}
                                AND perdatarif_id = {$perdatarif_id}
                                AND tipepaket_id IS NULL
                                {$whereKamarRuangan}
                                {$whereDokter}
                            ")->execute();
                        }
                        TarifTindakan::batchInsert($insertTarif);
                        $transaction->commit();
                        return $this->helper->callBack(DocoMessages::KEY_SUC_SYSTEM);
                    } else {
                      return $this->helper->callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $modelParent->errors
                      ]);
                    }
                }
                return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                    'text' => 'Tarif tidak ditemukan'
                ]);
            } catch (\yii\db\Exception $e) {
                Yii::$app->response->statusCode = 500;
                $transaction->rollBack();
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                Yii::$app->response->statusCode = 500;
                $transaction->rollBack();
                return ['message' => $e->getMessage()];
            }
        }

        return $this->helper->callBack(DocoMessages::KEY_ERR_SYSTEM, [
            'data' => $payload->errors
        ]);
    }

    protected function getDataTarif(TarifPayload $payload, $all = false)
    {
        $is_akomodasi = $payload->is_akomodasi;
        $kompTotal = $this->constans->actionGetId('komponen_total');
        $dokter_id = $payload->dokter_id;
        $model = $this->masterData();
        $query = $model::find();
        $query->where([
            'kelaspelayanan_id' => $payload->kelaspelayanan_id,
            'penjamin_id' => $payload->penjamin_id,
            'perdatarif_id'=> $payload->perdatarif_id,
        ]);
        if(!empty($dokter_id)) {
          $query->andWhere(['dokter_id'=> $payload->dokter_id]);
        }
        else {
            $query->andWhere(['IS', 'dokter_id', new \yii\db\Expression('null')]);
        }
        if (!empty($payload->daftartindakan_id)) {
            $query->andWhere(['daftartindakan_id'=> $payload->daftartindakan_id]);
        } else {
            $query->andWhere(['tipepaket_id'=> $payload->tipepaket_id]);
        }

        if ($all) {
            $query->andWhere(['!=' ,'komponentarif_id', $kompTotal ]);
        } else {
            $query->andWhere(['komponentarif_id' =>  $kompTotal ]);
        }

        if($is_akomodasi) {
            $query->andWhere(['kamarruangan_id' => $payload->kamar_ruangan_id]);
        }
        return $query;
    }

    private function checkUniqueTarif(TarifPayload $payload, $idTarif = null)
    {
        $message = '';
        $getTarif = $this->getDataTarif($payload)->asArray()->one();
        if (!empty($getTarif)) {
            $isUnique = true;
            if ($idTarif) {
                if ($getTarif['tariftindakan_id'] == $idTarif) {
                    $isUnique = false;
                }
            }
            if ($isUnique) {
                if (isset($getTarif['daftartindakan_id'] )) {
                $message .= 'Tindakan '.'<strong>'.$getTarif['daftartindakan_kode'].' - '.$getTarif['daftartindakan_nama'].'</strong>';
                } else {
                    $message .= 'Paket '.'<strong>'.$getTarif['tipepaket_nama'].'</strong>';
                }

                $message .= ', Kelas pelayanan <strong>'. $getTarif['kelaspelayanan_nama'] .'</strong>,  Cara bayar <strong>';
                $message .= $getTarif['carabayar_nama'].'</strong>, Penjamin <strong>'.$getTarif['penjamin_nama'].'</strong>, Perda <strong>';
                $message .= $getTarif['perdanama_sk'].'</strong>';
                if(!empty($payload->dokter_id)) {
                    if($payload->dokter_id == $getTarif['dokter_id'])
                        $message .= ', dan Dokter <strong>'. $getTarif['dokter'] .'<strong>';
                }
                $message .= 'sudah digunakan.';
                return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                    'text' => $message,
                ]);
            }
        }
        return true;
    }

    protected function generateTarif(TarifPayload $payload, array $komponen, $tindakanId, $newRecord = true)
    {
        $kompTotal = $this->constans->actionGetId('komponen_total');
        $totalTarif = 0;
        $duplicateKom = [];
        $payload->kamar_ruangan_id = !empty($payload->kamar_ruangan_id) ? $payload->kamar_ruangan_id : null;
        foreach ($komponen as $valKomp) {
            $komponenId = !empty($valKomp['komponen_id']) ? (int) $valKomp['komponen_id'] : null;
            $nominal = !empty($valKomp['nominal']) ? $valKomp['nominal'] : 0;
            $ruanganId = !empty($valKomp['ruangan_id']) ? (int) $valKomp['ruangan_id'] : null;
            $persentase = !empty($valKomp['persentase']) ? $valKomp['persentase'] : 0;

            /** Validasi Komponen  nominal dan id komponen harus numerik 
             *   Komponen detail tidak ada yang 6 atau komponen total
            **/
            if (!is_numeric($komponenId) || !is_numeric($nominal) /*|| $komponenId === $kompTotal*/) {
                return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                    'text' => 'Data list komponen tidak valid.'
                ]);
            }
            if (isset($duplicateKom[$komponenId])) {
                return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                    'text' => 'Komponen tidak boleh ada yang sama'
                ]);
            }
            $duplicateKom[$komponenId] = true;
            if ($nominal < 0) {
                return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                    'text' => 'Nominal tidak boleh kecil dari 0.'
                ]);
            }
            $insertTarif[] = $this->parseAttribute($payload, $tindakanId, $komponenId, $nominal, $ruanganId, $persentase);
            $totalTarif += ($komponenId != $kompTotal) ? $nominal : 0;
        }
        if (empty($payload->tipepaket_id) && $newRecord) {
            $insertTarif[] = $this->parseAttribute($payload, $tindakanId, $kompTotal, $totalTarif);
        }

        return [
            'data' => $insertTarif,
            'total_tarif' => $totalTarif
        ];
    }

    protected function generateTarifSave(TarifPayload $payload, array $komponen, $tindakanId, $ruangan_id, $newRecord = true)
    {
        $kompTotal = $this->constans->actionGetId('komponen_total');
        $totalTarif = 0;
        $duplicateKom = [];
        $payload->kamar_ruangan_id = !empty($payload->kamar_ruangan_id) ? $payload->kamar_ruangan_id : null;
        
        foreach ($komponen as $valKomp) {
            $komponenId = !empty($valKomp['komponen_id']) ? (int) $valKomp['komponen_id'] : null;
            $nominal = !empty($valKomp['nominal']) ? $valKomp['nominal'] : 0;

            /** Validasi Komponen  nominal dan id komponen harus numerik 
             *   Komponen detail tidak ada yang 6 atau komponen total
            **/
            if (!is_numeric($komponenId) || !is_numeric($nominal) /*|| $komponenId === $kompTotal*/) {
            return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Data list komponen tidak valid.'
            ]);
            }
            
            if (isset($duplicateKom[$komponenId])) {
            return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Komponen tidak boleh ada yang sama'
            ]);
            }
            $duplicateKom[$komponenId] = true;

            if ($nominal < 0) {
            return $this->helper->callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Nominal tidak boleh kecil dari 0.'
            ]);
            }
            $insertTarif[] = $this->parseAttributeSave($payload, $tindakanId, $komponenId, $nominal, $ruangan_id);
            $totalTarif += ($komponenId != $kompTotal) ? $nominal : 0;
        }
        if (empty($payload->tipepaket_id) && $newRecord) {
            $insertTarif[] = $this->parseAttributeSave($payload, $tindakanId, $kompTotal, $totalTarif, $ruangan_id);
        }

        return [
            'data' => $insertTarif,
            'total_tarif' => $totalTarif
        ];
    }

    private function parseAttributeSave(TarifPayload $payload, $tindakanId, $komponen, $nominal)
    {
        return [
            'kelaspelayanan_id' => $payload->kelaspelayanan_id,
            'komponentarif_id' => $komponen,
            'daftartindakan_id' => $tindakanId,
            'perdatarif_id' => $payload->perdatarif_id,
            'harga_tariftindakan' => $nominal,
            'persendiskon_tindakan' => 0,
            'hargadiskon_tindakan' => 0,
            'persencyto_tindakan' => $payload->persencyto_tindakan,
            'tipepaket_id' => $payload->tipepaket_id,
            'penjamin_id' => $payload->penjamin_id,
            'is_deleted' => false,
            'is_active' => $payload->is_active,
            'is_clone' => false,
            'tarifparent_id' => null,
            'kamarruangan_id' => $payload->kamar_ruangan_id,
            'persen_penyulit' => $payload->persen_penyulit,
            'dokter_id' => $payload->dokter_id,
            'is_persentase' => ($payload->is_persentase == 1) ? true : false,
        ];
    }

    private function parseAttribute(TarifPayload $payload, $tindakanId, $komponen, $nominal, $ruangan_id = null, $persentase = 0)
    {
        return [
            'kelaspelayanan_id' => $payload->kelaspelayanan_id,
            'komponentarif_id' => $komponen,
            'daftartindakan_id' => $tindakanId,
            'perdatarif_id' => $payload->perdatarif_id,
            'harga_tariftindakan' => $nominal,
            'persendiskon_tindakan' => 0,
            'hargadiskon_tindakan' => 0,
            'persencyto_tindakan' => $payload->persencyto_tindakan,
            'tipepaket_id' => $payload->tipepaket_id,
            'penjamin_id' => $payload->penjamin_id,
            'is_deleted' => false,
            'is_active' => $payload->is_active,
            'is_clone' => false,
            'tarifparent_id' => null,
            'kamarruangan_id' => $payload->kamar_ruangan_id,
            'persen_penyulit' => $payload->persen_penyulit,
            'dokter_id' => $payload->dokter_id,
            'ruangan_id' => $ruangan_id,
            'persentase_komponen' => $persentase,
            'is_persentase' => ($payload->is_persentase == 1) ? true : false,
        ];
    }

    public function actionView($id = null)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function getData($id = null)
    {
        $data = TarifTindakan::find()
        ->joinWith(array
            (
                'perdatarif' =>function($query){
                    $query->select(array('perdatarif_m.perdanama_sk'));
                },
                'komponentarif' => function($query){
                    $query->select(array('komponentarif_nama'));
                },
                'kelaspelayanan' => function($query){
                    $query->select(array('kelaspelayanan_nama'));
                },
                'daftartindakan' => function($query){
                    $query->select(array('daftartindakan_kode','daftartindakan_nama'));
                },
                'jenistarif' => function($query){
                    $query->select(array('jenistarif_kode','jenistarif_nama'));
                }
            )
        )
        ->select([
            'tariftindakan_m.tariftindakan_id',
            'tariftindakan_m.tariftindakan_id',
            'tariftindakan_m.tariftindakan_id',
            'tariftindakan_m.tariftindakan_id',
            'tariftindakan_m.persendiskon_tindakan',
            'tariftindakan_m.harga_tariftindakan',
            'tariftindakan_m.persencyto_tindakan',
            'tariftindakan_m.is_active',
            'perdatarif_m.perdatarif_id',
            'komponentarif_m.komponentarif_id',
            'kelaspelayanan_m.kelaspelayanan_id',
            'daftartindakan_m.daftartindakan_id',
            'daftartindakan_m.daftartindakan_kode',
            'jenistarif_m.jenistarif_id'
        ]);
        if ($id) {
            $data->where(['tariftindakan_m.tariftindakan_id' => $id]);
        }

        return $data;
    }

    /**
    * @controller actionPrintTarifTindakan
    * @attribute #table_detail# => menampilkan data master tarif tindakan
    **/

    public function actionPrintTarifTindakan()
    {
        $model = $this->data();
        $query = $model::find(true);
        $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();

        $result = ['data'=>$data];

        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index',$result),
        ];
        $print->Output();
    }

    public function actionGetKomponen()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $data = new KomponenTarif();
            $result = $data::find();
            $result->select(['komponentarif_id', 'komponentarif_kode', 'komponentarif_nama', 'persen_delegasi']);
            if(!empty($post['term'])){
                $term = $post['term'];
                $result->where(['ILIKE','LOWER(komponentarif_nama)',$term]);
                $result->orWhere(['ILIKE','LOWER(komponentarif_kode)',$term]);
            }
            $result = $result->asArray()->all();
        }catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    public function actionGetListKomponen()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);
          
        $model = new KomponenTarif();
        $query = $model::find();
        if($term){
            $query->where(['ILIKE','LOWER(komponentarif_nama)',$term]);
            $query->orWhere(['ILIKE','LOWER(komponentarif_kode)',$term]);
        }

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }
    
    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $term = $request->get('search', null);
        $result = [];
        try{
            $data = new DaftarTindakan();
            $result = $data::find();
            $result->select(['daftartindakan_m.daftartindakan_id', 
                'daftartindakan_m.daftartindakan_kode', 
                'daftartindakan_m.daftartindakan_nama', 
                'kelompoktindakan_m.kelompoktindakan_persencyto', 
                'kelompoktindakan_m.kelompoktindakan_persendiskon',
                'daftartindakan_m.is_akomodasi'
            ])
            ->from("daftartindakan_m")
            ->leftJoin("kelompoktindakan_m", "daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id");
            if(!empty($term)){
                $result->where(['ILIKE','LOWER(daftartindakan_nama)',$term]);
                $result->orWhere(['ILIKE','LOWER(daftartindakan_kode)',$term]);
            }
            $result = $result->asArray()->all();
        }catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    public function actionGetPaket()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $result = [];
        try{
            $data = new TipePaket();
            $result = $data::find();
            $result->select(['tipepaket_id', 'tipepaket_kode', 'tipepaket_nama', 'is_mcu']);
            if(!empty($post['term'])){
                $term = $post['term'];
                $result->where(['ILIKE','LOWER(tipepaket_nama)',$term]);
                $result->orWhere(['ILIKE','LOWER(tipepaket_kode)',$term]);
            }
            $result = $result->asArray()->all();
        }catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    public function actionGetTindakanPaket()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = [];
        try{
            $data = $this->data();
            $result = $data::find();
            $result->select(['tindakan_paket_id', 'nama_tindakan_paket']);
            if(!empty($post['term'])){
                $term = $post['term'];
                $result->where(['ILIKE','LOWER(nama_tindakan_paket)',$term]);
            }
            $result->orderBy(['nama_tindakan_paket'=>SORT_ASC]);
            $result->groupBy(['tindakan_paket_id', 'nama_tindakan_paket']);
            $result = $result->asArray()->all();
        }catch(\yii\db\Exception $e){
            $result = [];
        }
        return $result;
    }

    public function actionDetailPaketTindakan()
    {
        $get = \Yii::$app->request->get();

        // Try catch
        try {
            // Find model
            $model = new PaketPelayananV;
            $query = $model::find();
            $query = $query->andWhere(['tipepaket_id' => DocoHelpers::decrypt($get['tipepaket_id'])]);
            $is_mcu = !empty($get['is_mcu']) ? $get['is_mcu'] : false;
            $kelaspelayanan_id = !empty($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : null;
            $penjamin_id = !empty($get['penjamin_id']) ? $get['penjamin_id'] : null;
            $perdatarif_id = !empty($get['perdatarif_id']) ? $get['perdatarif_id'] : null;
            if(!$is_mcu){
                return $query->all();
            }else{
                $listPaketDetail = $query->all();
                $komponenDetail = $listDaftartindakan = [];
                if($is_mcu && !empty($kelaspelayanan_id) && !empty($penjamin_id) && !empty($perdatarif_id)){
                    foreach ($listPaketDetail as $val){
                        $listDaftartindakan[$val->ruangan_id][] = $val->tindakan_paket_id;
                    }
                    foreach($listDaftartindakan as $k => $val){
                        if(is_array($val) && !empty($val)){
                            $listDaftartindakanId = implode("," , $val);
                            $listDaftartindakanId ="(".$listDaftartindakanId.")";
                            $ruangan_id = $k;
                            $komponentarif_id = DocoConstants::KOMPONEN_TARIF;
                            $komponen = Yii::$app->db->createCommand("
                                SELECT
                                    ruangan_id,
                                    instalasi_id,
                                    kelaspelayanan_id,
                                    penjamin_id,
                                    daftartindakan_id,
                                    tipepaket_id,
                                    komponentarif_id,
                                    komponentarif_nama,
                                    harga_tariftindakan,
                                    persencyto_tindakan,
                                    carabayar_id,
                                    persen_penyulit,
                                    dokter_id,
                                    ruangan_tarif_id
                                FROM tarifkomponenrs_fn(:ruangan_id, :penjamin_id, :kelaspelayanan_id,'pelayanan')
                                WHERE daftartindakan_id IN $listDaftartindakanId
                                AND tipepaket_id is NULL
                                AND komponentarif_id != :komponentarif_id
                                AND dokter_id IS NULL
                            ")
                            ->bindParam(':komponentarif_id', $komponentarif_id)
                            ->bindParam(':ruangan_id', $ruangan_id)
                            ->bindParam(':penjamin_id', $penjamin_id)
                            ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                            ->queryAll();
                            $komponenDetail[$ruangan_id] = $komponen;

                        }
                    }

                    return [
                        'listPaketDetail' => $listPaketDetail,
                        'komponenDetail' => $komponenDetail,
                    ];
                }else{
                    return $listPaketDetail;
                }
            }

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionDetailPaketTindakanSatuan()
    {
        $get = \Yii::$app->request->get();

        // Try catch
        try {
            // Find model
            $model = new PaketPelayananV;
            if (!empty($get['tipepaket_id']) || (!empty($get['tindakan_paket_id']))) {
              $query = $model::find();
              $query = $query->andWhere(['tipepaket_id' => DocoHelpers::decrypt($get['tipepaket_id'])]);
              $query = $query->andWhere(['tindakan_paket_id' => DocoHelpers::decrypt($get['tindakan_paket_id'])]);
              return $query->one();

              // Doco active filter
              $query = DocoRestActiveFilter::advancedFilter($model, $query);

              // Return data
              return new ActiveDataProvider([
                  'query' => $query,
              ]);
            }else{
              return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => 'Tidak Ada Data Yang Sesuai / Parameter tidak boleh kosong'
              ]);
            }
            
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

    public function actionSetDefault()
    {
          $request = Yii::$app->request;
          $model = new TarifTindakan;
          $connection = Yii::$app->db;
          $transaction = $connection->beginTransaction();
          try {
            $post = $request->post();
            $getData = $model->find()->where(['penjamin_id'=>$post['penjamin_awal']])->asArray()->all();
            $rawData = [];
            foreach ($getData as $key => $value) :
              unset($value['tariftindakan_id']);
              $value['penjamin_id'] = $post['penjamin_tujuan'];
              $value['is_clone'] = true;
              $rawData[] = $value;
            endforeach;
            try {
              $save_insert = TarifTindakan::batchInsert($rawData);
              $transaction->commit();
              return true;
            } catch(\yii\db\Exception $e){
                $transaction->rollBack();
                throw new \Exception($e->getMessage());
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

    public function actionExportExcel()
    {
        $title = 'Master Komponen';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchJenisTindakan = '';
            $searchNamaTindakan = '';
            $searchKelasPelayanan = '';
            $searchCaraBayar = '';
            $searchPenjamin = '';
            $searchPerdaSK = '';

            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['jenis_tindakan_paket'])) {
                    $searchJenisTindakan = $advancedFilters['jenis_tindakan_paket'];
                }

                if (!empty($advancedFilters['nama_tindakan_paket'])) {
                    $searchNamaTindakan = $advancedFilters['nama_tindakan_paket'];
                }
                // if (!empty($advancedFilters['kelaspelayanan_nama'])) {
                //     $getKelasPelayanan = KelasPelayanan::find($advancedFilters['kelaspelayanan_nama'])->one();
                //     $searchKelasPelayanan = $getKelasPelayanan->kelaspelayanan_nama;
                // }

                // if (!empty($advancedFilters['carabayar_id'])) {
                //     $getCaraBayar = CaraBayar::find($advancedFilters['carabayar_id'])->one();
                //     $searchCaraBayar = $getCaraBayar->carabayar_nama;
                // }

                // if (!empty($advancedFilters['penjamin_id'])) {
                //     $getPenjamin = Penjamin::find($advancedFilters['penjamin_id'])->one();
                //     $searchPenjamin = $getPenjamin->penjamin_nama;
                // }

                // if (!empty($advancedFilters['perdatarif_id'])) {
                //     $getPerdatarif = PerdaTarif::find($advancedFilters['perdatarif_id'])->one();
                //     $searchPerdaSK = $getPerdatarif->perdanama_sk;
                // }

                if (!empty($advancedFilters['is_active'])) {
                    $searchStatus = $advancedFilters['is_active'];
                }
            }

            $model = $this->masterData();
            $query = $model::find();
            $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            $result = $query->asArray()->all();

            $data = [];
            if (!empty($result)) {
                $counter = 0;
                foreach ($result as $index => $value) {
                    $data[$counter][\Yii::t('app', 'Jenis Tindakan / Paket')] = $value['jenis_tindakan_paket'];
                    $data[$counter][\Yii::t('app', 'Nama Tindakan / Paket')] = $value['nama_tindakan_paket'];
                    $data[$counter][\Yii::t('app', 'Kelas Pelayanan')] = $value['kelaspelayanan_nama'];
                    $data[$counter][\Yii::t('app', 'Cara bayar')] = $value['carabayar_nama'];
                    $data[$counter][\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
                    $data[$counter][\Yii::t('app', 'Perda / SK')] = $value['perdanama_sk'];
                    $data[$counter][\Yii::t('app', 'Persen Cyto (%)')] = "'".$value['persencyto_tindakan']."'";
                    $data[$counter][\Yii::t('app', 'Persen Diskon (%)')] = "'".$value['persendiskon_tindakan']."'";
                    $data[$counter][\Yii::t('app', 'Harga (Rp)')] = !empty($value['harga_tariftindakan']) ? DocoHelpers::formatNumber($value['harga_tariftindakan']) : 0 ;
                    $data[$counter][\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
                    if (!empty($advancedFilters['kelaspelayanan_nama'])) {
                        $searchKelasPelayanan = $value['kelaspelayanan_nama'];
                    }
                    if (!empty($advancedFilters['carabayar_id'])) {
                        $searchCaraBayar = $value['carabayar_nama'];
                    }
                    if (!empty($advancedFilters['penjamin_id'])) {
                        $searchPenjamin = $value['penjamin_nama'];
                    }

                    if (!empty($advancedFilters['perdatarif_id'])) {
                        $searchPerdaSK = $value['perdanama_sk'];
                    }
                    $counter++;
                }
            }

            if (empty($searchStatus)) {
               $searchStatused = '';
            }else{
                $searchStatused = ($searchStatus == 1 ) ? 'Aktif' : 'Tidak Aktif';
            }


            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Jenis Paket/Tindakan' => $searchJenisTindakan,
                'Nama Paket/Tindakan' => $searchNamaTindakan,
                'Kelas Pelayanan' => $searchKelasPelayanan,
                'Cara Bayar' => $searchCaraBayar,
                'Penjamin' => $searchPenjamin,
                'Perda/SK' => $searchPerdaSK,
                'Status' => $searchStatused,
            ];
            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                    // 'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];
            $footer = count($data) > 0 ? $footer : [] ;
            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data di table 
    * @attribute #searchJenisTindakan# => Untuk menampilkan data pencarian Jenis Tindakan 
    * @attribute #searchNamaTindakan# => Untuk menampilkan data pencarian nama Tindakan 
    * @attribute #searchKelasPelayanan# => Untuk menampilkan data pencarian Kelas Pelayaan 
    * @attribute #searchCaraBayar# => Untuk menampilkan data pencarian Cara Bayar 
    * @attribute #searchPenjamin# => Untuk menampilkan data pencarian Penjamin 
    * @attribute #searchPerdaSK# => Untuk menampilkan data pencarian Perda 
    * @attribute #searchStatused# => Untuk menampilkan data pencarian Status 
   */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $model = $this->masterData();
        $query = $model::find();
        $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);

        $searchJenisTindakan = '';
        $searchNamaTindakan = '';
        $searchKelasPelayanan = '';
        $searchCaraBayar = '';
        $searchPenjamin = '';
        $searchPerdaSK = '';

        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (!empty($advancedFilters['jenis_tindakan_paket'])) {
                $searchJenisTindakan = $advancedFilters['jenis_tindakan_paket'];
            }

            if (!empty($advancedFilters['nama_tindakan_paket'])) {
                $searchNamaTindakan = $advancedFilters['nama_tindakan_paket'];
            }
            if (!empty($advancedFilters['kelaspelayanan_id'])) {
                $getKelasPelayanan = KelasPelayanan::find($advancedFilters['kelaspelayanan_id'])->one();
                $searchKelasPelayanan = $getKelasPelayanan->kelaspelayanan_nama;
            }

            if (!empty($advancedFilters['carabayar_id'])) {
                $getCaraBayar = CaraBayar::find($advancedFilters['carabayar_id'])->one();
                $searchCaraBayar = $getCaraBayar->carabayar_nama;
            }

            if (!empty($advancedFilters['penjamin_id'])) {
                $getPenjamin = Penjamin::find($advancedFilters['penjamin_id'])->one();
                $searchPenjamin = $getPenjamin->penjamin_nama;
            }

            if (!empty($advancedFilters['perdatarif_id'])) {
                $getPerdatarif = PerdaTarif::find($advancedFilters['perdatarif_id'])->one();
                $searchPerdaSK = $getPerdatarif->perdanama_sk;
            }

            if (!empty($advancedFilters['is_active'])) {
                $searchStatus = $advancedFilters['is_active'];
            }
        }

        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }
        }

        if (empty($searchStatus)) {
           $searchStatused = '';
        }else{
            $searchStatused = ($searchStatus == 1 ) ? 'Aktif' : 'Tidak Aktif';
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['created_date' => SORT_DESC]);
        $getData = $query->asArray()->all();

        $result = [];
        if (!empty($getData)) {
            $counter = 0;
            foreach ($getData as $index => $value) {
                $counter++;
                $result[] = $value ;
            }
        }
        $print = new DocoPrint();
        $print->attributes = [
                '#datatable#' => $this->renderPartial('_dataTable_pdf', [
                    'result' => $result,
                ]),
                '#searchJenisTindakan#' => $searchJenisTindakan,
                '#searchNamaTindakan#' => $searchNamaTindakan,
                '#searchKelasPelayanan#' => $searchKelasPelayanan,
                '#searchCaraBayar#' => $searchCaraBayar,
                '#searchPenjamin#' => $searchPenjamin,
                '#searchPerdaSK#' => $searchPerdaSK,
                '#searchStatused#' => $searchStatused,
            ];
        $print->Output();
    }

    private function getDataKamar()
    {
        $request = Yii::$app->request;
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $_GET['term'] = $request->get('q');
        return [
          'class' => 'Doco\actions\GetDataAction',
            'model' => new MasterKamarRuanganView,
            'selected' => [
                'kamarruangan_id AS id',
                'kamarruangan_nokamar as text',
                'kamarruangan_id',
                'kamarruangan_nokamar',
                'ruangan_nama'
            ],
            'field_search' => [
                'kamarruangan_nokamar',
                'ruangan_nama'
            ],
            'default_where' => [
              ['kelaspelayanan_id', $kelaspelayanan_id],
            ],
            'orderby' => [
              ['kamarruangan_nokamar', 'ASC']
            ]
        ];
    }

    private function getKomponenTarifTotal($payload)
    {
        $model = new TarifTindakan;
        $query = $model::find();
        $query->where(['kelaspelayanan_id'=> $payload['kelaspelayanan_id'],
                       'perdatarif_id'=> $payload['perdatarif_id'],
                       'kamarruangan_id' => $payload['kamar_ruangan_id'],
                       'komponentarif_id' => DocoConstants::KOMPONEN_TARIF
                      ]);

        if (isset($payload['daftartindakan_id'])) {
            $query->andWhere(['daftartindakan_id'=> $payload['daftartindakan_id'] ]);
        }

        $data = $query->one();

        return [
          'komponen_id' => $data->komponentarif_id,
          'nominal' => (int) $data->harga_tariftindakan
        ];
    }

    public function actionGetDataLog()
    {
        $request = Yii::$app->request;
        $daftartindakan_id = $request->get('daftartindakan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $tipepaket_id = $request->get('tipepaket_id', null);
        $dokter_id = $request->get('dokter_id', null);
        $perda_id = $request->get('perda_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $data = $this->getDataLog($daftartindakan_id, $penjamin_id, $kelaspelayanan_id, $tipepaket_id, $dokter_id, $perda_id, $ruangan_id);
        return new ActiveDataProvider([
            'query' => $data,
        ]);
    }

    private function getDataLog($daftartindakan_id, $penjamin_id, $kelaspelayanan_id, $tipepaket_id, $dokter_id, $perda_id, $ruangan_id = null)
    {
        $model = new InfoHistoryTarifView;
        $query = $model::find(true)->where([
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'perdatarif_id' => $perda_id,
        ]);
        if(!empty($dokter_id)) {
            $query->andWhere(['dokter_id' => $dokter_id]);
        }
        if(!empty($daftartindakan_id)) {
            $query->andWhere(['daftartindakan_id' => $daftartindakan_id]);
        }
        else {
            $query->andWhere(['tipepaket_id' => $tipepaket_id]);
        }

        if (!empty($ruangan_id)) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        } else {
            $query->andWhere(['IS', 'ruangan_id', NULL]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataLaporanExcel($getData)->asArray()->all();

        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTarifTindakanExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportTarifTindakan' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanTarifTindakanExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function GetDataLaporanExcel($advancedFilters = [])
    {
        $model = new MasterTarifTindakanView;
        $query = $model::find();
        $query->where(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

        if (isset($advancedFilters)) {
            if (isset($advancedFilters['is_active'])) {
                $query->andWhere(['is_active' =>  $advancedFilters['is_active']]);
            }
        }
        $query->orderBy(['created_date' => SORT_DESC]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;

            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path . '/' . $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '/Laporan Tarif Tindakan.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionGetDefaultKomponen()
    {
        $komponenRs = (new DocoConstansId)->actionGetId('komponen_rs');
        return KomponenTarif::findOne($komponenRs);
    }
}
