<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-23 11:13:00
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-23 14:41:45
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\models\LaporanMutasiBarangDetailView;
use app\modules\v1\models\LaporanMutasiBarangView;

class LapMutasiBarangController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanMutasiBarangDetailView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }
    public function actionIndex()
    {
    	$model = new LaporanMutasiBarangDetailView;
    	$query = $model::find();        

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
        	// return $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tgl_mutasibarang'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tgl_mutasibarang']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                }
                unset($_GET['advanced-filter']['tgl_mutasibarang']); // Unset Advanced Filter  date range
                $between = true;
            }            
        }        
        $query->andWhere(['between', 'tgl_mutasibarang', $start, $end]);        

    	$query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);   
    }
    public function actionGetNomutasi()
    {    	
    	$request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataNomutasi();        
        $result->select(['nomutasi_barang']);
        $result->groupBy(['nomutasi_barang']);
        if(!empty($post['term'])){          
            $term = $post['term'];                     
            $result->where(['ILIKE','nomutasi_barang',$term]);
        }        
        return $result->asArray()->all();
    }
    public function dataNomutasi(){
        $data = LaporanMutasiBarangDetailView::find();
        return $data;
    }
}
