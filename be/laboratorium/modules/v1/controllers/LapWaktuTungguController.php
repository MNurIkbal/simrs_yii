<?php

/**
 * @author Randy Vianda Putra
 * @todo Laporan Waktu Tunggu Lab
 * @copyright 01 Agustus 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\LaporanWaktuTungguLabView;

class LapWaktuTungguController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanWaktuTungguLabView';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanWaktuTungguLabView;
            $query = $model::find();

            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }   

            }
            // if ($between) {
                $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            // }
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

    /**
     * @controller actionExportPdf
     * @attribute #table_exportpdf# => table 
     * @attribute #tgl_awal_bulan# => tanggal awal bulan
     * @attribute #tgl_akhir_bulan# => tanggal Akhir bulan
     **/
    public function actionExportPdf()
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $ruangan_nama = '';
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
            }
            if (isset($_GET['advanced-filter']['ruangan'])) {
                $ruangan_nama = $_GET['advanced-filter']['ruangan'];
            }
        }
        try {
            $request = Yii::$app->request;
            $model = new LaporanWaktuTungguLabView;
            $query = $model::find();
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($model)) {
                $header = array();
                $print = new DocoPrint();
                $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'tgl_awal_bulan'=> $start,
                        'tgl_akhir_bulan'=> $end,
                        'ruangan_nama'=> $ruangan_nama,
                        'header' => $header,
                        'model' => $query,
                    ]),
                ];
                $print->Output();
            }
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
        $title = "Laporan Waktu Tunggu Pemeriksaan Laboratorium";
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $header = array();
        $ruangan_nama = '';
        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']);
                $periode = date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end));
                $header['Tanggal Pendaftaran'] = $periode;
            }
            if (isset($advancedFilter['no_pendaftaran'])) {
                    $no_pendaftaran = $advancedFilter['no_pendaftaran'];
                    $header['No Pendaftaran'] = $no_pendaftaran;
                }

            if (isset($advancedFilter['no_rekam_medik'])) {
                $no_rekam_medik = $advancedFilter['no_rekam_medik'];
                $header['No Rekam Medis'] = $no_rekam_medik;
            }

            if (isset($advancedFilter['nama_pasien'])) {
                $nama_pasien = $advancedFilter['nama_pasien'];
                $header['Nama Pasien'] = $nama_pasien;
            }

            if (isset($advancedFilter['dokter'])) {
                $dokter = $advancedFilter['dokter'];
                $header['Dokter'] = $dokter;
            }

            if (isset($advancedFilter['nama_sample'])) {
                $nama_sample = $advancedFilter['nama_sample'];
                $header['Specimen'] = $nama_sample;
            }

            if (isset($advancedFilter['pemeriksaanlab_nama'])) {
                $pemeriksaanlab_nama = $advancedFilter['pemeriksaanlab_nama'];
                $header['Pemeriksaan'] = $pemeriksaanlab_nama;
            }
        }
        try {
            $data = array();
            $request = Yii::$app->request;
            $model = new LaporanWaktuTungguLabView;
            $query = $model::find();
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;
                $temp = [];
                $interval_sample = $interval_daftar = [];
                foreach ($query as $index => $value) {
                    $temp[$value['no_rekam_medik']] = $value['no_rekam_medik'];
                    $tgl_sample = $value['tgl_ambilsample'] . ' ' . $value->jam_ambilsample;
                    $diffPendaftaran = DocoHelpers::getLamaTunggu($value->tglmasukpenunjang, $value->tgl_expertise);
                    $diffSample = DocoHelpers::getLamaTunggu($tgl_sample, $value->tgl_expertise);
                    $interval_sample[] = DocoHelpers::timeToSeconds($diffSample);
                    $interval_daftar[] = DocoHelpers::timeToSeconds($diffPendaftaran);
                    $data[$counter]['no_pendaftaran'] = $value->no_pendaftaran;
                    $data[$counter]['no_rekam_medik'] = $value->no_rekam_medik;
                    $data[$counter]['nama_pasien'] = $value->nama_pasien;
                    $data[$counter]['dokter'] = $value->dokter;
                    $data[$counter]['specimen'] = $value->nama_sample;
                    $data[$counter]['pemeriksaan'] = $value->pemeriksaanlab_nama;
                    $data[$counter]['tanggal_pendaftaran'] = date('d M Y H:i:s', strtotime($value->tglmasukpenunjang));
                    $data[$counter]['tanggal_specimen'] = date('d M Y H:i:s', strtotime($tgl_sample));
                    $data[$counter]['tanggal_hasil_pemeriksaan'] = date('d M Y H:i:s', strtotime($value->tgl_hasilpemeriksaanlab));
                    $data[$counter]['tanggal_expertise'] = date('d M Y H:i:s', strtotime($value->tgl_expertise));
                    $data[$counter]['waktu_tunggu (Specimen - Expertise)'] = $diffSample;
                    $data[$counter]['waktu_tunggu (Pendaftaran - Expertise)'] = $diffPendaftaran;

                    $counter++;
                }
                $find_average_second_sample = DocoHelpers::getAverage($interval_sample);
                $find_average_second_daftar = DocoHelpers::getAverage($interval_daftar);
                $average_lama_sample = DocoHelpers::secondsToTime($find_average_second_sample);
                $average_lama_daftar = DocoHelpers::secondsToTime($find_average_second_daftar);
            }
            $footer = [
                'title'=>['Jumlah Pasien', 3],
                'data'=>[
                    'Dokter' => count($temp),
                    'Tanggal expertise' => 'Rata-rata waktu tunggu',
                    'Waktu tunggu (Specimen - Expertise)' => $average_lama_sample,
                    'Waktu tunggu (Pendaftaran - Expertise)' => $average_lama_daftar,
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, array(
                "uploadPath" => "./uploads",
                "subTitle" => $ruangan_nama,
            ), $footer,[],true);
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

}