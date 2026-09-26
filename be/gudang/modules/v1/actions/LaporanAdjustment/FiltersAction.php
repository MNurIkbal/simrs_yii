<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAdjustment;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\JenisObatAlkes;

class FiltersAction extends Action
{
    public function run()
    {
        $types = Yii::$app->request->get('types', []);
        if (!is_array($types)) {
            $types = [$types];
        }

        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $additionalPayload = Yii::$app->request->get('additionalPayload', []);
        $limit = Yii::$app->request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $lookupData = [];
        $resultData = [];

        foreach ($types as $eachType) {
            $isInfinityScroll = false;
            $result = null;

            switch ($eachType) {
                case 'instalasi_ruangan':
                    $result = RuanganView::find()->select(["ruangan_nama as id", "concat(instalasi_nama, ' - ', ruangan_nama) as text"])
                        ->andWhere(["is_active" => true]);
                    if (isset($additionalPayload['ruangan_id'])) {
                        $result->andWhere(['ruangan_id' => $additionalPayload['ruangan_id']]);
                    }
                    break;
                case 'jenis_adjustment':
                    $result[0]['id'] = 'Masuk';
                    $result[0]['text'] = 'Masuk';
                    $result[1]['id'] = 'Keluar';
                    $result[1]['text'] = 'Keluar';

                    break;
                case 'jenis_obatalkes':
                    $result = JenisObatAlkes::find()->select(['jenisobatalkes_nama as id', 'jenisobatalkes_nama as text'])
                        ->andWhere(['is_deleted' => false, 'is_active' => true]);
                    break;
            }

            if($eachType == 'jenis_adjustment') {
                $resultData[$eachType] = $result;
            } else {
                if (!empty($result)) {
                    if ($isInfinityScroll) {
                        $result->limit(($limit + 1))->offset($limit * ($page - 1));
                    }
                    $resultData[$eachType] = $result->asArray()->all();
                } else {
                    $resultData[$eachType] = [];
                }
            }
        }

        if (!empty($lookupData)) {
            $lookupData = $this->lookup_type->dataByTypes($lookupData);
        }

        return array_merge($lookupData, $resultData);
    }
}
