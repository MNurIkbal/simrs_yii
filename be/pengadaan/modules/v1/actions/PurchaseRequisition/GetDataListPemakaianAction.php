<?php

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\historyPemakaianObatOutFn;
use yii\db\Expression;

class GetDataListPemakaianAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $data_pemakaian = $request->get('data_pemakaian');
        $data_pemakaian = $data_pemakaian." Day";

        $model = new historyPemakaianObatOutFn(['extParam' => [$data_pemakaian, (int)$id]]);
        $query = $model->find()->select([
            'obatalkes_id', 'tanggal_transaksi', 'ruangan_nama', 'qtystok_out', 'keterangan', 'satuanunit_nama'
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
