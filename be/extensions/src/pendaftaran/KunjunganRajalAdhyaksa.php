<?php 

namespace Extensions\pendaftaran; 

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRjDenganBatalView;

class KunjunganRajalAdhyaksa extends \Doco\components\DocoBaseProcessExtension {

    protected function processFlow() {
        $model = new LaporanKunjunganRjDenganBatalView;
        $query = $model::find();
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->getFilterData($query, $advancedFilters);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    private function getFilterData($query, $advancedFilters)
    {
        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }
            
            if(isset($advancedFilters['petugas'])) {
                $term = strtolower($advancedFilters['petugas']);
                $query->andWhere(['like', 'LOWER(pembuat_nama)', $term]);
                $query->orWhere(['like', 'LOWER(pembuat_nama)', $term]);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($advancedFilters['status_periksa_id'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa_id']]);
                unset($_GET['advanced-filter']['status_periksa_id']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);

        return $query;
    }
}

?>