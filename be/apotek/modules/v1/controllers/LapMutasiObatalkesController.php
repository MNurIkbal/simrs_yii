<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-01 15:03:38
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-21 11:29:37
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoMutasiObatalkesView;
use app\modules\v1\models\DetailMutasiObatAlkesView;
use app\modules\v1\models\LaporanMutasiObatalkesView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoPrint;

class LapMutasiObatalkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoMutasiObatalkesView';

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
    //index view info mutasi obat alkes
    public function actionIndex()
    {
        $model = new LaporanMutasiObatalkesView;
        $query = $model::find(true);

        // date today as default
        $between = true;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');        
        if(isset($_GET['advanced-filter'])) {      
            // return $_GET['advanced-filter'];      
            if(isset($_GET['advanced-filter']['tglmutasioa'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglmutasioa']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                }                
                unset($_GET['advanced-filter']['tglmutasioa']); // Unset Advanced Filter  date range
                $between = true;                
            }

            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere('ruangan_asal_id = '.$id);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere('ruangan_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }

        $query->andWhere(['between','tglmutasioa',$start,$end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }
    public function actionDataNomutasi2(){
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataNomutasi();
        $result->select(['mutasiobatruangan_id','nomutasioa']);     
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);    
            $result->where(['like', 'nomutasioa', $term]);            
        }        
        return $result->asArray()->all();
    }
    public function getDataNomutasi(){
        $model = InfoMutasiObatalkesView::find();
        return $model;
    }

    protected $_title = 'laporan mutasi obat alkes';
    public function actionExportExcel()
    {
        $model = new LaporanMutasiObatalkesView;
        $query = $model::find(true);
  

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi_nama = '';
        if(isset($_GET['advanced-filter'])) {            
            
            if(isset($_GET['advanced-filter']['tglmutasioa'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglmutasioa']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                }                
                unset($_GET['advanced-filter']['tglmutasioa']); // Unset Advanced Filter  date range
                $between = true;                
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $id = $_GET['advanced-filter']['instalasi_nama'];
                $instalasi = Yii::$app->db->createCommand('select instalasi_nama from instalasi_m where instalasi_id = '.$id)->queryOne();
                $instalasi_nama = $instalasi['instalasi_nama'];
                $query->andWhere('ruangan_asal_id = '.$id);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere('ruangan_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            // if(isset($_GET['advanced-filter']['instalasi_nama'])){
            //     $id = $_GET['advanced-filter']['instalasi_nama'];      
            //     $instalasi_nama = Yii::$app->db->createCommand('select instalasi_nama from instalasi_m where instalasi_id = '.$id)->queryOne();
            //     $query->andWhere('instalasi_tujuan_id = '.$id);
            //     unset($_GET['advanced-filter']['instalasi_nama']);
            // }
        }
        
        $query->andWhere(['between','tglmutasioa',$start,$end]);
        $query->orderBy($_GET['order']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ]
        ]);

        $result = [];
        
        foreach ($dataProvider->getModels() as $key => $value) {
            // Data Selection
            $value['tglmutasioa'] = date("j F Y", strtotime($value['tglmutasioa']));

            $newValue = [];
            $newValue[\Yii::t('app', 'Nomor Mutasi')] = $value['nomutasioa'];
            $newValue[\Yii::t('app', 'Tanggal Mutasi')] = $value['tglmutasioa'];
            $newValue[\Yii::t('app', 'Nama Obat Alkes')] = $value['obatalkes_namalain'];
            $newValue[\Yii::t('app', 'Qty')] = $value['qty_satuan_besar'];
            $newValue[\Yii::t('app', 'Satuan Besar')] = $value['satuanbesar_nama'];
            $newValue[\Yii::t('app', 'Qty')] = $value['jumlah_mutasi'];
            $newValue[\Yii::t('app', 'Satuan Kecil')] = $value['satuankecil_nama'];
            // $newValue[\Yii::t('app', 'instalasi tujuan')] = $value['instalasi_nama'];
            // $newValue[\Yii::t('app', 'ruangan tujuan')] = $value['ruangan_nama'];
            // $newValue[\Yii::t('app', 'status')] = $value['statusmutasi'];            
            $result[$key] = $newValue;
        }                
        // Directory Creation
        $header = array(
            Yii::t('app', "Tanggal Mutasi Obat Alkes") => (($start." - ".$end)),                        
            // Yii::t('app', "nomor mutasi obat alkes") => (@$_GET['advanced-filter']['obatalkes_namalain']),            
            // Yii::t('app', "instalasi tujuan") => $instalasi_nama,            
            // Yii::t('app', "ruangan tujuan") => (@$_GET['advanced-filter']['ruangan_nama']),            
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }    

    /**
    * @controller actionPrintPdf 
    * @attribute #table_detail# => table 
    **/
    public function actionPrintPdf()
    {
        $model = new LaporanMutasiObatalkesView;
        $query = $model::find(true);
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');        
        if(isset($_GET['advanced-filter'])) {            
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tglmutasioa'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmutasioa']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmutasioa']);
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere('ruangan_asal_id = '.$id);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere('ruangan_tujuan_id = '.$id);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }        

        $query->andWhere(['between', 'tglmutasioa', $start, $end]); 
        $query->orderBy($_GET['order']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $filter = $start.' - '.$end;
        $result = ['data'=>$data, 'filter'=>$filter];
        $print = new DocoPrint();            
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index',$result),
        ];
        $print->Output();

    }
}
