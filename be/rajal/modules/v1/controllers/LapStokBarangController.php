<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanStokBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Barang;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

class LapStokBarangController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanStokBarangView';
    protected $_title = "Laporan Stok Barang ";

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
            $model = new LaporanStokBarangView;
            $query = $model::find()->where(['ruangan_id' => $request->get('ruangan_id', null)]);

            $start = date('Y-m-d');
            $end = date('Y-m-d');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['periodestok_nama']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }

                    $_GET['advanced-filter']['tglperiodestok_awal'] = $start;
                    $_GET['advanced-filter']['tglperiodestok_akhir'] = $end;
                    unset($_GET['advanced-filter']['periodestok_nama']);
                }
                // return $_GET['advanced-filter'];
                if(isset($_GET['advanced-filter']['instalasi_nama'])){
                    $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                    unset($_GET['advanced-filter']['instalasi_nama']);
                }

                if(isset($_GET['advanced-filter']['ruangan_nama'])){
                    $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                    unset($_GET['advanced-filter']['ruangan_nama']);
                }

                if(isset($_GET['advanced-filter']['barang_nama'])){
                    $_GET['advanced-filter']['barang_id'] = $_GET['advanced-filter']['barang_nama'];
                    unset($_GET['advanced-filter']['barang_nama']);
                }
            }
            
            $query->andWhere(['<', 'tglperiodestok_awal', $start]);
            $query->andWhere(['>', 'tglperiodestok_akhir', $start]);
            
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // barang
        $modelBarang = new Barang;
        $queryBarang = $modelBarang::find();

        $queryBarang = DocoRestActiveFilter::advancedFilter($modelBarang, $queryBarang);
        $queryBarang = new ActiveDataProvider([
            'query' => $queryBarang,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'barang' => $queryBarang->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanStokBarangView;
        $query = $model::find(true)->where(['ruangan_id' => $request->get('ruangan_id')]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['periodestok_nama']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                $_GET['advanced-filter']['tglperiodestok_awal'] = $start;
                $_GET['advanced-filter']['tglperiodestok_akhir'] = $end;
                unset($_GET['advanced-filter']['periodestok_nama']);
            }
        }

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Periode Stok')] = $value['periodestok_nama'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Nama Barang')] = $value['barang_nama'];
            $newValue[\Yii::t('app', 'Qty Masuk')] = $value['qty_masuk'];
            $newValue[\Yii::t('app', 'Qty Keluar')] = ($value['qty_keluar']);
            $newValue[\Yii::t('app', 'Qty Dipesan')] = $value['qty_dipesan'];
            $newValue[\Yii::t('app', 'Qty Tersedia')] = $value['qty_tersedia'];
            $newValue[\Yii::t('app', 'Stok')] = $value['qty_stok'];
            $result[$key] = $newValue;
        }

        $header = array(
            Yii::t("app", "Periode Stok") => (($start." - ".$end)),
            Yii::t("app", "Ruangan") => (@$yiiRestfulParams['advanced-filter']['ruangan_nama']),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }
     /**
    * @controller actionCetakPdf
    * @attribute #lapstokbarang# => menampilkan hasil pdf laporan stok barang
    **/
    public function actionCetakPdf()
    {

        $model = new LaporanStokBarangView;
        $query = $model::find(true);


        if(isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
            }
        }


        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = ['data'=>$data];
        $print = new DocoPrint();            
        $print->attributes = [
            '#lapstokbarang#' => $this->renderPartial('index', $result),
        ];
        $print->Output();

        // try {
        //     $model = new LaporanStokBarangView;
        //     $query = $model::find(true);

        //     // manual filter, for unsupported feature in advancedFilter 

        //     $query->orderBy(['ruangan_nama' => SORT_ASC]);
        //     $data = $query->asArray()->all();
        //     $header = [];

        //     $print = new DocoPrint();
        //     $print->attributes = [
        //         '#table#' => $this->renderPartial('index',[
        //             'title'=> $this->_title,
        //             'header'=> $header,
        //             'data' => $data,
        //         ]),
        //     ];
        //     $print->Output();

        //     // return true;
        // }catch(\Exception $e){
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }
}