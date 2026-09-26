<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoReturResepView;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoRestActiveFilter;

class IndexAction extends Action {
    public function run() {
        $model = new InfoReturResepView;
        $query = $model::find();

        $request = Yii::$app->request;
        $get = $request->get();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_retur'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_retur'])){
                $query->andWhere(['=','status_retur', $_GET['advanced-filter']['status_retur']]);
                unset($_GET['advanced-filter']['status_retur']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $query->andWhere(['ruangan_nama' => $_GET['advanced-filter']['ruangan_nama']]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $query->andWhere(['ILIKE','nama_pasien', $_GET['advanced-filter']['nama_pasien']]);
                $query->orWhere(['ILIKE','no_pendaftaran', $_GET['advanced-filter']['nama_pasien']]);
                $query->orWhere(['ILIKE','no_rekam_medik', $_GET['advanced-filter']['nama_pasien']]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }
        }

        $query->andWhere(['between', 'tgl_retur', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}