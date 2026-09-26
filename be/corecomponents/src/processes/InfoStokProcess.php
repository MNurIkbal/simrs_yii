<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KetersediaanObatView;

class InfoStokProcess extends \Doco\components\DocoBaseProcessExtension
{
	protected function processFlow()
    {
    	$model = new KetersediaanObatView;
        $query = $model::find(true);

        $between = false;
        $date = date('Y-m-d');
        $filterNama = '';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
                $filterNama = $_GET['advanced-filter']['obatalkes_nama'];
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruanganId = $_GET['advanced-filter']['ruangan_id'];
                $listRuangan = explode(",", $ruanganId);
                $query->andWhere(['IN', 'ruangan_id', $listRuangan]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }   
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}