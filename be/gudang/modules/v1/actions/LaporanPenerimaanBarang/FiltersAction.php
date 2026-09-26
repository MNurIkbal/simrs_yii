<?php

namespace app\modules\v1\actions\LaporanPenerimaanBarang;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\Barang;
use app\modules\v1\models\KelompokBarang;

class FiltersAction extends Action
{
    public function run()
    {
        $types = Yii::$app->request->get('types', []);
        if (!is_array($types)) {
            $types = [$types];
        }

        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $additionalPayload = Yii::$app->request->get('additionalPayload', []);
        $limit = Yii::$app->request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $lookupData = [];
        $resultData = [];

        foreach ($types as $eachType) {
            $isInfinityScroll = false;
            $result = null;

            switch ($eachType) {
                case 'ruangan_nama':
                    $result = RuanganView::find()->select(['ruangan_nama as id', 'ruangan_nama as text'])
                        ->andWhere(['is_active' => true]);
                    break;
                case 'nama_barang':
                    $result = Barang::find()->select(['barang_nama as id', 'barang_nama as text'])
                        ->andWhere(['is_deleted' => false, 'is_active' => true]);
                    break;
                case 'kode_barang':
                    $result = Barang::find()->select(['barang_kode as id', 'barang_kode as text'])
                        ->andWhere(['is_deleted' => false, 'is_active' => true]);
                    break;
                case 'kelompok_barang':
                    $result = KelompokBarang::find()->select(['kelompokbarang_nama as id', 'kelompokbarang_nama as text'])
                        ->andWhere(['is_deleted' => false, 'is_active' => true]);
                    break;
            }

            if (!empty($result)) {
                if ($isInfinityScroll) {
                    $result->limit(($limit + 1))->offset($limit * ($page - 1));
                }
                $resultData[$eachType] = $result->asArray()->all();
            } else {
                $resultData[$eachType] = [];
            }
        }

        if (!empty($lookupData)) {
            $lookupData = $this->lookup_type->dataByTypes($lookupData);
        }

        return array_merge($lookupData, $resultData);
    }
}
