<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\DaftarPaketFisioDetail;
use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\DaftarPaketFisioV;
use Doco\components\DocoRestActiveFilter;

class PaketFisioController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get"] = ["GET"];
        $verbs["update"] = ["POST"];
        $verbs["edit"] = ["GET"];
        $verbs["get-detail"] = ["GET"];
        $verbs["get-sub"] = ["GET"];
        $verbs["save-data"] = ["POST"];
        $verbs["delete-data"] = ["DELETE", "POST"];
        $verbs["update"] = ["PUT"];
        $verbs["get-detail-paket"] = ["GET"];
        $verbs["export-pdf"] = ["GET"];
        $verbs["export-excel"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        $newActions = [
            'get' => 'app\modules\v1\actions\PaketFisio\GetDataAction',
            'update' => 'app\modules\v1\actions\PaketFisio\UpdateDataAction',
            'edit' => 'app\modules\v1\actions\PaketFisio\EditDataAction',
            'get-detail' => 'app\modules\v1\actions\PaketFisio\GetDataDetailAction',
            'get-sub' => 'app\modules\v1\actions\PaketFisio\GetDataSubAction',
            'get-detail-paket' => 'app\modules\v1\actions\PaketFisio\GetDetailPaketAction',
            'save-data' => 'app\modules\v1\actions\PaketFisio\SaveDataAction',
            'delete-data' => 'app\modules\v1\actions\PaketFisio\DeleteDataAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    private function findModel()
    {
        $model = new DaftarPaketFisioV;
        $query = $model::find()
        ->where([DaftarPaketFisioV::tableName().'.is_deleted' => false]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionExportPdf()
    {
        try {
            $query = $this->findModel();
            $model = $query->all();
            if (!empty($model)) {
                $print = new DocoPrint();
                $print->attributes = [
                    '#tgl#' => Docohelpers::convertTo224(date('Y-m-d')),
                    '#table_exportpdf#' => $this->renderPartial('cetak-paket-fisio', [
                        'header' => array(),
                        'model' => $model,
                    ]),
                ];
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        try {
            $data = array();
            $header = $footer = [];
            $header['Kode Paket'] = '-';
            $header['Nama Paket'] = '-';
            $header['Nama Lain Paket'] = '-';
            $header['Status'] = '-';
            $header['Catatan'] = '-';
            if (!is_null($request->get('advanced-filter'))) {
                $advancedFilter = $request->get('advanced-filter');
                if (isset($advancedFilter['daftartindakan_kode'])) {
                    $kodePaket = $advancedFilter['daftartindakan_kode'];
                    $header['Kode Paket'] = $kodePaket;
                }
                if (isset($advancedFilter['daftartindakan_nama'])) {
                    $namaPaket = $advancedFilter['daftartindakan_nama'];
                    $header['Nama Paket'] = $namaPaket;
                }
                if (isset($advancedFilter['daftartindakan_namalainnya'])) {
                    $namaPaketLainnya = $advancedFilter['daftartindakan_namalainnya'];
                    $header['Nama Lain Paket'] = $namaPaketLainnya;
                }
                if (isset($advancedFilter['is_active'])) {
                    $is_active = $advancedFilter['is_active'];
                    $header['Status'] = ($is_active) ? 'Aktif' : 'Tidak Aktif';
                }
                if (isset($advancedFilter['catatan'])) {
                    $catatan = $advancedFilter['catatan'];
                    $header['Catatan'] = $catatan;
                }
            }
            $query = $this->findModel();
            $model = $query->all();
            if (!empty($model)) {
                $counter = 0;
                foreach ($model as $index => $value) {
                    $data[$counter]['kode_paket'] = $value->daftartindakan_kode;
                    $data[$counter]['nama_paket'] = $value->daftartindakan_nama;
                    $data[$counter]['nama_paket_lainnya'] = $value->daftartindakan_namalainnya;
                    $data[$counter]['frekuensi'] = $value->frekuensi;
                    $data[$counter]['jumlah'] = $value->jumlah;
                    $data[$counter]['status'] = $value->is_active ? 'Aktif' : 'Tidak Aktif';
                    $data[$counter]['catatan'] = $value->catatan;
                    $counter++;
                }
            }
            $options = [];
            $filePath = DocoHelpers::exportExcel('Master Paket Fisioterapi', $data, $header, $options, $footer, [], true);
            $filePath->save('php://output');
            die();
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionTest()
    {
        $daftarPaketFisioParentId = 59;
        $model = DaftarPaketFisioDetail::find()
            ->where(['daftarpaketfisio_id' => $daftarPaketFisioParentId])
            ->all();
        foreach ($model as $value) {
            $modelDetail = DaftarPaketFisioDetail::find()
                ->where(['daftarpaketfisiodet_id' => $value->daftarpaketfisiodet_id])
                ->one();
            $modelDetail->delete();
        }
    }
}
