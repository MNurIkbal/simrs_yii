<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-27 17:35:26
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-11 10:23:12
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\ObatAlkes;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanPemakaianObatAlkesView;
use app\modules\v1\models\InformasiPemakaianObatalkesView;

class LapPemakaianObatalkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPemakaianObatAlkesView';

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
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $model = new InformasiPemakaianObatalkesView;
        $query = $model::find()->where([
            'ruangan_id' => $ruangan_id
        ]); 

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemakaianobat'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglpemakaianobat']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0].' 00:00:00';
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0].' 23:59:59';
                }
                unset($_GET['advanced-filter']['tglpemakaianobat']);
            }

            if(isset($_GET['advanced-filter']['obatalkes_namalain'])){
                $obatalkes_namalain = $_GET['advanced-filter']['obatalkes_namalain'];
                $query->andWhere(['ILIKE', 'obatalkes_namalain', $obatalkes_namalain]);
            }
        }

        $query->andWhere(['between', 'tglpemakaianobat', $start, $end]);    


        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }
    public function actionHapusPemakaian()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = $post['id'];
        $pegawai_id = $post['pegawai_id'];      
        $pemakaianobat_id = $post['pemakaian_id'];
        $obatalkes_id = $post['obatalkes_id'];

        $data = [
            'is_deleted' => true, 
            'deleted_date' => date('Y-m-d H:i:s'), 
            'last_modified_date' => date('Y-m-d H:i:s'), 
            'deleted_by' => $pegawai_id
        ];
        $hapus_detail = Yii::$app->db->createCommand()->update('pemakaianobatdetail_t', $data, 'pemakaianobatdetail_id = '.$id)->execute();     
        
        //itung banyak pemakaian obat alkes detail yg nyisa
        //ketika dia 0 maka is deleted yg di pemakaianobat_t di update jadi true
        $itung_banyakdetail = Yii::$app->db->createCommand('select count(pemakaianobat_id) from pemakaianobatdetail_t where pemakaianobat_id = '.$pemakaianobat_id. ' and is_deleted = false')->queryOne();
        if($itung_banyakdetail['count'] == 0){          
            $hapus_pemakaian = Yii::$app->db->createCommand()->update('pemakaianobat_t', $data, 'pemakaianobat_id = '.$pemakaianobat_id)->execute();            
        }       
        
        return "sakses";
    }

    protected $_title = 'Laporan Pemakaian Obat Alkes';

    public function actionExportExcel()
    {
        $model = new LaporanPemakaianObatAlkesView;
        $query = $model::find(true)->where(['ruangan_id' => $_GET['ruangan_id']]);   

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemakaianobat'])) {
                $date = explode(' - ', $_GET['advanced-filter']['tglpemakaianobat']);  
                if(count($date) == 2) {
                    $startDate = explode('-', $date[0]);
                    $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                    $endDate = explode('-', $date[1]);
                    $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
                }
                unset($_GET['advanced-filter']['tglpemakaianobat']);
            }

            if(isset($_GET['advanced-filter']['nopemakaian_obat'])){
                $nopemakaian_obat = $_GET['advanced-filter']['nopemakaian_obat'];
                $query->andWhere(['ILIKE', 'nopemakaian_obat', $nopemakaian_obat]);
                unset($_GET['advanced-filter']['nopemakaian_obat']);
            }

            if(isset($_GET['advanced-filter']['nama_pegawai'])){
                $nama_pegawai = $_GET['advanced-filter']['nama_pegawai'];
                $query->andWhere(['ILIKE', 'nama_pegawai', $nama_pegawai]);
                unset($_GET['advanced-filter']['nama_pegawai']);
            }
        }
        
        $query->andWhere(['between', 'tglpemakaianobat', $start, $end]); 
        $query->orderBy($_GET['order']);

        $result = [];
        
        foreach ($query->asArray()->all() as $key => $value) {
            // Data Selection
            $qty_besar = $value['jumlah_input'] . ' ' . $value['satuanbesar_nama'];
            $qty_kecil = $value['qty_satuanpakai'] . ' ' . $value['satuankecil_nama'];
            $value['tglpemakaianobat'] = date("d-M-Y", strtotime($value['tglpemakaianobat']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Nomor Pemakaian')] = $value['nopemakaian_obat'];
            $newValue[\Yii::t('app', 'Tanggal Pemakaian')] = $value['tglpemakaianobat'];
            $newValue[\Yii::t('app', 'Nama Obat Alkes')] = $value['obatalkes_nama'];
            $newValue['Qty Pemakaian'] = $qty_besar;
            $newValue['Qty Konversi'] = $qty_kecil;
            $newValue[\Yii::t('app', 'Nama Penginput')] = $value['nama_pegawai'];
            $newValue[\Yii::t('app', 'Keterangan')] = $value['keterangan_pemakaianobat'];
            $result[$key] = $newValue;
        }
                
        // Directory Creation
        $obatalkes = new ObatAlkes;
        $obatalkes_namalain = '';
        if(isset($_GET['advanced-filter']['obatalkes_id'])) {
            $queryObatAlkes = $obatalkes->findOne($_GET['advanced-filter']['obatalkes_id']);
            $obatalkes_namalain = ($queryObatAlkes) ? $queryObatAlkes->obatalkes_namalain : '';
        }

        $header = array(
            Yii::t('app', "Tanggal Pemakaian") => (($start." - ".$end)),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], [], [], true);

        $filePath->save('php://output');
        die;
    }    

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id, $qty_satuanpakai)
    {
        $model = new SatuanKonversi();
        $query = $model->find()->where(['satuanbesar_id' => $satuanbesar_id, 'satuankecil_id' => $satuankecil_id])->one();
        $qty = 0;

        if($query) {
            $qty = $query->nilai_konversi * $qty_satuanpakai;
        }

        return $qty;
    }

    /**
    * @controller actionPrintPdf 
    * @attribute #table_detail# => table 
    **/
    public function actionPrintPdf()
    {
        $model = new LaporanPemakaianObatAlkesView;
        $query = $model::find(true)->where(['ruangan_id' => $_GET['ruangan_id']]);   
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tglpemakaianobat'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemakaianobat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemakaianobat']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['obatalkes_namalain'])){
                $_GET['advanced-filter']['obatalkes_id'] = $_GET['advanced-filter']['obatalkes_namalain'];
                unset($_GET['advanced-filter']['obatalkes_namalain']);
            }
        }        

        $query->andWhere(['between', 'tglpemakaianobat', $start, $end]); 
        $query->orderBy($_GET['order']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $filter = date('d-M-Y',strtotime($start)).' - '.date('d-M-Y',strtotime($end));
        $result = ['data'=>$data, 'filter'=>$filter];
        $print = new DocoPrint();            
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index',$result),
        ];
        $print->Output();
    }

    public function actionDetail($id)
    {
        $query = LaporanPemakaianObatAlkesView::find()->where([
            'pemakaianobat_id' => $id
        ])->all();

        return [
            'data' => $query
        ];
    }
}