<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;

class GetNoReturAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->controller->dataReturResep();
        $result->select(['no_returresep']);

        if(!empty($post['term'])){
            $term = strtoupper($post['term']);
            $result->andFilterWhere(['like', 'no_returresep', $term]);
        }

        return $result->asArray()->all();
    }
}