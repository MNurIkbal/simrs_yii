<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-20 15:34:12
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-21 10:50:58
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanStokOpnameView;

class LapStokOpnameController extends DocoActiveController
{

	public $modelClass = 'app\modules\v1\models\LaporanStokOpnameView';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
    	$model = new LaporanStokOpnameView;
    	$query = $model::find(true);

    	$between = false;
    	$start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');     
        
    	if(isset($_GET['advanced-filter'])){    		
    		$filter = $_GET['advanced-filter'];
    		if(isset($filter['tglstokopname'])){
    			$date = explode(' - ', $filter['tglstokopname']);    			
    			$dateStart = explode('-', $date[0]);
    			$dateEnd = explode('-', $date[1]);
    			$start = $dateStart[2].'-'.date('m', strtotime($dateStart[1])).'-'.$dateStart[0];
    			$end = $dateEnd[2].'-'.date('m', strtotime($dateEnd[1])).'-'.$dateEnd[0];    			    			
    			$between = true;    			
    		}
    	}
    	if($between){
    		$query->andWhere(['between','tglstokopname',$start,$end]);
    	}

    	$query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);    	
    }

    public function getData(){
    	$data = LaporanStokOpnameView::find();
    	return $data;
    }

    public function actionDataNostokopname()
    {	
    	$request = Yii::$app->request;
    	$post = $request->post();    	
    	$result = $this->getData();
    	$result->select(['nostokopname']);    	
    	if(!empty($post['term'])){
            $term = strtoupper($post['term']);    
            $result->where(['ILIKE','nostokopname',$term]);          
        }        
        return $result->asArray()->all();
    }
}
