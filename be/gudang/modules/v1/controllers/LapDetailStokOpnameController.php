<?php

/**
 * @author Randy Vianda Putra
 * @todo Info Kartu Stok Barang
 * @copyright 17 April 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\LaporanStokOpnameBarangDetailView;
use app\modules\v1\models\CetakStokOpnameBarangDetailView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;

class LapDetailStokOpnameController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanStokOpnameBarangDetailView';
    protected $_title = "Laporan Detail Stok Opname Barang";    

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
            $model = new LaporanStokOpnameBarangDetailView;
            $query = $model::find(true);
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $tglstokopname =  false;
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
                    $tglstokopname = true;
                }
            }
            if ($tglstokopname) {
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

    public function getLapDetailSo()
    {
    	$data = LaporanStokOpnameBarangDetailView::find();
    	return $data;
    }

    public function actionDataDetailSo()
    {
    	$request = Yii::$app->request;
    	$post = $request->post();
    	$result = $this->getLapDetailSo();    	
    	$result->select(['nostokopname','nostokopname']);
    	if (!empty($post['term'])) {    		
            $term = $post['term'];                     
            $result->where(['ILIKE','LOWER(nostokopname)',$term]);
        }
        $result->groupBy(['nostokopname']);
        
        return $result->asArray()->all();
    }

    private function dataProvider()
    {
        $model = new LaporanStokOpnameBarangDetailView;

        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglstokopname'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglstokopname']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d  23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglstokopname']);
            }

        }
        $_GET['periode'] = date('d-M-Y',strtotime($start)) .' s/d '. date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tglstokopname', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    /**
    * @controller actionExportPdf
    * @attribute #ruangan# => Untuk Menampilkan Ruangan
    * @attribute #periode# => Untuk Menampilkan periode Laporan Detail Stok opname
    * @attribute #tabel_detail_so# => Untuk Menampilkan Tabel Detail Stok Opname
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $data = $this->dataProvider()->asArray()->all();
        $print = new DocoPrint;
        $ruangan = null;
        if (isset($_GET['advanced-filter']['ruangan_id'])) {
            $modelRuangan = Ruangan::find()->where([
                'ruangan_id' => $_GET['advanced-filter']['ruangan_id']
            ])->one();
            $ruangan = !empty($modelRuangan->ruangan_nama) ? $modelRuangan->ruangan_nama : null;
        }
        $print->attributes = [
            '#periode#' => $request->get('periode'),
            '#tabel_detail_so#' => $this->renderPartial('index',[
                'detail' => $data
            ]),
            '#ruangan#' => $ruangan
        ];
        $print->Output();
    }

    private function cetakExcel()
    {
        $model = new CetakStokOpnameBarangDetailView;

        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        // if(isset($_GET['advanced-filter'])) {
        //     if(isset($_GET['advanced-filter']['tglstokopname'])) {
        //         $explode = explode(" - ", $_GET['advanced-filter']['tglstokopname']);
        //         if(count($explode) == 2) {
        //             $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
        //             $end = date('Y-m-d  23:59:00', strtotime($explode[1]));
        //         }
        //         unset($_GET['advanced-filter']['tglstokopname']);
        //     }

        // }
        $_GET['periode'] = date('d-M-Y',strtotime($start)) .' s/d '. date('d-M-Y',strtotime($end));
        // $query->andWhere(['between', 'Tanngal Stok Opname', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->cetakExcel()->all();

            $ruangan = null;
            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $modelRuangan = Ruangan::find()->where([
                    'ruangan_id' => $_GET['advanced-filter']['ruangan_id']
                ])->one();
                $ruangan = !empty($modelRuangan->ruangan_nama) ? $modelRuangan->ruangan_nama : null;
            }
            $this->_title .= " {$ruangan}";
            // Directory Creation
            $header = array(
                Yii::t('app', "Periode") => $request->get('periode'),
            );

            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}