<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapMutasiObatAlkes;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\LaporanMutasiObatView;
use Doco\components\DocoRestActiveFilter;

class GetListDataAction extends Action {
    public function run() {
        try {
            $model = new LaporanMutasiObatView;
            $query = $model::find();

            /*
            $query->orderBy([
                'tgl_pemesanan' => SORT_ASC,
                'no_pemesanan' => SORT_ASC, 
                'nama_obat' => SORT_ASC
            ]);
            */

            if (isset($_GET['advanced-filter'])) {
                $this->controller->filterQuery($_GET['advanced-filter'], $query);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
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
