<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-20 14:36:23
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\LaporanPermintaanMakanView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class LaporanPermintaanMakanController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\LaporanPermintaanMakanView';

    /**
     * @todo Verbs function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan list data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPermintaanMakan()
    {
        return Yii::$app->docoPlugin->execute('get_data_permintaan_makan');
    }

    public function countData($data)
    {
        $count_jenis = [];
        $count_makanan = [];
        $data_count = [];
        $data_count['jenisdiet_nama'] = [];
        $data_count['makanandiet_nama'] = [];
        $data_count['jumlah'] = 0;

        foreach ($data as $key => $value ) {
            $data_count['jenisdiet_nama'][] = $value['jenisdiet_id'];
            $data_count['makanandiet_nama'][] = $value['makanandiet_id'];
            $data_count['jumlah'] = $data_count['jumlah'] + $value['jumlah'];
        }

        if (!empty($data_count['jenisdiet_nama'])) {
            $count_jenis = array_count_values($data_count['jenisdiet_nama']);
        }

        if (!empty($data_count['makanandiet_nama'])) {
            $count_makanan = array_count_values($data_count['makanandiet_nama']);
        }

        return [
            'count_jenis' => count($count_jenis),
            'count_makanan' => count($count_makanan),
            'count_jumlah' => $data_count['jumlah'],
        ];
    }
    /**
     * @todo Fungsi untuk mendapatkan data count
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataCount()
    {
        $count_jenis = [];
        $count_makanan = [];
        $data_count = [];
        $data_count['jenisdiet_nama'] = [];
        $data_count['makanandiet_nama'] = [];
        $data_count['jumlah'] = 0;
        $model = LaporanPermintaanMakanView::find()->all();

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                $data_count['jenisdiet_nama'][] = $value['jenisdiet_id'];
                $data_count['makanandiet_nama'][] = $value['makanandiet_id'];
                $data_count['jumlah'] = $data_count['jumlah'] + $value['jumlah'];
            }
        }

        if (!empty($data_count['jenisdiet_nama'])) {
            $count_jenis = array_count_values($data_count['jenisdiet_nama']);
        }

        if (!empty($data_count['makanandiet_nama'])) {
            $count_makanan = array_count_values($data_count['makanandiet_nama']);
        }

        return [
            'count_jenis' => count($count_jenis),
            'count_makanan' => count($count_makanan),
            'count_jumlah' => $data_count['jumlah'],
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #periode# => periode
    * @attribute #pj_ruangan# => pj_ruangan
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #waktu_dicetak# => waktu_dicetak
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        try {
            $model = new LaporanPermintaanMakanView;
            $query = $model::find();
            $start = $end = date('Y-m-d');
            if (isset($_GET['ruangan_id']) && $_GET['ruangan_id'] != '') {
                $pegawai = PegawaiView::find()->where(['ruangan_id' => $_GET['ruangan_id'], 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();
            } else {
                $pegawai = [];
            }

            if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_permintaanmakan']);
            } else {
                $now = date('Y-m-d', strtotime('NOW'));

                $query->andWhere(['tgl_permintaanmakan' => $now]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $temp_jenis = [];
            $temp_makanan = [];
            $temp_diagnosa = [];
            $temp_alergi = [];
            $count_jenis = 0;
            $count_makanan = 0;
            $count_jumlah = 0;
            $count_diagnosa = 0;
            $count_alergi = 0;
            for ($i = count($query) - 1; $i >= 0; $i--) {
                if (!in_array($query[$i]["jenisdiet_id"], $temp_jenis)) {
                    $temp_jenis[] = $query[$i]["jenisdiet_id"];
                    $count_jenis = $count_jenis + 1;
                }

                if (!in_array($query[$i]["makanandiet_id"], $temp_makanan)) {
                    $temp_makanan[] = $query[$i]["makanandiet_id"];
                    $count_makanan = $count_makanan + 1;
                }

                if (!in_array($query[$i]["diagnosa"], $temp_diagnosa)) {
                    $temp_diagnosa[] = $query[$i]["diagnosa"];
                    $count_diagnosa = $count_diagnosa + 1;
                }

                if (!in_array($query[$i]["riwayat_alergi"], $temp_alergi)) {
                    if($query[$i]["riwayat_alergi"] != null){
                        $temp_alergi[] = $query[$i]["riwayat_alergi"];
                        $count_alergi = $count_alergi + 1;
                    }
                }

                $count_jumlah = $count_jumlah + $query[$i]["jumlah"];
            }

            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $query[$key]['tgl_permintaanmakan'] = DocoHelpers::convDateTime($value['tgl_permintaanmakan'], false, true);
                }
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#periode#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($start)), false, false).' - '.DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($end)), false, false),
                '#pj_ruangan#' => isset($pegawai['nama_pegawai']) ? $pegawai['nama_pegawai'] : '',
                '#nama_pegawai#' => isset($_GET['nama_pegawai']) ? $_GET['nama_pegawai'] : '',
                '#waktu_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, true),
                '#table#' => $this->renderPartial('pdf', [
                    'data' => $query,
                    'count_jenis' => $count_jenis,
                    'count_makanan' => $count_makanan,
                    'count_jumlah' => $count_jumlah,
                    'count_diagnosa' => $count_diagnosa,
                    'count_alergi' => $count_alergi,
                ]),
            ];
            $print->Output();
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->docoPlugin->execute('excel_data_permintaan_makan');
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }
}