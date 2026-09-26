<?php

namespace app\modules\v1\traits;

use Yii;
use app\components\BedahComponent;
use app\modules\v1\models\PostOperativeAnestesi;
use app\modules\v1\models\PostOperativeAnestesiAldreteScore;
use app\modules\v1\models\PostOperativeAnestesiDrugSupport;
use app\modules\v1\models\PostOperativeAnestesiDrugSupportView;

trait PostOperativeAnestesiTrait
{

    public function actionSavePostOperative()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $post = Yii::$app->request->post();
        if (isset($post['anestesipostopr_id']) && empty($post['anestesipostopr_id'])) {
            unset($post['anestesipostopr_id']);
        }
        $model = PostOperativeAnestesi::find()->where(['pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id']])->one();
        if (empty($model)) {
            $model = new PostOperativeAnestesi();
        }
        $model->attributes = $post;
        try {
            if ($model->save(true)) {
                $deleteCondition = sprintf('anestesipostopr_id = %d', $model->getPrimaryKey());
                $deleteAttributes = [
                    'is_deleted' => true,
                    'is_active' => false
                ];
                $dataInserts = [];
                if (isset($post['drugsupports'])) {
                    $dataKeepKeys = [];
                    foreach ($post['drugsupports'] as $drugsupport) {
                        if (isset($drugsupport['time_delivery']) && isset($drugsupport['dose']) && isset($drugsupport['obatalkes_id'])) {
                            if (isset($drugsupport['anestesipostoprdrugsupport_id']) && !empty($drugsupport['anestesipostoprdrugsupport_id'])) {
                                $dataKeepKeys[] = intval($drugsupport['anestesipostoprdrugsupport_id']);
                            } else {
                                $dataInserts[] = [
                                    'time_delivery' => $drugsupport['time_delivery'],
                                    'obatalkes_id' => $drugsupport['obatalkes_id'],
                                    'dose' => $drugsupport['dose'],
                                    'anestesipostopr_id' => $model->getPrimaryKey(),
                                ];
                            }
                        }
                    }
                    if (count($dataKeepKeys) > 0) {
                        $deleteCondition .= sprintf(' and anestesipostoprdrugsupport_id not in (%s)', implode(', ', $dataKeepKeys));
                    }
                }
                Yii::$app->db->createCommand()
                    ->update('anestesipostoprdrugsupport_t', $deleteAttributes, $deleteCondition)
                    ->execute();
                empty($dataInserts) or PostOperativeAnestesiDrugSupport::batchInsert($dataInserts);

                // reset $dataInserts
                $dataInserts = [];
                $dataUpdateAttributes = [
                    'arrived_id' => [],
                    'score_id' => [],
                ];
                $dataUpdateKeys = [];
                if (isset($post['aldretescores'])) {
                    foreach ($post['aldretescores'] as $aldretescore) {
                        if (isset($aldretescore['arrived_id']) && isset($aldretescore['score_id'])) {
                            if (isset($aldretescore['anestesipostopraldscore_id']) && !empty($aldretescore['anestesipostopraldscore_id'])) {
                                $dataUpdateKeys[] = intval($aldretescore['anestesipostopraldscore_id']);
                                $dataUpdateAttributes['arrived_id'][] = intval($aldretescore['arrived_id']);
                                $dataUpdateAttributes['score_id'][] = intval($aldretescore['score_id']);
                            } else {
                                $dataInserts[] = [
                                    'arrived_id' => $aldretescore['arrived_id'],
                                    'score_id' => $aldretescore['score_id'],
                                    'anestesipostopr_id' => $model->getPrimaryKey(),
                                ];
                            }
                        }
                    }
                }
                empty($dataInserts) or PostOperativeAnestesiAldreteScore::batchInsert($dataInserts);
                if (count($dataUpdateKeys) > 0) {
                    $updateCondition = [
                        'anestesipostopraldscore_id' => $dataUpdateKeys
                    ];

                    $updated = BedahComponent::updateMultiple('anestesipostopraldscore_t', $dataUpdateAttributes, $updateCondition);
                }

                $transaction->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200,
                    'statusCode' => 200,
                    'item' => array_merge($model->attributes, [
                        'aldretescores' => PostOperativeAnestesiAldreteScore::find()
                            ->where(['anestesipostopr_id' => $model->getPrimaryKey()])
                            ->asArray()
                            ->all(),
                        'drugsupports' => PostOperativeAnestesiDrugSupportView::find()
                            ->where(['anestesipostopr_id' => $model->getPrimaryKey()])
                            ->asArray()
                            ->all()
                    ])
                ];
            } else {
                throw new \yii\base\ErrorException(json_encode($model->getErrors()), 500);
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

    public function actionPostOperative()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('id');
        $data = PostOperativeAnestesi::find()->where(compact('pasienmasukpenunjang_id'))
            ->asArray()
            ->one();
        if ($data) {
            $data['aldretescores'] = PostOperativeAnestesiAldreteScore::find()
                ->where(['anestesipostopr_id' => $data['anestesipostopr_id']])
                ->asArray()
                ->all();
            $data['drugsupports'] = PostOperativeAnestesiDrugSupportView::find()
                ->where(['anestesipostopr_id' => $data['anestesipostopr_id']])
                ->asArray()
                ->all();
        }
        return $data;
    }

}
