<?php

/**
 * @author Randy Vianda Putra
 * @todo Info Stok Opname Barang
 * @copyright 17 April 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoStokOpnameBarangView;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;
use app\modules\v1\models\StokOpnameBarang;
use Doco\components\DocoPrint;

class InfStokOpnameController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoStokOpnameBarangView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new InfoStokOpnameBarangView;
            $query = $model::find(true);
            $query->select([
                'stokopnamebarang_id',
                'tglstokopname',
                'nostokopname',
                'noformulir',
                'totalharga_fisik',
                'totalharga_sistem',
                'selisih',
                'ruangan_id',
                'ruangan_nama',
            ]);
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $tgl_stokopname =  false;
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:00');
    
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglstokopname'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglstokopname']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglstokopname']); // Unset Advanced Filter  date range
                    $tgl_stokopname = true;
                }
            }
            if ($tgl_stokopname) {
                $query->andWhere(['between', 'tglstokopname', $start, $end]);
            }
            /**
             * End Special Condition date range
             **/
    
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function getInfoSo()
    {
    	$data = InfoStokOpnameBarangView::find();
    	return $data;
    }

    public function actionAutocompleteNoFormulir()
    {
    	$request = Yii::$app->request;
    	$post = $request->post();
    	$result = $this->getInfoSo();    	
    	$result->select(['noformulir','noformulir']);
    	if (!empty($post['term'])) {    		
            $term = $post['term'];                     
            $result->where(['ILIKE','LOWER(noformulir)',$term]);
        }
        $result->groupBy(['noformulir']);
        
        return $result->asArray()->all();
    }

    public function actionGetInfoSoDetail()
    {
        try {
            $model = new InfoStokOpnameBarangDetailView;
            $query = $model::find(true);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetInfoSo($id)
    {
        $result = $this->getInfoSo();
        if ($id) {
            $result->where(['=', 'stokopnamebarang_id', $id]);
        }

        return $result->asArray()->one();
    }

    /**
    * @controller actionPrintDetail
    * @attribute #data_barang# => print 

    **/

    public function actionPrintDetail()    
    {
        $id = $_GET['id'];
        $data_detail = $this->getInfoSo();
        if ($id) {
            $data_detail->where(['=', 'stokopnamebarang_id', $id]);
        }

        $data_barang = InfoStokOpnameBarangDetailView::find();
        if ($id) {
            $data_barang->where(['=', 'stokopnamebarang_id', $id]);
        }
        
        $print = new DocoPrint();    
        $print->attributes = [
            '#data_barang#' => $this->renderPartial('index', [
                'data_header' => $data_detail->asArray()->one(),
                'data_barang' => $data_barang->asArray()->all(),
            ])
        ];
        $print->Output();
    }
}