<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizal
 * @Date:   2018-11-21 23:50:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\Ruangan;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfPasienRanapController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRanap';
    protected $_title = 'Informasi Pasien Rawat Inap';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new InfoPasienGiziView;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_admisi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_admisi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_admisi', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_admisi']); // Unset Advanced Filter  date range
            }

            if (isset($_GET['advanced-filter']['status_gizi'])) {
                if ($_GET['advanced-filter']['status_gizi'] == '1') {
                    $query->andWhere('(skor < 2 OR skor is null)');
                } else {
                    $query->andWhere(['>=', 'skor', '2']);
                }
                unset($_GET['advanced-filter']['status_gizi']); // Unset Advanced Filter status gizi
            }

            if (isset($_GET['advanced-filter']['status_ranap'])) {
                $status_ranap = $_GET['advanced-filter']['status_ranap'];
                $status_ranap = array_map('intval', explode(',', $status_ranap));
                $query->andWhere(['in', 'status_ranap', $status_ranap]);
                unset($_GET['advanced-filter']['status_ranap']); // Unset Advanced Filter status ranap
            }
        }
        /**
         * End Special Condition date range
        **/


        // restactivefilter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return $query->createCommand()->getRawSql();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf 
    * @attribute #periode# => periode
    * @attribute #ruangan_nama# => nama ruangan 
    * @attribute #table# => list data 
    **/
    public function actionExportPdf()
    {
        $periode = 'Semua Tanggal';
        $ruangan_nama = 'Semua Ruangan';
        $request = Yii::$app->request;

        $model = new InfoPasienGiziView;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_admisi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_admisi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    $query->andWhere(['between', 'tgl_admisi', $start, $end]);
                    
                    $startFormat = date('d M Y', strtotime($start));
                    $endFormat = date('d M Y', strtotime($end));
                    $periode = $startFormat . ' - ' . $endFormat;
                }
                unset($_GET['advanced-filter']['tgl_admisi']); // Unset Advanced Filter  date range
            }

            if (isset($_GET['advanced-filter']['status_gizi'])) {
                if ($_GET['advanced-filter']['status_gizi'] == '1') {
                    $query->andWhere('(skor < 2 OR skor is null)');
                } else {
                    $query->andWhere(['>=', 'skor', '2']);
                }
                unset($_GET['advanced-filter']['status_gizi']); // Unset Advanced Filter status gizi
            }

            if (isset($_GET['advanced-filter']['status_ranap'])) {
                $status_ranap = $_GET['advanced-filter']['status_ranap'];
                $status_ranap = array_map('intval', explode(',', $status_ranap));
                $query->andWhere(['in', 'status_ranap', $status_ranap]);
                unset($_GET['advanced-filter']['status_ranap']); // Unset Advanced Filter status ranap
            }

            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
                $ruangan = Ruangan::findOne($ruangan_id);
                $ruangan_nama = $ruangan->ruangan_nama;
            }
        }
        /**
         * End Special Condition date range
        **/

        // restactivefilter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => $periode ,
            '#ruangan_nama#' => $ruangan_nama,
            '#table#' => $this->renderPartial('print_pdf', ['data' => $data]),
        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $model = new InfoPasienGiziView;
        $query = $model::find();
        $periode = 'Semua Tanggal';
        $ruangan_nama = 'Semua Ruangan';
       /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_admisi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_admisi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    $query->andWhere(['between', 'tgl_admisi', $start, $end]);
                    
                    $startFormat = date('d M Y', strtotime($start));
                    $endFormat = date('d M Y', strtotime($end));
                    $periode = $startFormat . ' - ' . $endFormat;
                }
                unset($_GET['advanced-filter']['tgl_admisi']); // Unset Advanced Filter  date range
            }

            if (isset($_GET['advanced-filter']['status_gizi'])) {
                if ($_GET['advanced-filter']['status_gizi'] == '1') {
                    $query->andWhere('(skor < 2 OR skor is null)');
                } else {
                    $query->andWhere(['>=', 'skor', '2']);
                }
                unset($_GET['advanced-filter']['status_gizi']); // Unset Advanced Filter status gizi
            }

            if (isset($_GET['advanced-filter']['status_ranap'])) {
                $status_ranap = $_GET['advanced-filter']['status_ranap'];
                $status_ranap = array_map('intval', explode(',', $status_ranap));
                $query->andWhere(['in', 'status_ranap', $status_ranap]);
                unset($_GET['advanced-filter']['status_ranap']); // Unset Advanced Filter status ranap
            }

            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
                $ruangan = Ruangan::findOne($ruangan_id);
                $ruangan_nama = $ruangan->ruangan_nama;
            }
        }
        /**
         * End Special Condition date range
        **/

        // restactivefilter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        $result = [];
        
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Masuk')] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
            $newValue[\Yii::t('app', 'Info Pasien')] = $value['no_rekam_medik'] . ' - ' . $value['no_pendaftaran'] . ' - ' . $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Jenis Kelamin')] = $value['jenis_kelamin'];
            $newValue[\Yii::t('app', 'Tanggal Lahir')] = $value['tanggal_lahir'];
            $newValue[\Yii::t('app', 'Diagnosa')] = $value['diagnosa_nama'] ? json_decode($value['diagnosa_nama'])->text : '';
            $newValue[\Yii::t('app', 'Jenis Diet')] = $value['jenisdiet_nama'];
            $newValue[\Yii::t('app', 'Dokter DPJP')] = $value['dokter_admisi'];
            $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Hak Kelas')] = $value['hak_kelas'];
            $newValue[\Yii::t('app', 'Kelas Saat Ini')] = $value['kelas_pelayanan'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Kamar')] = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
            $newValue[\Yii::t('app', 'Skor Asesmen Gizi')] = $value['skor'];
            $newValue[\Yii::t('app', 'Status Rawat Inap')] = $value['stat_ranap'];
            $newValue[\Yii::t('app', 'Status Asesmen Gizi')] = $value['stat_asesmen_gizi'];
            $result[$key] = $newValue;
        }
        // Directory Creation

        $header = array(
            Yii::t('app', "Periode") => $periode,
            Yii::t('app', "Ruangan") => $ruangan_nama,
        );
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }
}
