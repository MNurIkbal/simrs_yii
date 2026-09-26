<?php
/**
 * Author : Dede Herdiana
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;


use app\modules\v1\models\TipeDiskonView;
use app\modules\v1\models\TipeDiskonDetailView;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\TipeDiskon;
use app\modules\v1\models\TipeDiskonDetail;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\DaftarTindakan;

class TipeDiskonController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TipeDiskonDetailView';

    public function actionGetTipeDiskon(){
        $request = Yii::$app->request;
        $tipediskon_id = $request->post('tipediskon_id', null);
        try {
            $model = new TipeDiskonView;
            $query = $model::find(true);
            if(!empty($tipediskon_id)){
                $query->andWhere(['tipediskon_id' => $tipediskon_id]);
            }
            $advancedFilters = $request->get('advanced-filter', []);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetTipeDiskonDetail(){
        $request = Yii::$app->request;
        $tipediskon_id = $request->get('tipediskon_id', null);
        try {
            $model = new TipeDiskonDetailView;
            $query = $model::find();
            if(!empty($tipediskon_id)){
                $query->andWhere([
                    'tipediskon_id' => $tipediskon_id,
                ]);
            }
            $advancedFilters = $request->get('advanced-filter', []);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSave(){
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        if($request->post()){
            try {
                $tipediskon_id = $request->post('tipediskon_id', null);
                $model = new TipeDiskon;
                if (!empty($tipediskon_id)) {
                    $dataExist = TipeDiskon::find()->where(['tipediskon_id' => $tipediskon_id])->one();
                    $model = !empty($dataExist) ? $dataExist : new TipeDiskon;
                }
                $model->attributes = $request->post();
                $details = $request->post('detail', []);
                $deleted_data = $request->post('deleted_data', []);
                $formName = "TipeDiskonForm";

                if ($model->validate() && $model->save() && $model->getPrimaryKey()) {
                    if(!empty($request->post('tipediskon_id', null))){
                        $tipediskon_id = $model->getPrimaryKey();
                        if(is_array($details) && !empty($details)){
                            $rows = [];
                            foreach ($details as $value) {
                                $value['tipediskon_id'] = $tipediskon_id;
                                $jenislayanan_id = !empty($value['jenislayanan_id']) ? $value['jenislayanan_id'] : 0;
                                $layanan_id = !empty($value['layanan_id']) ? $value['layanan_id'] : 0;
                                $dataExist = TipeDiskonDetail::find()->where(['tipediskon_id' => $tipediskon_id, 'jenislayanan_id' => $jenislayanan_id, 'layanan_id' => $layanan_id])->one();
                                $detail = !empty($dataExist) ? $dataExist : new TipeDiskonDetail;
                                $detail->attributes = $value;
                                $detail->tipediskon_id = $tipediskon_id;
                                if(!empty($dataExist)){
                                    $detail->save();
                                }else{
                                    $rows[] = $detail->attributes;
                                }
                            }
                            TipeDiskonDetail::batchInsert($rows);
                        }
                        if(is_array($deleted_data) && !empty($deleted_data)){
                            $ids = [];
                            foreach ($deleted_data as $key => $value) {
                                $tipediskondetail_id = !empty($value['tipediskondetail_id']) ? $value['tipediskondetail_id'] : [];
                                array_push($ids, $tipediskondetail_id);
                            }
                            TipeDiskonDetail::deleteAll(['in', 'tipediskondetail_id', $ids]);
                        }

                    }else{
                        $tipediskon_id = $model->getPrimaryKey();
                        if(is_array($details) && !empty($details)){
                            $rows = [];
                            foreach ($details as $value) {
                                $value['tipediskon_id'] = $tipediskon_id;
                                $rows[] = $value;
                            }
                            TipeDiskonDetail::batchInsert($rows);
                        }
                    }
                    $transaction->commit();
                  return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                } else {
                    $transaction->rollBack();
                    \Yii::error([$model->getErrors()]);
                    
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
                  return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
              } catch (\yii\db\Exception $e) {
                \Yii::error([ $e->getMessage()]);
                Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
              } catch (\Exception $e) {
                \Yii::error([ $e->getMessage()]);
                Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        }
    }

    public function actionGetKelasPelayanan()
    {
        $data = KelasPelayanan::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
        $kelaspelayanan = $this->getOrSetCache(DocoConstants::VAR_C_KP, $result, true);

        return [
            "kelaspelayanan" => $kelaspelayanan
        ];
    }
    
    public function actionGetKelompokTindakan()
    {
        $data = KelompokTindakan::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kelompoktindakan_nama' => SORT_ASC]);
        $kelompoktindakan = $this->getOrSetCache('cache_kelompok_tindakan', $result, true);
        return [
            "kelompoktindakan" => $kelompoktindakan
        ];
    }

    public function actionGetDaftarTindakan()
    {
        $data = DaftarTindakan::find();
        $result = $data->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['daftartindakan_nama' => SORT_ASC]);
        $daftartindakan = $this->getOrSetCache('cache_daftar_tindakan', $result, true);
        return [
            "daftartindakan" => $daftartindakan
        ];
    }
}
