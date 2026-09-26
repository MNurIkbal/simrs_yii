<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LogPerubahanResep;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LogPerubahanResepR;

class GetDataAction extends Action {
    public function run($id = null, $type = 'reseptur') {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new LogPerubahanResepR;
            $query = $model::find();

            if($id != null && $type == 'reseptur') {
                $query->where(["reseptur_id" => $id]);
            } else {
                $query->where(["penjualanresep_id" => $id]);
            }

            $query->orderBy(["tanggal_perubahan" => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider(['query' => $query]);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
