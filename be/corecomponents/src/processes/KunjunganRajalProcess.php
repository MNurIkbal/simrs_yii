<?php 

namespace Doco\processes; 

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;

class KunjunganRajalProcess extends \Doco\components\DocoBaseProcessExtension {

    protected function processFlow() {
        $model = new LaporanKunjunganRawatJalanView;
        $query = $model::find();
        $request = Yii::$app->request;
        $is_executive = $request->get('is_executive', false);
        if ($is_executive) {
            $query->andWhere(['is_executive' => true]);
        }
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->getFilterData($query, $advancedFilters);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getFilterData($query, $advancedFilters)
    {
        if(!empty($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = date('Y-m-d',strtotime($advancedFilters['tgl_pendaftaran_awal']));
                $tgl_akhir = date('Y-m-d',strtotime($advancedFilters['tgl_pendaftaran_akhir']));
            }
            
            if(isset($advancedFilters['petugas'])) {
                $term = strtolower($advancedFilters['petugas']);
                $query->andWhere(['ILIKE', 'LOWER(pembuat_nama)', $term]);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa']]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($advancedFilters['ruangan_id']) && is_array(explode(',', $advancedFilters['ruangan_id'])) ){
                $query->andWhere(['in', 'ruangan_id', explode(',', $advancedFilters['ruangan_id'])]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }
        }

        $query->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $tgl_awal, $tgl_akhir]);

        return $query;
    }
}

?>