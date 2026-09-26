<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:   2018-07-13 11:44:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:47:01
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanVisiteDokterView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapVisiteDokterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanVisiteDokterView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new LaporanVisiteDokterView;
        $query = $model::find();
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['Tanggal Visite'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['Tanggal Visite']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['Tanggal Visite']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['r_medik'])) {
                $r_medik = $_GET['advanced-filter']['r_medik'];
                $query->andWhere(['like', '"No. Rekam Medik"', $r_medik]);
                unset($_GET['advanced-filter']['r_medik']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dpj'])) {
                $dpj = $_GET['advanced-filter']['dpj'];
                $query->andWhere(['like', '"Dokter Penanggung Jawab"', $dpj]);
                unset($_GET['advanced-filter']['dpj']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['pasien'])) {
                $pasien = $_GET['advanced-filter']['pasien'];
                $query->andWhere(['like', 'LOWER("Nama Pasien")', strtolower($pasien)]);
                unset($_GET['advanced-filter']['pasien']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['jv'])) {
                $jv = $_GET['advanced-filter']['jv'];
                $query->andWhere(['like', '"Jenis Visite"', $jv]);
                unset($_GET['advanced-filter']['jv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dokv'])) {
                $dokv = $_GET['advanced-filter']['dokv'];
                $query->andWhere(['dokvisite_id' => $dokv]);
                unset($_GET['advanced-filter']['dokv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Kamar'])) {
                $Kamar = $_GET['advanced-filter']['Kamar'];
                $query->andWhere(['like', '"Kamar"', $Kamar]);
                unset($_GET['advanced-filter']['Kamar']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Ruangan'])) {
                unset($_GET['advanced-filter']['Ruangan']); // Unset Advanced Filter 
            }

        }
        if ($_GET['ruangan_id']) {
            $ruangan_id = $_GET['ruangan_id'];
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        $query->andWhere(['between', '"Tanggal Visite"', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    /**
    * @controller actionExportPdf 
    * @attribute #tanggal_periode_akhir# => nama pasien
    * @attribute #tanggal_periode_awal# => tanggal lahir
    * @attribute #table_detail# => table 
    **/
    public function actionExportPdf()
    {
        $model = new LaporanVisiteDokterView;
        $request = Yii::$app->request;
        $data = [];
        $id_ruangan = $request->get('ruangan_id');
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($advancedFilters['tgl_visite_awal']) && isset($advancedFilters['tgl_visite_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_visite_awal'];
            $tgl_akhir = $advancedFilters['tgl_visite_akhir'];
        }
        $query->andWhere(['between', '"Tanggal Visite"', $tgl_awal, $tgl_akhir]);

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['r_medik'])) {
                $r_medik = $_GET['advanced-filter']['r_medik'];
                $query->andWhere(['like', '"No. Rekam Medik"', $r_medik]);
                unset($_GET['advanced-filter']['r_medik']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dpj'])) {
                $dpj = $_GET['advanced-filter']['dpj'];
                $query->andWhere(['like', '"Dokter Penanggung Jawab"', $dpj]);
                unset($_GET['advanced-filter']['dpj']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['pasien'])) {
                $pasien = $_GET['advanced-filter']['pasien'];
                $query->andWhere(['like', 'LOWER("Nama Pasien")', strtolower($pasien)]);
                unset($_GET['advanced-filter']['pasien']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['jv'])) {
                $jv = $_GET['advanced-filter']['jv'];
                $query->andWhere(['like', '"Jenis Visite"', $jv]);
                unset($_GET['advanced-filter']['jv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dokv'])) {
                $dokv = $_GET['advanced-filter']['dokv'];
                $query->andWhere(['dokvisite_id' => $dokv]);
                unset($_GET['advanced-filter']['dokv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Kamar'])) {
                $Kamar = $_GET['advanced-filter']['Kamar'];
                $query->andWhere(['like', '"Kamar"', $Kamar]);
                unset($_GET['advanced-filter']['Kamar']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Ruangan'])) {
                unset($_GET['advanced-filter']['Ruangan']); // Unset Advanced Filter 
            }
        }
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('print_pdf', ['data' => $data]),
            '#tanggal_periode_akhir#' => $tgl_awal,
            '#tanggal_periode_awal#' => $tgl_akhir,
        ];

        $print->Output();
    }


    public function actionExportExcel()
    {
        $data = [];
        $model = new LaporanVisiteDokterView;
        $request = Yii::$app->request;
        $id_ruangan = $request->get('ruangan_id');
        
        $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_visite_awal']) && isset($advancedFilters['tgl_visite_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_visite_awal'];
            $tgl_akhir = $advancedFilters['tgl_visite_akhir'];
        }
        $query->andWhere(['between', '"Tanggal Visite"', $tgl_awal, $tgl_akhir]);

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['r_medik'])) {
                $r_medik = $_GET['advanced-filter']['r_medik'];
                $query->andWhere(['like', '"No. Rekam Medik"', $r_medik]);
                unset($_GET['advanced-filter']['r_medik']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dpj'])) {
                $dpj = $_GET['advanced-filter']['dpj'];
                $query->andWhere(['like', '"Dokter Penanggung Jawab"', $dpj]);
                unset($_GET['advanced-filter']['dpj']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['pasien'])) {
                $pasien = $_GET['advanced-filter']['pasien'];
                $query->andWhere(['like', 'LOWER("Nama Pasien")', strtolower($pasien)]);
                unset($_GET['advanced-filter']['pasien']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['jv'])) {
                $jv = $_GET['advanced-filter']['jv'];
                $query->andWhere(['like', '"Jenis Visite"', $jv]);
                unset($_GET['advanced-filter']['jv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['dokv'])) {
                $dokv = $_GET['advanced-filter']['dokv'];
                $query->andWhere(['dokvisite_id' => $dokv]);
                unset($_GET['advanced-filter']['dokv']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Kamar'])) {
                $Kamar = $_GET['advanced-filter']['Kamar'];
                $query->andWhere(['like', '"Kamar"', $Kamar]);
                unset($_GET['advanced-filter']['Kamar']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['Ruangan'])) {
                unset($_GET['advanced-filter']['Ruangan']); // Unset Advanced Filter 
            }
        }

        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1=date_create($value['Tanggal Visite']);
            $date2=date_create();
            $diff=date_diff($date1,$date2);
            $dataHariRawat = $diff->d;
            $data_baru[$counter]['Tanggal Admisi'] = $value['Tanggal Admisi'];
            $data_baru[$counter]['Tanggal Visite'] = $value['Tanggal Visite'];
            $data_baru[$counter]['No. Pendaftaran'] = $value['No. Pendaftaran'];
            $data_baru[$counter]['No. Rekam Medik'] = $value['No. Rekam Medik'];
            $data_baru[$counter]['Nama Pasien'] = $value['Nama Pasien'];
            $data_baru[$counter]['Jenis Kelamin'] = $value['Jenis Kelamin'];
            $data_baru[$counter]['Cara Bayar / Penjamin'] = $value['Cara Bayar'].' / '.$value['Penjamin'];
            $data_baru[$counter]['Kasus Penyakit'] = $value['Kasus Penyakit'];
            $data_baru[$counter]['Ruangan - Kamar'] = $value['Ruangan']." / ".$value['Kamar']." - ".$value['Bed'];
            $data_baru[$counter]['Dokter Penanggung Jawab'] = $value['Dokter Penanggung Jawab'];
            $data_baru[$counter]['Jenis Visite'] = $value['Jenis Visite'];
            $data_baru[$counter]['Dokter Visite'] = $value['Dokter Visite'];
            $counter++;
        }
        $header = ['Periode'=> $tgl_awal . ' - '.$tgl_akhir];
        $filePath = DocoHelpers::exportExcel("Laporan Visite Dokter", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

}