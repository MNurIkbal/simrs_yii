<?php

namespace app\modules\v1\traits;

use Yii;
use app\modules\v1\models\IntraOperativeAnestesi;
use app\modules\v1\models\IntraOperativeAnestesiMonitor;
use app\modules\v1\models\IntraOperativeAnestesiVitalSign;

trait IntraOperativeAnestesiTrait
{

    public function actionSaveIntraOperative()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $post = Yii::$app->request->post();
        if (isset($post['anestesiintraopr_id']) && empty($post['anestesiintraopr_id'])) {
            unset($post['anestesiintraopr_id']);
        }
        $model = IntraOperativeAnestesi::find()->where(['pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id']])->one();
        if (empty($model)) {
            $model = new IntraOperativeAnestesi();
        }
        $model->attributes = $post;
        try {
            if ($model->save(true)) {
                $vitalSign = null;
                $otherMonitoring = null;
                if (isset($post['vital_sign_individual'])) {
                    if (isset($post['vital_sign_individual']['time'])) {
                        $vitalSign = IntraOperativeAnestesiVitalSign::find()->where([
                            'time' => $post['vital_sign_individual']['time'],
                            'anestesiintraopr_id' => $model->getPrimaryKey(),
                        ])->one();
                    }
                    if (empty($vitalSign)) {
                        $vitalSign = new IntraOperativeAnestesiVitalSign();
                    }
                    $vitalSign->attributes = $post['vital_sign_individual'];
                    $vitalSign->anestesiintraopr_id = $model->getPrimaryKey();
                    $vitalSign->save();
                }
                if (isset($post['other_monitoring_individual'])) {
                    if (isset($post['other_monitoring_individual']['anestesiintraoprmonitor_id'])) {
                        $otherMonitoring = IntraOperativeAnestesiMonitor::find()->where([
                            'time' => $post['other_monitoring_individual']['time'],
                            'anestesiintraopr_id' => $model->getPrimaryKey()
                        ])->one();
                    }
                    if (empty($otherMonitoring)) {
                        $otherMonitoring = new IntraOperativeAnestesiMonitor();
                    }
                    $otherMonitoring->attributes = $post['other_monitoring_individual'];
                    $otherMonitoring->anestesiintraopr_id = $model->getPrimaryKey();
                    $otherMonitoring->save();
                }
                $transaction->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200,
                    'statusCode' => 200,
                    'item' => $model->attributes
                ];
            } else {
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

    public function actionIntraOperative()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('id');
        $data = IntraOperativeAnestesi::find()->where(compact('pasienmasukpenunjang_id'))
            ->asArray()
            ->one();
        if ($data) {
            $data['vital_sign'] = IntraOperativeAnestesiVitalSign::find()
                ->where(['anestesiintraopr_id' => $data['anestesiintraopr_id']])
                ->asArray()
                ->all();
            $data['other_monitoring'] = IntraOperativeAnestesiMonitor::find()
                ->where(['anestesiintraopr_id' => $data['anestesiintraopr_id']])
                ->asArray()
                ->all();
        }
        return $data;
    }

}
