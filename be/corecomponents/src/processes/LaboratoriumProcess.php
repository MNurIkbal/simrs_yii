<?php

/**
 * @author : Aris Munandar
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;

use yii\data\ActiveDataProvider;

use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;

use Doco\models\Laboratorium\InfoPasienLabView;

class LaboratoriumProcess extends \Doco\components\DocoBaseProcessExtension
{

    protected function viewProduk() 
    {
            // $request = Yii::$app->request;
            $request = $this->_requestData;
            $model = new InfoPasienLabView;
            $query = $model::find();
            $query->andWhere(['not',['status_penunjang'=>DocoConstants::BTL_APPROVE]]);
            $query->orWhere(['status_penunjang'=>null]);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';
            if (isset($_GET['advanced-filter'])) {
                // return $_GET['advanced-filter'];
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['status_periksa_btn'])) {
                    $status_periksa = $_GET['advanced-filter']['status_periksa_btn'];
                    $query->andWhere(['status_periksa' => $status_periksa]);
                    unset($_GET['advanced-filter']['status_periksa_btn']);
                }

                if (isset($_GET['advanced-filter']['nama_stat'])) {
                    $nama_stat = $_GET['advanced-filter']['nama_stat'];
                    if($nama_stat == 0){
                        $query->andWhere(['status_periksa' => '476']);
                    } elseif($nama_stat == 1){
                        $query->andWhere(['received_flag' => null]);
                    } elseif($nama_stat == 2){
                        $query->andWhere(['received_flag' => 1, 'is_hasil' => false]);
                    } elseif($nama_stat == 3){
                        $query->andWhere(['received_flag' => 1, 'is_hasil' => true, 'is_complete' => false]);
                    } elseif($nama_stat == 4){
                        $query->andWhere(['received_flag' => 1, 'is_hasil' => true, 'is_complete' => true]);
                    }
                    unset($_GET['advanced-filter']['nama_stat']);
                }

                if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
                    $query->andWhere(['or', [ 'ILIKE',  'nama_pasien', $_GET['advanced-filter']['no_rekam_medik']], ['ILIKE', 'no_rekam_medik', $_GET['advanced-filter']['no_rekam_medik']]]);
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            // if ($between) {
            //     $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            // }

            if(!empty($startLahir) && !empty($endLahir) && $between){
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
            /**
             * End Special Condition date range
             **/

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
    }

    protected function processFlow()
    {
        return $this->viewProduk();
    }

}