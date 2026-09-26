<?php
/**
* @author yaya
**/
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoStokBarangDetail;
use app\modules\v1\businessLogic\FormulirStokOpname;

class FormulirStokOpnameController extends DocoActiveController
{
    public $modelClass = InfoStokBarangDetail::class;

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
        $query = $this->dataProvider();
        return new ActiveDataProvider([
            'query' => $query,
        ]);

    }

    private function dataProvider()
    {
        $model = new InfoStokBarangDetail;
        $query = $model::find();

        $between = false;
        $start = $end = date('Y-m-d');

        if(isset($_GET['advanced-filter']['periodestokbarang_id'])) {
            $explode = explode(" - ", $_GET['advanced-filter']['periodestokbarang_id']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['periodestokbarang_id']);
        }
        $_GET['periode_stok'] = date('d-M-Y',strtotime($start)) . ' s/d ' . date('d-M-Y',strtotime($end));
        $query->andWhere(['between','tglperiodestok_awal',$start,$end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionSave()
    {
        $dataDetail = $this->dataProvider()->asArray()->all();
        $result = FormulirStokOpname::excecute($dataDetail);
        return $result;
    }

    /**
    * @controller actionPrintFormulirStokOpname
    * @attribute #priode_stok# => Untuk Menampilkan Data Periode stok
    * @attribute  #nomor_formulir# => Untuk Menampiilkan Nomor formulir stok opname
    * @attribute #table_formulir# => Untuk menampilkan tabel formulir
    * @attribute #ruangan# => Untuk menapilkan ruangan
    **/
    public function actionPrintFormulirStokOpname()
    {
        $request = Yii::$app->request;
        $print = new DocoPrint;
        $print->attributes = [
            '#priode_stok#' => $request->post('periode'),
            '#nomor_formulir#' => $request->post('no_formulir'),
            '#table_formulir#' => $this->renderPartial('index',[
                'detail' => $request->post('data')
            ]),
            '#ruangan#' => $request->post('ruangan_nama')
        ];
        $print->Output();
    }
}