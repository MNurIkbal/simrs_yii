<?php

namespace app\modules\v1\traits;

use app\modules\v1\models\BeforeLeaving;
use app\modules\v1\models\BeforeLeavingDrug;
use app\modules\v1\models\BeforeLeavingDrugView;
use app\modules\v1\models\PreAnesthetic;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\models\Ruangan;
use Exception;
use yii\helpers\ArrayHelper;

trait BeforeLeavingAnestesiTrait {

    public function actionSaveBeforeLeavingAnestesi()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $post = Yii::$app->request->post();
        if (isset($post['anestesikondisipasien_id']) && empty($post['anestesikondisipasien_id'])) {
            unset($post['anestesikondisipasien_id']);
        }
        $model = BeforeLeaving::find()->where(['pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id']])->one();
        if (empty($model)) {
            $model = new BeforeLeaving();
        }
        $model->attributes = $post;
        try{
            if ($model->save(true)) {
                $deleteCondition = sprintf('anestesikondisipasien_id = %d', $model->getPrimaryKey());
                $deleteAttributes = [
                    'is_deleted' => true,
                    'is_active' => false
                ];
                $dataInserts = [];
                if (isset($post['drugsupports'])) {
                    $dataKeepKeys = [];
                    foreach ($post['drugsupports'] as $drugsupport) {
                        if (isset($drugsupport['time_delivery']) && isset($drugsupport['dose']) && isset($drugsupport['obatalkes_id'])) {
                            if (isset($drugsupport['anestesikondisipasienprdrugsupport_id']) && !empty($drugsupport['anestesikondisipasienprdrugsupport_id'])) {
                                $dataKeepKeys[] = intval($drugsupport['anestesikondisipasienprdrugsupport_id']);
                            } else {
                                $dataInserts[] = [
                                    'time_delivery' => $drugsupport['time_delivery'],
                                    'obatalkes_id' => $drugsupport['obatalkes_id'],
                                    'dose' => $drugsupport['dose'],
                                    'anestesikondisipasien_id' => $model->getPrimaryKey(),
                                ];
                            }
                        }
                    }
                }

                Yii::$app->db->createCommand()
                ->update('anestesikondisipasiendrugsupport_t', $deleteAttributes, $deleteCondition)
                ->execute();
                empty($dataInserts) or BeforeLeavingDrug::batchInsert($dataInserts);

                $transaction->commit();

                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200,
                    'statusCode' => 200,
                    'item' => array_merge($model->attributes, [
                        'drugsupports' => BeforeLeavingDrugView::find()
                            ->where(['anestesikondisipasien_id' => $model->getPrimaryKey()])
                            ->asArray()
                            ->all()
                    ])
                ];
            } else {
                throw new \yii\base\ErrorException(json_encode($model->getErrors()), 400);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionBeforeLeaving()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('id');
        $data = BeforeLeaving::find()->where(compact('pasienmasukpenunjang_id'))
            ->asArray()
            ->one();
        if ($data) {
            $data['drugsupports'] = BeforeLeavingDrugView::find()
                ->where(['anestesikondisipasien_id' => $data['anestesikondisipasien_id']])
                ->asArray()
                ->all();
        }
        return $data;
    }
}
    