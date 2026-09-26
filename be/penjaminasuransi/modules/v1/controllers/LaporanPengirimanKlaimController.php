<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\LaporanPengirimanKlaimView;
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class LaporanPengirimanKlaimController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPengirimanKlaimView';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionGetData()
    {
        try {
            return new ActiveDataProvider([
                'query' => $this->actionDataKlaim()
            ]);
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionSummary()
    {
        try {
            $data = $this->actionDataKlaim()->select([
                'total_tarifrs',
                'group_tarif'
            ])->asArray()->all();

            $tagihanRs = 0;
            $tarifKlaim = 0;
            if (! empty($data)) {
                foreach ($data as $value) {
                    if (isset($value['total_tarifrs'])) {
                        $tagihanRs += $value['total_tarifrs'];
                    }

                    if (isset($value['group_tarif'])) {
                        $tarifKlaim += $value['group_tarif'];
                    }
                }
            }

            return [
                'status' => 200,
                'tagihanRs' => $tagihanRs,
                'tarifKlaim' => $tarifKlaim
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionExportExcel()
    {
        try {
            $title = 'Laporan Pengiriman Klaim';
            $data = $this->actionDataKlaim()->asArray()->all();
            $newData = [];

            foreach ($data as $value) {
                $newValue['Nosep'] = $value['nosep'];
                $newValue['Tanggal Pendaftaran'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $newValue['Tanggal Pulang'] = date('d M Y', strtotime($value['tgl_pulang']));
                $newValue['Tanggal Klaim'] = date('d M Y', strtotime($value['tgl_klaim']));
                $newValue['No Pendaftaran'] = $value['no_pendaftaran'];
                $newValue['Nama Pasien'] = $value['nama_pasien'];
                $newValue['INACBGS Group'] = $value['group_nama'];
                $newValue['Spesial Group'] = '';
                $newValue['Spesial Group'] = '';
                if (isset($value['additional_data'])) {
                    $additional_data = json_decode($value['additional_data'], true);
                    $prosedur = ArrayHelper::getValue($additional_data, 'pros_code');
                    $proc = ArrayHelper::getValue($additional_data, 'proc_code');
                    $investigasi = ArrayHelper::getValue($additional_data, 'inv_code');
                    $drug = ArrayHelper::getValue($additional_data, 'drug_code');

                    if (isset($prosedur)) {
                        $newValue['Spesial Group'] .= $prosedur . ',';
                    }

                    if (isset($proc)) {
                        $newValue['Spesial Group'] .= $proc . ',';
                    }

                    if (isset($investigasi)) {
                        $newValue['Spesial Group'] .= $investigasi . ',';
                    }

                    if (isset($drug)) {
                        $newValue['Spesial Group'] .= $drug;
                    }
                }

                $newValue['Tarif Klaim'] = $value['group_tarif'];
                $newValue['Tagihan RS'] = $value['total_tarifrs'];
                $newValue['Status Data Klaim'] = $value['is_terkirim'] ? 'Sudah Terkirim' : 'Belum Terkirim';
                $newData[] = $newValue;
            }

            $filePath = DocoHelpers::exportExcel($title, $newData, [], [], [], [], true);
            $filePath->save('php://output');
            die;
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

    private function actionDataKlaim()
    {
        $model = new LaporanPengirimanKlaimView();
        $query = $model::find();
        
        if (isset($_GET['advanced-filter'])) {
            unset($_GET['advanced-filter']['tgl_masukpulang']);

            $tanggalPendaftaran = isset($_GET['advanced-filter']['tgl_pendaftaran']) ? $_GET['advanced-filter']['tgl_pendaftaran'] : '';
            $tanggalPulang = isset($_GET['advanced-filter']['tgl_pulang']) ? $_GET['advanced-filter']['tgl_pulang'] : '';
            $tanggalKlaim = isset($_GET['advanced-filter']['tgl_klaim']) ? $_GET['advanced-filter']['tgl_klaim'] : '';

            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $tanggal = $_GET['advanced-filter']['tgl_pendaftaran'];
                if ($tanggal !== '') {
                    $query->andWhere(['between',  new \yii\db\Expression('(tgl_pendaftaran::date)'), $tanggal, $tanggal]);
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                }
            }

            if (isset($_GET['advanced-filter']['tgl_pulang'])) {
                $tanggal = $_GET['advanced-filter']['tgl_pulang'];
                if ($tanggal !== '') {
                    $query->andWhere(['between', new \yii\db\Expression('(tgl_pulang::date)'), $tanggal, $tanggal]);
                    unset($_GET['advanced-filter']['tgl_pulang']);
                }
            }

            if (isset($_GET['advanced-filter']['tgl_klaim'])) {
                $tanggal = $_GET['advanced-filter']['tgl_klaim'];
                if ($tanggal !== '') {
                    $query->andWhere(['between', new \yii\db\Expression('(tgl_klaim::date)'), $tanggal, $tanggal]);
                    unset($_GET['advanced-filter']['tgl_klaim']);
                }
            }

            if ($tanggalPendaftaran !== '' || $tanggalPulang !== '' || $tanggalKlaim !== '') {
                $query = DocoRestActiveFilter::advancedFilter($model, $query);
                return $query;
            } else {
                return $query->where('1=0');
            }
        } else {
            return $query->where('1=0');
        }
    }
}
