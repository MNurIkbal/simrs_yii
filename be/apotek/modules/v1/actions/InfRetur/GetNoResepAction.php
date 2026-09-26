<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;

class GetNoResepAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->controller->dataReturResep();
        $result->select(['noresep']);

        if(!empty($post['term'])){
            $term = strtoupper($post['term']);
            $result->andFilterWhere(['like', 'noresep', $term])
                    ->groupBy(['noresep']);
        }

        return $result->asArray()->all();
    }
}