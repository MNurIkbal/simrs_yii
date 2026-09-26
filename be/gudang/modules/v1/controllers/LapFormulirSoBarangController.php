<?php

/**
* @author yaya
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanFormSoBarang;
use app\modules\v1\models\Ruangan;

class LapFormulirSoBarangController extends DocoActiveController
{
    public $modelClass = LaporanFormSoBarang::class;
    protected $_title = "Laporan Formulir Stok Opname Barang";

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
        try {
            $request = Yii::$app->request;
            return new ActiveDataProvider([
                'query' => $this->dataProvider(),
            ]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }


    private function dataProvider()
    {
        $model = new LaporanFormSoBarang;

        $query = $model::find();

        $start = date('Y-m-d');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglformulir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglformulir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglformulir']);
            }

        }
        $_GET['periode'] = date('d-M-Y',strtotime($start)) .' s/d '. date('d-M-Y',strtotime($end));
        $query->andWhere(['between', 'tglformulir', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    /**
    * @controller actionExportPdf
    * @attribute #ruangan# => Untuk Menampilkan Ruangan
    * @attribute #periode# => Untuk Menampilkan periode Laporan Formulir Stok opname
    * @attribute #tabel_formulir_so# => Untuk Menampilkan Tabel Formulir Stok Opname
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
            '#tabel_formulir_so#' => $this->renderPartial('index',[
                'detail' => $data
            ]),
            '#ruangan#' => $ruangan
        ];
        $print->Output();
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->dataProvider()->all();
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