<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 18:14:50
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 13:41:23
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanReturResepView;
use app\modules\v1\models\DetailReturResepView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LapReturController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanReturResepView';

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

    public function actionIndex(){
    	$model = new LaporanReturResepView;
        $query = $model::find();

    	$request = Yii::$app->request;
    	$get = $request->get();        
    	
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

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

            if (isset($_GET['advanced-filter']['no_returresep'])) {
                $query->andWhere(['no_returresep' => $_GET['advanced-filter']['no_returresep']]);
                unset($_GET['advanced-filter']['no_returresep']); // Unset Advanced Filter  no_returresep
            }

            if (isset($_GET['advanced-filter']['noresep'])) {
                $query->andWhere(['noresep' => $_GET['advanced-filter']['noresep']]);
                unset($_GET['advanced-filter']['noresep']); // Unset Advanced Filter  noresep
            }
        }
        if($between) {
            $query->andWhere(['between', 'tgl_retur', $start, $end]);    
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionGetDetail($id){    	
    	$result = $this->detailReturResep($id);
    	return $result->asArray()->all();
    }
    public function actionGetDetailPasien($id){
        $result = $this->detailReturResep($id);
        return $result->asArray()->one();
    }
    public function actionGetNoRetur()
    {
        $request = Yii::$app->request;
        $post = $request->post();        
        $result = $this->dataReturResep();     
        $result->select(['no_returresep']);   
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);    
            $result->andFilterWhere(['like', 'no_returresep', $term]);
            // $result->where('no_returresep LIKE "%:term%"',[':term'=>$term]);
        }        
        return $result->asArray()->all();
    }
    public function actionGetNoResep()
    {
        $request = Yii::$app->request;
        $post = $request->post();        
        $result = $this->dataReturResep();     
        $result->select(['noresep']);   
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);    
            $result->andFilterWhere(['like', 'noresep', $term]);
            // $result->where('no_returresep LIKE "%:term%"',[':term'=>$term]);
        }        
        return $result->asArray()->all();
    }

    public function dataReturResep(){
    	$data = LaporanReturResepView::find()->select([
							    		'returresep_id',
							    		'tgl_retur',
							    		'no_returresep',
							    		'nama_pasien',
							    		'noresep',
							    		'carabayar_nama',
							    		'penjamin_nama',
							    	]);
    	return $data;
    }    
    public function detailReturResep($id){
    	$data = DetailReturResepView::find()
    								->select([
    									'nama_pasien',
    									'tgl_retur',
    									'carabayar_nama',
    									'penjamin_nama',
    									'no_returresep',
    									'noresep',
                                        'obatalkes_nama',
    									'qty_retur',
    									'hargasatuan',
    									'total',
    								])
    								->where('returresep_id = :id',[':id'=>$id]);
    	return $data;

    }
    protected $_title = 'laporan retur';
    public function actionExportExcel()
    {

        $model = new LaporanReturResepView;
        $query = $model::find();

    	$request = Yii::$app->request;
    	$get = $request->get();        
    	
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

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

            if (isset($_GET['advanced-filter']['no_returresep'])) {
                $query->andWhere(['no_returresep' => $_GET['advanced-filter']['no_returresep']]);
                unset($_GET['advanced-filter']['no_returresep']); // Unset Advanced Filter  no_returresep
            }

            if (isset($_GET['advanced-filter']['noresep'])) {
                $query->andWhere(['noresep' => $_GET['advanced-filter']['noresep']]);
                unset($_GET['advanced-filter']['noresep']); // Unset Advanced Filter  noresep
            }
        }
        if($between) {
            $query->andWhere(['between', 'tgl_retur', $start, $end]);    
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = $query->asArray()->all();                

        $header = array(
            Yii::t('app', "Tanggal retur") => (($start." - ".$end)),
            Yii::t('app', "Nomor retur") => (@$_GET['advanced-filter']['no_returresep']),
            Yii::t('app', "Nomor resep") => (@$_GET['advanced-filter']['noresep']),
        );
        
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }  


    /**
    * @controller actionExportPdf 
    * @attribute #table_report# => table 

    **/
    public function actionExportPdf()
    {
        $model = new LaporanReturResepView;
        $query = $model::find();
        
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');  

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

            if (isset($_GET['advanced-filter']['no_returresep'])) {
                $query->andWhere(['no_returresep' => $_GET['advanced-filter']['no_returresep']]);
                unset($_GET['advanced-filter']['no_returresep']); // Unset Advanced Filter  no_returresep
            }

            if (isset($_GET['advanced-filter']['noresep'])) {
                $query->andWhere(['noresep' => $_GET['advanced-filter']['noresep']]);
                unset($_GET['advanced-filter']['noresep']); // Unset Advanced Filter  noresep
            }
        }
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);        

        $result = $query->asArray()->all();
        // Directory Creation
        $header = array(
            Yii::t('app', "Tanggal retur") => (($start." - ".$end)),                        
            Yii::t('app', "Nomor retur") => (@$_GET['advanced-filter']['no_returresep']),            
            Yii::t('app', "Nomor resep") => (@$_GET['advanced-filter']['noresep']),                                
        );
        $res = ['filter'=>$header, 'detail'=>$result];               
        $print = new DocoPrint();            
        $print->attributes = [
            '#table_report#' => $this->renderPartial('index',['filter'=>$header, 'detail'=>$result]),
        ];
        $print->Output();
    }  

}