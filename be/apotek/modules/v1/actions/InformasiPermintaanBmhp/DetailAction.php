<?php
namespace app\modules\v1\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPermintaanBmhpDetailView;

class DetailAction extends Action
{
    public function run($pendaftaran_id)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new InfoPermintaanBmhpDetailView;
            $query = $model::find(true);
            $query->where(["pendaftaran_id" => $pendaftaran_id]);
            $query->orderBy(["status_bmhp" => SORT_ASC, "tgl_permintaan" => SORT_DESC]);

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
