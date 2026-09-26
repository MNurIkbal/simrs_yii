<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanDarah;
use app\modules\v1\models\PesanDarahDetail;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RuanganView;
use app\modules\v1\models\InfoPemesananDarahView;
use app\modules\v1\models\InfoPemesananDarahDetailView;
use app\modules\v1\models\InfoPemesananDarahHdView;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class InfPemesananDarahRuanganController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PesanDarah';
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPemesananDarahHdView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if(isset($_GET['advanced-filter'])) {
                $advancedFilters = $_GET['advanced-filter'];
                if(isset($advancedFilters['tgl_pesandarah_awal']) && isset($advancedFilters['tgl_pesandarah_akhir'])) {
                    $start = $advancedFilters['tgl_pesandarah_awal'];
                    $end = $advancedFilters['tgl_pesandarah_akhir'];
                }

                if(isset($advancedFilters['ruangan_nama'])) {
                    $ruangan_nama = $advancedFilters['ruangan_nama'];
                    $query->andWhere(['ruanganpemesan_id' => $ruangan_nama]);
                    unset($_GET['advanced-filter']['ruangan_nama']);
                }
                if(isset($advancedFilters['golongandarah_nama'])) {
                    $golongandarah_nama = $advancedFilters['golongandarah_nama'];
                    $query->andWhere(['golongandarah_id' => $golongandarah_nama]);
                    unset($_GET['advanced-filter']['golongandarah_nama']);
                }
                if(isset($advancedFilters['jenisdarah_nama'])) {
                    $jenisdarah_nama = $advancedFilters['jenisdarah_nama'];
                    $query->andWhere(['jenisdarah_id' => $jenisdarah_nama]);
                    unset($_GET['advanced-filter']['jenisdarah_nama']);
                }
                if(isset($advancedFilters['status_pesan'])) {
                    $status_pesan = $advancedFilters['status_pesan'];
                    $query->andWhere(['status_pesan_id' => $status_pesan]);
                    unset($_GET['advanced-filter']['status_pesan']);
                }
            }
            
            $query->andWhere(['between', 'tgl_pesandarah', $start, $end]);
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPesanDarahPmiView;
        $query = $model::find();
        $title = 'Informasi Pemesanan Darah PMI';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        
        $namaPmi = [];
        $no_pesandarahpmi = [];
        $periode = [];
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_pesandarahpmi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesandarahpmi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            if(isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];
                $query->andWhere(['ILIKE', 'supplier_nama', $supplier_nama]);
                $namaPmi = [
                    Yii::t('app', 'Nama PMI') => $supplier_nama
                ];
            }
            if(isset($advancedFilters['no_pesandarahpmi'])) {
                $no_pesandarahpmi = $advancedFilters['no_pesandarahpmi'];
                $query->andWhere(['ILIKE', 'no_pesandarahpmi', $no_pesandarahpmi]);
                $no_pesandarahpmi = [
                    Yii::t('app', 'Nomor Pemesanan') => $no_pesandarahpmi
                ];
            }
        }

        $periode = [
            Yii::t('app', "Periode") => date('d-M-Y H:i:s', strtotime($start)).' - '.date('d-M-Y H:i:s', strtotime($end))
        ];

        $header = array_merge($periode, $namaPmi, $no_pesandarahpmi);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pemesanan')] = date('d-M-Y', strtotime($value['tgl_pesandarahpmi']));
            $newValue[\Yii::t('app', 'Nomor Pemesanan')] = $value['no_pesandarahpmi'];
            $newValue[\Yii::t('app', 'Nama PMI')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Jumlah Pemesanan')] = is_null($value['qty_pesan']) ? 0 : $value['qty_pesan'];
            $newValue[\Yii::t('app', 'Jumlah Diterima')] = is_null($value['qty_diterima']) ? 0 : $value['qty_diterima'];
            $newValue[\Yii::t('app', 'Sisa')] = is_null($value['qty_sisa']) ? 0 : $value['qty_sisa'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionGetRequest()
    {
        $ruangan = RuanganView::find()->where(['is_active' => true])->all();
        $jenisDarah = JenisDarah::find()->where(['is_active' => true])->asArray()->all();
        $golonganDarah = Lookup::find()->where(['is_active' => true, 'lookup_type' => 'golongan_darah'])->asArray()->all();

        $status_pesan = Lookup::find()
        ->where(['is_active' => true, 'lookup_type' => 'status_pesandarah'])
        ->asArray()->all();

        return [
            'ruangan' => $ruangan,
            'golongan_darah' => $golonganDarah,
            'jenis_darah' => $jenisDarah,
            'status_pesan' => $status_pesan,
        ];
    }
}