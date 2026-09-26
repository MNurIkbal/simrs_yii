<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 17:48:35
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-13 15:43:35
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPemesananObatAlkesView;
use app\modules\v1\models\LaporanPemesananObatalkesView;
use app\modules\v1\models\LaporanPemesananObatAlkesHeaderView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoPrint;

class LapPemesananObatalkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPemesananObatalkesView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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
        $model = new LaporanPemesananObatAlkesHeaderView;

        try {
            $query = $model::find(true)->where(['ruanganpemesan_id' => $_GET['ruangan_id']]);
            $between = true;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['ruangan_pemesan_id'])){
                    $id = $_GET['advanced-filter']['ruangan_pemesan_id'];
                    $query->andWhere(['ruangan_pemesan_id' => $id]);
                }

                if (isset($_GET['advanced-filter']['instalasi_tujuan'])){
                    $id = $_GET['advanced-filter']['instalasi_tujuan'];
                    $query->andWhere(['instalasi_id' => $id]);
                    unset($_GET['advanced-filter']['instalasi_tujuan']);
                }

                if (isset($_GET['advanced-filter']['ruangan_tujuan'])){
                    $id = $_GET['advanced-filter']['ruangan_tujuan'];
                    $query->andWhere(['ruangan_id' => $id]);
                    unset($_GET['advanced-filter']['ruangan_tujuan']);
                }
            }

            $query->andWhere(['between', 'tglpemesanan', $start, $end]);    

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected $_title = 'Laporan Pemesanan Obat Alkes';
    public function actionExportExcel()
    {
        $model = new LaporanPemesananObatalkesView;
        try {
            $query = $model::find(true)->where([
                'ruanganpemesan_id' => $_GET['ruangan_id']
            ]);
            $between = true;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $instalasi_nama = '';
            $ruangan_nama = '';

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['nopemesanan'])){
                    $nopemesanan = $_GET['advanced-filter']['nopemesanan'];
                    $query->andWhere(['ILIKE', 'nopemesanan', $nopemesanan]);
                }

                if (isset($_GET['advanced-filter']['instalasi_tujuan'])){
                    $instalasi_tujuan = $_GET['advanced-filter']['instalasi_tujuan'];
                    $query->andWhere(['instalasi_id' => $instalasi_tujuan]);
                    $instalasi = Instalasi::findOne($instalasi_tujuan);
                    $instalasi_nama = $instalasi->instalasi_nama;
                }

                if (isset($_GET['advanced-filter']['ruangan_tujuan'])){
                    $ruangan_tujuan = $_GET['advanced-filter']['ruangan_tujuan'];
                    $query->andWhere(['ruangan_id' => $ruangan_tujuan]);
                    $ruangan = Ruangan::findOne($ruangan_tujuan);
                    $ruangan_nama = $ruangan->ruangan_nama;
                }

                if (isset($_GET['advanced-filter']['ruangan_pemesan_id'])){
                    $id = $_GET['advanced-filter']['ruangan_pemesan_id'];
                    $query->andWhere(['ruangan_pemesan_id' => $id]);
                }
            }

            $query->andWhere(['between', 'tglpemesanan', $start, $end]);
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newValue = [];
                $newValue[\Yii::t('app', 'Tanggal Pemesanan')] = date('Y-m-d', strtotime($value['tglpemesanan'])) ;
                $newValue[\Yii::t('app', 'Nomor Pemesanan')] = $value['nopemesanan'];
                $newValue[\Yii::t('app', 'Instalasi Tujuan')] = $value['instalasi_tujuan'];
                $newValue[\Yii::t('app', 'Ruangan Tujuan')] = $value['ruangan_tujuan'];
                $newValue[\Yii::t('app', 'Nama Obat Alkes')] = $value['obatalkes_namalain'];
                $newValue[\Yii::t('app', 'Qty Pemesanan')] = $value['qty_besar'];
                $newValue[\Yii::t('app', 'Nama Satuan Besar')] = $value['satuan_besar'];
                $newValue[\Yii::t('app', 'Qty  Pemesanan')] = $value['jumlah_pesan'];
                $newValue[\Yii::t('app', 'Nama Satuan Kecil')] = $value['satuan_kecil'];
                $result[$key] = $newValue;
            } 

            // Directory Creation
            $header = array(
                Yii::t('app', "Tanggal Pemesanan") => ((date('Y-m-d', strtotime($start))." - ".date('Y-m-d', strtotime($end)))),
                Yii::t('app', "Nomor Pemesanan") => (@$_GET['advanced-filter']['nopemesanan']),
                Yii::t('app', "Instalasi Tujuan") => $instalasi_nama,
                Yii::t('app', "Ruangan Tujuan") => $ruangan_nama,
            );
            
            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }   

    public function actionPesanBarang()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = $this->dataPesanBarang();
        $result->select(['pesanobatalkes_id','nopemesanan']);
        if(!empty($get['ruangan_pemesan_id'])){
            $term = $get['ruangan_pemesan_id'];
            $result->where(['=','ruangan_pemesan_id',$term]);
        } 
        if(!empty($get['term'])){
            $term = $get['term'];
            $result->andWhere(['ILIKE','nopemesanan',$term]);
        }       
        return $result->asArray()->all();
    }
    
    public function dataPesanBarang()
    {
        $data = InfoPemesananObatAlkesView::find();
        return $data;
    }

    /**
    * @controller actionPrintPdf 
    * @attribute #table_detail# => table 
    **/
    public function actionPrintPdf()
    {
        $model = new LaporanPemesananObatalkesView;
        $query = $model::find(true)->where(['ruangan_pemesan_id' => $_GET['ruangan_id']]);
        $ruangan = Ruangan::findOne($_GET['ruangan_id']);
        $ruangan_nama = ($ruangan) ? $ruangan->ruangan_nama : '';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');        
        if(isset($_GET['advanced-filter'])) {            
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                $between = true;
            }

            if (isset($_GET['advanced-filter']['nopemesanan'])){
                $nopemesanan = $_GET['advanced-filter']['nopemesanan'];
                $query->andWhere(['ILIKE', 'nopemesanan', $nopemesanan]);
            }

            if (isset($_GET['advanced-filter']['instalasi_tujuan'])){
                $instalasi_tujuan = $_GET['advanced-filter']['instalasi_tujuan'];
                $query->andWhere(['instalasi_id' => $instalasi_tujuan]);
            }

            if (isset($_GET['advanced-filter']['ruangan_tujuan'])){
                $ruangan_tujuan = $_GET['advanced-filter']['ruangan_tujuan'];
                $query->andWhere(['ruangan_id' => $ruangan_tujuan]);
            }

            if (isset($advancedFilter['ruangan_pemesan_id'])){
                $id = $advancedFilter['ruangan_pemesan_id'];
                $query->andWhere(['ruangan_pemesan_id' => $id]);
            }
        }

        $query->andWhere(['between', 'tglpemesanan', $start, $end]); 
        $data = $query->asArray()->all();
        $filter = date('Y-m-d', strtotime($start))." - ".date('Y-m-d', strtotime($end));
        $result = ['data'=>$data, 'filter'=>$filter];
        $print = new DocoPrint();            
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index', get_defined_vars()),
        ];
        $print->Output();

    }

    public function actionDetail($id)
    {
        $query = LaporanPemesananObatalkesView::find()->where([
            'pesanobatalkes_id' => $id
        ])->all();

        return [
            'data' => $query
        ];
    }
}
