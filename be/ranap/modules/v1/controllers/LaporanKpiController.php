<?php

namespace app\modules\v1\controllers;

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan KPI
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanPasienriView;
use app\modules\v1\models\LaporanKpiView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LaporanKpiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanKpiView';

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

        $model = new LaporanKpiView;
        $query = $model::find();
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_masukkamar'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_masukkamar']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['Tanggal Masuk']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tgl_masukkamar', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }


    public function actionExportExcel()
    {
        $data = [];
        $model = new LaporanKpiView;
        $request = Yii::$app->request;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_masuk_awal']) && isset($advancedFilters['tgl_masuk_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_masuk_awal'];
            $tgl_akhir = $advancedFilters['tgl_masuk_akhir'];
        }
        $query->andWhere(['between', 'tgl_masukkamar', $tgl_awal, $tgl_akhir]);

        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
  
            $data_baru[$counter]['Tipe Pasien'] = $value['patient_type'];
            $data_baru[$counter]['Tanggal Masuk'] = $value['tgl_masukkamar'];
            $data_baru[$counter]['No Pendaftaran'] = $value['no_pendaftaran'];
            $data_baru[$counter]['Tanggal Admisi'] = $value['tgl_admisi'];
            $data_baru[$counter]['No. Rekam Medik'] = $value['no_rekam_medik'];
            $data_baru[$counter]['Nama Pasien'] = $value['nama_pasien'];
            $data_baru[$counter]['Tanggal Lahir'] = $value['tanggal_lahir'];
            $data_baru[$counter]['Kamar Terakhir'] = $value['kamar_terakhir'];
            $data_baru[$counter]['Jenis Kamar'] = $value['jenis_kamar'];
            $data_baru[$counter]['Ruangan Terakhir'] = $value['ruangan_terakhir'];
            $data_baru[$counter]['Namad DPJP'] = $value['nama_dpjp'];
            $data_baru[$counter]['Tanggal Pasien Pulang'] = $value['tglpasienpulang'];
            $data_baru[$counter]['Kondisi Pulang'] = $value['kondisi_pulang'];
            $data_baru[$counter]['Cara Keluar'] = $value['cara_keluar'];
            $data_baru[$counter]['Nama Penjamin'] = $value['penjamin_nama'];
            $data_baru[$counter]['Tanggal Rencana Pulang'] = $value['tgl_rencanapulang'];
            $data_baru[$counter]['Nomor Tagihan'] = $value['nomor_tagihan'];
            $data_baru[$counter]['Tanggal Tagihan'] = $value['tgl_tagihan'];
            $data_baru[$counter]['Tanggal Bayar'] = $value['tgl_bayar'];
            $data_baru[$counter]['Tanggal Dibersihkan'] = $value['tgl_dibersihkan'];
            $data_baru[$counter]['Jam Tunggu'] = $value['jam_ranap'];
            $data_baru[$counter]['Jam Ranap'] = $value['jam_tunggu'];
            $counter++;
        }
        $header = ['Periode'=> date('d F Y', strtotime($tgl_awal)) . ' - '.date('d F Y', strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel("Laporan KPI", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }


}