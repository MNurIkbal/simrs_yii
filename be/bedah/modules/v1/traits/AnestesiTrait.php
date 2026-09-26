<?php

namespace app\modules\v1\traits;

use Yii;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Anestesi;
use app\modules\v1\models\AnestesiDetail;
use yii\helpers\ArrayHelper;

trait AnestesiTrait
{

    public function actionSaveAnestesi()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $post = Yii::$app->request->post();
        $data = ArrayHelper::getValue($post, 'anestesi', []);
        $dataDetail = ArrayHelper::getValue($post, 'anestesi_detail', []);
        $isUpdate = ArrayHelper::getValue($post, 'is_update');
        $anestesiId = null;
        
        $model = Anestesi::find()
            ->where(['pasienmasukpenunjang_id' => $data['pasienmasukpenunjang_id']])
            ->one();

        if (empty($model)) {
            $model = new Anestesi();
            unset($data['anestesi_id']);
        } else {
            $anestesiId = $model->anestesi_id;
        }
        $model->attributes = $data;
        
        try {
            if ($model->save(true)) {
                if ($isUpdate) {
                    // Delete existing data
                    if ($anestesiId) {
                        $deleteDetail = AnestesiDetail::deleteByAnestesiId($anestesiId);
                    }
                    
                    // When add or delete anestesidetail
                    $anestesiId = $model->anestesi_id;
                    $dataDetail = $this->mapDetailAnestesi($dataDetail, $anestesiId);
                    $saveDetail = AnestesiDetail::batchInsert($dataDetail);
                }
                $transaction->commit();

                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200,
                ];
            } else {
                $transaction->rollBack();
                throw new \yii\base\ErrorException(json_encode($model->getErrors()), 500);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function mapDetailAnestesi($data, $anestesiId)
    {
        $result = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $result[] = [
                    'anestesi_id' => $anestesiId,
                    'obatalkes_id' => $value['obatalkes_id'] != '' ? $value['obatalkes_id'] : null,
                    'dose' => $value['dose'],
                    'time_delivery' => $value['time_delivery'] != '' ? $value['time_delivery'] : null,
                    'created_date' => date('Y-m-d H:i:s'),
                    'is_deleted' => false,
                    'is_active' => true,
                ];
            }
        }

        return $result;
    }
}
