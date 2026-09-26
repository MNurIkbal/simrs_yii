<?php

namespace app\modules\v1\controllers;

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien konsul
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanPasienriView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienKonsul;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;

class LaporanPasienKonsulController extends DocoActiveController
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

    public function actionIndex($id)
    {
        $request = Yii::$app->request;
        $model = new PasienKonsul;
        $query = $this->GetData($id);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $betweenKonsul = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $ex = explode("-",$start);
        $baru = $ex[1]-01;
        $string = "Y-".$baru."-d 00:00:00";
        $startKonsul = date($string);
        $endKonsul = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
              
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
            if(isset($_GET['advanced-filter']['tgl_konsulpoli'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_konsulpoli']);
               
                if(count($explode) == 2) {
                    $startKonsul = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endKonsul = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_konsulpoli']); // Unset Advanced Filter  date range
                $betweenKonsul = true;
            }
            

            
        }
    
        $query->andWhere(['between', 'tgl_konsulpoli', $startKonsul, $endKonsul]);

        if (!empty($start) && !empty($end) && $between) {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    /**
    * @controller actionExportPdf 
    * @attribute #tanggal_periode_akhir# => sebagai tanggal periode akhir
    * @attribute #tanggal_periode_awal# => sebagai tanggal periode awal
    * @attribute #table_detail# => sebagai table 
    * @attribute #tanggal# => untuk menampilkan tanggal sekarang
    * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
    * @attribute #cetak_oleh# => untuk menampilkan pencetak
    * @attribute #kepala# => untuk menampilkan nama kepala ruangan
    * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
    * @attribute #ruangan# => untuk menampilkan ruangan
    **/
    public function actionExportPdf()
    {
        $model = new PasienKonsul;
        $request = Yii::$app->request;
        $id = $request->get('instalasi_id');
        $data = [];
        $advancedFilters = $request->get('advanced-filter', []);
        $query = $this->GetData($id);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $explodeStart = explode("-",$start);
        $monthAwal = $explodeStart[1]-01;
        $resultStart = "Y-".$monthAwal."-d 00:00:00";
        $tgl_awal = date($resultStart);
        $tgl_akhir = date('Y-m-d 23:59:59');
   
        if (isset($advancedFilters['tgl_masuk_awal']) && isset($advancedFilters['tgl_masuk_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_masuk_awal'];
            $tgl_akhir = $advancedFilters['tgl_masuk_akhir'];
        }
        $query->andWhere(['between', 'tgl_konsulpoli', $tgl_awal, $tgl_akhir]);
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $periodeKonsul = date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir));
        $periodePendaftaran = date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end));
        $print = new DocoPrint();
        $print->attributes = [
            '#datatable#' => $this->renderPartial('print_pdf', ['data' => $data]),
            '#pegawai_cetak#' => Yii::$app->jwt->user->nama_pemakai,
            '#tanggal#' => date('d F Y'),
            '#periodeKonsul#' => $periodeKonsul,
            '#periodePendaftaran#' => $periodePendaftaran,
            '#tanggal_cetak#' => date('d F Y H:i:s'),
        ];

        $print->Output();
    }


    public function actionExportExcel()
    {
        $data = [];
        $model = new PasienKonsul;
        $request = Yii::$app->request;
        $id = $request->get('instalasi_id');
        $query = $this->GetData($id);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $explodeStart = explode("-",$start);
        $monthAwal = $explodeStart[1]-01;
        $resultStart = "Y-".$monthAwal."-d 00:00:00";
        // modify advanced filters
        $request =  Yii::$app->request;
        $tgl_awal = date($resultStart);
        $tgl_akhir = date('Y-m-d 23:59:59');

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_masuk_awal']) && isset($advancedFilters['tgl_masuk_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_masuk_awal'];
            $tgl_akhir = $advancedFilters['tgl_masuk_akhir'];
        }
        $query->andWhere(['between', 'tgl_konsulpoli', $tgl_awal, $tgl_akhir]);

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
        }

        $data = $query->asArray()->all();
        $data_baru = [];
        $counter = 0;
        foreach ($data as $key => $value) {
            // $data_baru[$counter]['No'] = $value['rowNum'];
            $data_baru[$counter]['Tanggal Pendfaftaran'] = date('d/m/Y H:i:s',strtotime($value['tgl_pendaftaran']));
            $data_baru[$counter]['No Pendaftaran'] = $value['no_pendaftaran'];
            $data_baru[$counter]['No Rekam Medik'] = $value['no_rekam_medik'];
            $data_baru[$counter]['Nama Pasien'] = $value['nama_pasien'];
            $data_baru[$counter]['Tanggal Konsul'] = date('d/m/Y H:i:s',strtotime($value['tgl_konsulpoli']));
            $data_baru[$counter]['Dokter Pengirim'] = $value['nama_dokter'];
            $data_baru[$counter]['Ruangan Asal'] = $value['ruangan_asal'];
            $data_baru[$counter]['Dokter Rujukan'] = $value['dok_mengkonsul'];
            $data_baru[$counter]['Ruangan Tujuan'] = $value['ruangan_tujuan'];

            $counter++;
        }
        $resultHeader = date('d F Y', strtotime($tgl_awal)) . ' - '.date('d F Y', strtotime($tgl_akhir)).', Periode Tanggal Pendaftaran : '.date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end));
        $header = [
            'Periode Tanggal Konsul'=> $resultHeader
             ];
        $filePath = DocoHelpers::exportExcel("Laporan Pasien Konsul", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionRuangan($id){
        if($id == 8){
            $id = [1,3];
        }
        $ruangan = Ruangan::find()->where([
            'is_deleted' => false,
            'is_active' => true,
            'instalasi_id' => $id
        ])->orderBy(['ruangan_nama' => SORT_ASC])->all();;
        return $ruangan;
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => [1,3]]);

        return $data->all();
    }
  

    /**
    *
    * @see Fungsi get data
    * @return array
    *
    */
    private function GetData($id)
    {
        $setuju_ranap = DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU;
        $default_ranap = DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT;
        $dijawab_rajal = DocoConstants::STATUS_KONSUL_DIJAWAB;
        $belum_dijawab_rajal = DocoConstants::STATUS_KONSUL_BLM_DIJAWAB;
        
        
        if($id == 8){
            $id = [1,3];
        }
        try {
            $model = new PasienKonsul;
            $query = $model::find()->select([
                'no_pendaftaran',
                'tgl_pendaftaran',
                'no_rekam_medik',
                'nama_pasien',
                'nama_dokter',
                'ruangan_asal',
                'dok_mengkonsul',
                'ruangan_tujuan',
                'instalasi_id',
                'tgl_konsulpoli',
            ])->where([
                'instalasi_id' => $id,
                'status_konsul_id' => [$setuju_ranap,$default_ranap,$dijawab_rajal,$belum_dijawab_rajal],
            ]);
            
           
          return $query;
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