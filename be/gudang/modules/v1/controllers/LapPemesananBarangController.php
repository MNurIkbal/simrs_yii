<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPemesananBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\SatuanKonversi;
use Doco\components\DocoHelpers;

class LapPemesananBarangController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanPemesananBarangView';
    protected $_title = "Laporan Pemesanan Barang ";

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
        $model = new LaporanPemesananBarangView;

        try {
            $query = $model::find(true);
            $between = true;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pesanbarang']);
                    $between = true;
                }

                if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                    $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                    unset($_GET['advanced-filter']['instalasi_tujuan']);
                }

                if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                    $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                    unset($_GET['advanced-filter']['ruangan_tujuan']);
                }

                if (isset($_GET['advanced-filter']['ruangan_pemesan_id'])){
                    $id = $_GET['advanced-filter']['ruangan_pemesan_id'];
                    $query->andWhere(['ruangan_pemesan_id' => $id]);
                }
            }

            if($between) {
                $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);    
            }

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
        $modelPesanan = new PesanBarang;
        $queryPesanan = $modelPesanan::find();

        $queryPesanan = DocoRestActiveFilter::advancedFilter($modelPesanan, $queryPesanan);
        $queryPesanan = new ActiveDataProvider([
            'query' => $queryPesanan,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'pemesanan' => $queryPesanan->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanPemesananBarangView;
        $query = $model::find(true)->where(['ruanganpemesan_id' => $request->get('ruanganpemesan_id')]);
        
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesanbarang']);
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                unset($_GET['advanced-filter']['instalasi_tujuan']);
            }

            if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                unset($_GET['advanced-filter']['ruangan_tujuan']);
            }

            if (isset($_GET['advanced-filter']['ruangan_pemesan_id'])){
                $id = $_GET['advanced-filter']['ruangan_pemesan_id'];
                $query->andWhere(['ruangan_pemesan_id' => $id]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $qty_konversi = $this->actionGetKonversi($value['satuanbesar_id'], $value['satuankecil_id'], $value['qty_pesan']);
            $newValue = [];
            $newValue[\Yii::t('app', 'Nama Barang')] = $value['barang_nama'];
            $newValue[\Yii::t('app', 'Qty Pemesanan')] = $value['qty_pesan'];
            $newValue[\Yii::t('app', 'Satuan Besar')] = $value['satuan_besar'];
            $newValue[\Yii::t('app', 'Qty Konversi')] = $qty_konversi;
            $newValue[\Yii::t('app', 'Satuan Kecil')] = ($value['satuan_kecil']);
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
            Yii::t("app", "Tanggal Pemesanan") => (($start." - ".$end)),
            Yii::t("app", "No. Pemesanan") => isset($_GET['advanced-filter']['no_pemesanan']) ? $_GET['advanced-filter']['no_pemesanan'] : '',
            Yii::t("app", "Instalasi Tujuan") => ($instalasi_nama),
            Yii::t("app", "Ruangan Tujuan") => ($ruangan_nama),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id, $qty_pesan)
    {
        $model = new SatuanKonversi();
        $query = $model->find()->where(['satuanbesar_id' => $satuanbesar_id, 'satuankecil_id' => $satuankecil_id])->one();
        $qty = '';

        if($query) {
            $qty = $query->nilai_konversi * $qty_pesan;
        }

        return $qty;
    }

    public function actionGetRuangan($instalasi_id)
    {
        $model = new Ruangan();
        $query = $model->find()->where(['instalasi_id' => $instalasi_id])->asArray()->all();

        return $query;
    }
}