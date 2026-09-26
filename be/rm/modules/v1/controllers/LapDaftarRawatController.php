<?php
/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanPasienriView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapDaftarRawatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPasienriView';

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

        $model = new LaporanPasienriView;
        $query = $model::find()->where(['IS NOT','pasienpulang_id',null]);
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['Tanggal Keluar'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['Tanggal Keluar']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['Tanggal Keluar']); // Unset Advanced Filter  date range
                $between = true;
            }
            
            if(isset($_GET['advanced-filter']['pendaftaran'])) {
                $pendaftaran = $_GET['advanced-filter']['pendaftaran'];
                $query->andWhere(['like', '"No. Pendaftaran"', $pendaftaran]);
                unset($_GET['advanced-filter']['pendaftaran']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['r_medik'])) {
                $r_medik = $_GET['advanced-filter']['r_medik'];
                $query->andWhere(['like', '"No. Rekam Medik"', $r_medik]);
                unset($_GET['advanced-filter']['r_medik']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['pasien'])) {
                $pasien = $_GET['advanced-filter']['pasien'];
                $query->andWhere(['like', 'LOWER("Nama Pasien")', strtolower($pasien)]);
                unset($_GET['advanced-filter']['pasien']); // Unset Advanced Filter 
            }

            if(isset($_GET['advanced-filter']['jkp'])) {
                $jkp = $_GET['advanced-filter']['jkp'];
                $query->andWhere(['like', '"Jenis Kasus Penyakit"', $jkp]);
                unset($_GET['advanced-filter']['jkp']); // Unset Advanced Filter 
            }
            
        }

        $query->andWhere(['between', '"Tanggal Keluar"', $start, $end]);
        $query->orderBy(['"Tanggal Keluar"'=>SORT_ASC]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    public function actionExportExcel()
    {
        $data = [];
        $model = new LaporanPasienriView;
        $request = Yii::$app->request;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_keluar_awal']) && isset($advancedFilters['tgl_keluar_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_keluar_awal'];
            $tgl_akhir = $advancedFilters['tgl_keluar_akhir'];
        }
        $query->andWhere(['between', '"Tanggal Keluar"', $tgl_awal, $tgl_akhir]);
        $query->orderBy(['"Tanggal Keluar"'=>SORT_ASC]);
        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1=date_create($value['Tanggal Keluar']);
            $data_baru[$counter]['Tanggal Masuk'] = ($value['Tanggal Masuk']) ? date('d F Y H:i:s', strtotime($value['Tanggal Masuk'])) : "" ;
            $data_baru[$counter]['No. Pendaftaran'] = $value['No. Pendaftaran'];
            $data_baru[$counter]['No. Rekam Medik'] = $value['No. Rekam Medik'];
            $data_baru[$counter]['Nama Pasien'] = $value['Nama Pasien'];
            $data_baru[$counter]['Penanggung Jawab'] = $value['penanggungjawab_nama'];
            $data_baru[$counter]['Cara Bayar / Penjamin'] = $value['Cara Bayar'].' / '.$value['Penjamin'];
            $data_baru[$counter]['Kamar'] = $value['kamar'];
            $data_baru[$counter]['TT'] = $value['no_tempattidur'];
            $data_baru[$counter]['Kelas Pelayanan'] = $value['Kelas Pelayanan'];
            $data_baru[$counter]['DPJP'] = $value['Dokter'];
            $data_baru[$counter]['Tanggal Keluar'] = date('d F Y H:i:s', strtotime($value['Tanggal Keluar']));
            $counter++;
        }
        $header = ['Periode'=> date('d F Y', strtotime($tgl_awal)) . ' - '.date('d F Y', strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel("Laporan Pasien Dirawat", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }
}