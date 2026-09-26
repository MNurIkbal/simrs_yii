<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use app\components\DocoHelpers;
use app\modules\v1\models\LogActivityR;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetLogActivityAction extends Action {
    public function run() {
        try {
            $model = new LogActivityR;
            $query = $model::find();
            $query->andWhere(['<>', 'aksi', DocoConstants::LA_AKSI_TAMBAH]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
