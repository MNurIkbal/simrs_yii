<?php
namespace app\modules\v1\actions\InfKartuStok;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoKartuStokObatNewView;
use app\modules\v1\models\ObatAlkes;

class IndexAction extends Action
{
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new InfoKartuStokObatNewView;
            $query = $model::find(true);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $tgl_transaksi = false;
            $obat_alkes = 0;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            Yii::error($_GET['advanced-filter']);
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tanggal_transaksi'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_transaksi']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_transaksi']); // Unset Advanced Filter  date range
                    $tgl_transaksi = true;
                }
                if(isset($_GET['advanced-filter']['tglkadaluarsa'])){
                    $query->andWhere(['=','tglkadaluarsa', $_GET['advanced-filter']['tglkadaluarsa']]);
                    unset($_GET['advanced-filter']['tglkadaluarsa']);
                }

                if(isset($_GET['advanced-filter']['obatalkes_id'])){
                    $obat_alkes = $_GET['advanced-filter']['obatalkes_id'];
                    unset($_GET['advanced-filter']['obatalkes_id']);
                }
            }
            if ($tgl_transaksi) {
                $query->andWhere(['between', 'tanggal_transaksi', $start, $end]);
            }

            $query->andWhere([
                'obatalkes_id' => $obat_alkes
            ]);
            /**
             * End Special Condition date range
            **/
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $query->orderBy([
                'stokobatalkes_id' => SORT_ASC
            ]);

            return new ActiveDataProvider(['query' => $query]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}