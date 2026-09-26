<?php
namespace app\modules\v1\actions\InformasiPermintaanBmhp;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPermintaanBmhpView;

class IndexAction extends Action
{
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new InfoPermintaanBmhpView;
            $query = $model::find(true);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter']['tgl_permintaan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_permintaan']);
                $between = true;
            }

            $query->andWhere(['between', 'tgl_permintaan', $start, $end]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider(['query' => $query]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}