<?php
namespace app\modules\v1\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPermintaanBmhpView;

class DetailHeaderAction extends Action
{
    public function run($pendaftaran_id)
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new InfoPermintaanBmhpView;
            $query = $model::find(true);
            $query->where(["pendaftaran_id" => $pendaftaran_id]);
            return $query->asArray()->one();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}