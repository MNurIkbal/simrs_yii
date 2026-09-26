<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\gudang;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KetersediaanObatApprovalView;

class InfoStokApproval extends \Doco\processes\InfoStokProcess
{
	protected function processFlow()
    {
    	$model = new KetersediaanObatApprovalView;
        $query = $model::find(true);

        $between = false;
        $date = date('Y-m-d');
        $filterNama = '';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                $filterNama = $_GET['advanced-filter']['obatalkes_nama'];
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}