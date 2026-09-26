<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-02 14:52:56
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-20 15:56:59
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\LaporanStokObatalkesView;
use app\modules\v1\models\InfoStokObatalkesView;
use app\modules\v1\models\ObatAlkes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LapStokObatalkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanStokObatalkesView';

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
        $model = new LaporanStokObatalkesView;
        $query = $model::find(true);

        $periodestok_id = 0;

        if(isset($_GET['advanced-filter']['periodestok_nama'])) {
            $periodestok_id = $_GET['advanced-filter']['periodestok_nama'];
            unset($_GET['advanced-filter']['periodestok_nama']);
        }
        if(isset($_GET['advanced-filter']['ruangan_nama'])) {
            $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
            unset($_GET['advanced-filter']['ruangan_nama']);
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }
        $query->andWhere(['periodestok_id' => $periodestok_id]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionDataObat(){
        $request = Yii::$app->request;
        $post = $request->get();
        $result = $this->dataObat();    	
        $result->select(['obatalkes_id','obatalkes_namalain']);
        if(!empty($post['ruangan_id'])){    		
            $term = $post['ruangan_id'];                     
            $result->where(['=','ruangan_id',$term]);
        }        
        return $result->asArray()->all();
    }
    public function dataObat(){
        $data = InfoStokObatAlkesView::find();
        return $data;
    }

    /**
    * @controller actionExportPdf 
    * @attribute #periode_tahun# => periode tahun
    * @attribute #table_detail# => table 
    * @attribute #nama_ruangan# => ruangan nama 
    **/
    public function actionExportPdf()
    {
        $model = new LaporanStokObatalkesView;
        $request = Yii::$app->request;
        $data = [];
        $id_ruangan = $request->get('ruangan_id');
        $nama_ruangan = $request->get('ruangan_nama');
        $advancedFilters = $request->get('advanced-filter', []);
        $periodestok_id = 0;
        
        $query = $model::find();
        
        if(isset($_GET['advanced-filter']['periodestok_nama'])) {
            $periodestok_id = $_GET['advanced-filter']['periodestok_nama'];
            unset($_GET['advanced-filter']['periodestok_nama']);
        }
        if(isset($_GET['advanced-filter']['ruangan_nama'])) {
            $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
            unset($_GET['advanced-filter']['ruangan_nama']);
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }
        $query->andWhere(['periodestok_id' => $periodestok_id]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('print_pdf', ['data' => $data]),
            '#periode_tahun#' => ($data[0]['periodestok_nama']) ? $data[0]['periodestok_nama'] : "" ,
            '#nama_ruangan#' => $nama_ruangan,
        ];

        $print->Output();
    }

    protected $_title = 'laporan stok dan ketersediaan obat alkes';
    public function actionExportExcel()
    {
        $data = [];
        $model = new LaporanStokObatalkesView;
        $request = Yii::$app->request;
        $id_ruangan = $request->get('ruangan_id');
        $advancedFilters = $request->get('advanced-filter', []);
        $periodestok_id = 0;

        $query = $model::find();
        
        if(isset($_GET['advanced-filter']['periodestok_nama'])) {
            $periodestok_id = $_GET['advanced-filter']['periodestok_nama'];
            unset($_GET['advanced-filter']['periodestok_nama']);
        }
        if(isset($_GET['advanced-filter']['ruangan_nama'])) {
            $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
            unset($_GET['advanced-filter']['ruangan_nama']);
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }
        $query->andWhere(['periodestok_id' => $periodestok_id]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $data_baru[$counter]['Periode Stok'] = $value['periodestok_nama'];
            $data_baru[$counter]['Ruangan'] = $value['ruangan_nama'];
            $data_baru[$counter]['Nama Obat Alkes'] = $value['obatalkes_namalain'];
            $data_baru[$counter]['Qty Masuk'] = $value['qty_masuk'];
            $data_baru[$counter]['Qty Keluar'] = $value['qty_keluar'];
            $data_baru[$counter]['Qty Dipesan'] = $value['qty_dipesan'];
            $data_baru[$counter]['Qty Tersedia'] = $value['qty_tersedia'];
            $data_baru[$counter]['Stok'] = $value['qty_stok'];
            $counter++;
        }
        $header = ['Periode'=> ($data[0]['periodestok_nama']) ? $data[0]['periodestok_nama'] : "" ];

        $filePath = DocoHelpers::exportExcel("Laporan Stok Obat Alkes", $data_baru, $header, [],[],[],true);

        $filePath->save('php://output');
        die;

        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }
    
    public function actionDataObatAlkesNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $idRuangan = $post['idR'];
        $query_ruangan = "";
        if ($idRuangan) {
            $query_ruangan = " and ruangan_id=".$idRuangan;
        }
        $term = strtoupper($post['term']);

        // $sql = "select obatalkes_id, obatalkes_namalain 
        //     from laporanstokobatalkes_v 
        //     where UPPER( obatalkes_namalain ) LIKE '%{$term}%' {$query_ruangan} 
        //     group by obatalkes_id, obatalkes_namalain
        //     order by obatalkes_id asc limit 50
        // "; 
        $sql = "select obatalkes_id, obatalkes_namalain 
            from obatalkes_m 
            where UPPER( obatalkes_namalain ) LIKE '%{$term}%'
            group by obatalkes_id, obatalkes_namalain
            order by obatalkes_id asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }
}