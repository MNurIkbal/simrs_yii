<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanFormulirStokOpnameView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\FormulirStokOpname;
use Doco\components\DocoHelpers;

class LapFormulirStokOpnameController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanFormulirStokOpnameView';
    protected $_title = "Laporan Formulir Stok Opname ";

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
            $model = new LaporanFormulirStokOpnameView;
            $query = $model::find()->where(['ruangan_id' => $request->get('ruangan_id', null)]);

            $start = date('Y-m-d');
            $end = date('Y-m-d');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tglformulir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglformulir']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglformulir']);
                }
                if(isset($_GET['advanced-filter']['instalasi_nama'])){
                    $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                    unset($_GET['advanced-filter']['instalasi_nama']);
                }

                if(isset($_GET['advanced-filter']['ruangan_nama'])){
                    $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                    unset($_GET['advanced-filter']['ruangan_nama']);
                }
            }
            
            $query->andWhere(['between', 'tglformulir', $start, $end]);
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

        // formulir
        $modelFormulir = new FormulirStokOpname;
        $queryFormulir = $modelFormulir::find();

        $queryFormulir = DocoRestActiveFilter::advancedFilter($modelFormulir, $queryFormulir);
        $queryFormulir = new ActiveDataProvider([
            'query' => $queryFormulir,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'formulir' => $queryFormulir->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanFormulirStokOpnameView;
        $query = $model::find(true)->where(['ruangan_id' => $request->get('ruangan_id')]);
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglformulir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglformulir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglformulir']);
            }

            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Formulir')] = $value['tglformulir'];
            $newValue[\Yii::t('app', 'Periode Stok')] = $value['tglperiodestok_awal'].' - '.$value['tglperiodestok_akhir'];
            $newValue[\Yii::t('app', 'Nomor Formulir')] = $value['noformulir'];
            $newValue[\Yii::t('app', 'Harga Netto Sistem')] = number_format($value['harganetto_sistem'], 0);
            $result[$key] = $newValue;
        }

        $instalasi = new Instalasi;
        $instalasi_nama = '';
        $ruangan_nama = '';

        if(isset($_GET['advanced-filter']['instalasi_id'])) {
            $queryInstalasi = $instalasi->findOne($_GET['advanced-filter']['instalasi_id']);
            $instalasi_nama = ($queryInstalasi) ? $queryInstalasi->instalasi_nama : '';
        }

        $ruangan = new Ruangan;
        if(isset($_GET['advanced-filter']['ruangan_id'])) {
            $queryRuangan = $ruangan->findOne($_GET['advanced-filter']['ruangan_id']);
            $ruangan_nama = ($queryRuangan) ? $queryRuangan->ruangan_nama : '';
        }
        
        $header = array(
            Yii::t("app", "Tanggal Formulir") => (($start." - ".$end)),
            Yii::t("app", "Nomor Formulir") => isset($_GET['advanced-filter']['noformulir']) ? $_GET['advanced-filter']['noformulir'] : '',
            Yii::t("app", "Instalasi") => ($instalasi_nama),
            Yii::t("app", "Ruangan") => ($ruangan_nama),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }
}