<?php

/**
 * This function will return all of source data for datatable
 * 
 * @param String $type
 * @param String $terms
 * @param Int $page [optional]
 * @return Json
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
*/

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\JenisObatAlkes;

class FiltersAction extends Action {
    public function run() {
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
                case 'jenisobatalkes':
                    $result = JenisObatAlkes::find()->select(['jenisobatalkes_id as id', 'jenisobatalkes_nama as text'])
                        ->andWhere(['is_active' => true]);
                    break;
            }

            if (!empty($result)) {
                if ($isInfinityScroll) {
                    $result->limit(($limit + 1))->offset($limit * ($page - 1));
                }
                $resultData[$eachType] = $result->asArray()->all();
            } else {
                $resultData[$eachType] = [];
            }
        }

        if (!empty($lookupData)) {
            $lookupData = $this->lookup_type->dataByTypes($lookupData);
        }

        return array_merge($lookupData, $resultData);
    }
}
