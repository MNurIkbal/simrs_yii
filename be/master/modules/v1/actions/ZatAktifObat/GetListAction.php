<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoZatAktifObatView;

class GetListAction extends Action {
    public function run() {
        $model = new InfoZatAktifObatView;
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 1);
        $order = Yii::$app->request->get('order', null);
        $filter = Yii::$app->request->get('advanced-filter', []);
        $instalasi_ruangan = Yii::$app->request->get('instalasi_ruangan', null);

        if (!empty($order)) {
            $explodeOrder = explode(" ", $order);
            $orderKey = $explodeOrder[0];
            $orderType = strtolower($explodeOrder[1]);
        }

        $query = $model->find();

        $query->andWhere(['or', 
            ['is_primary' => true],
            ['jumlah_zataktif' => null]
        ]);

        if(isset($filter['obatalkes_kode'])) {
            $obatalkes_kode = trim($filter['obatalkes_kode']);
            $query->andWhere(['ilike', 'obatalkes_kode', $obatalkes_kode]);
        }

        if(isset($filter['obatalkes_nama'])) {
            $obatalkes_nama = trim($filter['obatalkes_nama']);
            $query->andWhere(['ilike', 'obatalkes_nama', $obatalkes_nama]);
        }

        if(isset($filter['jenisobatalkes']) && $filter['jenisobatalkes'] != '') {
            $query->andWhere(['jenisobatalkes_id' => $filter['jenisobatalkes']]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $totalRecord = $query->count();

        if (isset($orderKey) && isset($orderType)) {
            $query->orderBy([
                $orderKey => $orderType == 'asc' ? SORT_ASC : SORT_DESC,
            ]);
        }
        
        $record = $query->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();
        
        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $record,
        ];
    }
}
