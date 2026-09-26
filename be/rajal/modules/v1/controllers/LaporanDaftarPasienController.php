<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:   2018-07-13 11:44:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 12:03:03
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LapDaftarPasien;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LaporanDaftarPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LapDaftarPasien';

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

        $model = new LapDaftarPasien;
        $query = $model::find();
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
            }

        }
        // if ($_GET['ruangan_id']) {
        //     $ruangan_id = $_GET['ruangan_id'];
        //     $query->andWhere(['ruangan_id' => $ruangan_id]);
        // }

        $query->andWhere(['between', '"tgl_pendaftaran"', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    /**
    * @controller actionExportPdf 
    * @attribute #tgl_pendaftaran# => sebagai tanggal masuk pendaftaran
    * @attribute #dokter_pemeriksa# => sebagai dokter pemeriksa
    * @attribute #carabayar# => sebagai cara bayar
    * @attribute #penjamin# => sebagai penjamin
    * @attribute #ruangan_asal# => sebagai ruangan asal
    * @attribute #ruangan_nama# => sebagai ruangan nama poli
    * @attribute #table_detail# => sebagai table 
    **/
    public function actionExportPdf()
    {
        $model = new LapDaftarPasien;
        $request = Yii::$app->request;
        $data = [];
        $id_ruangan = $request->get('ruangan_id');
        $advancedFilters = $request->get('advanced-filter', []);
        // $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
        $query = $model::find();

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        //add params cetak
        $tgl_pendaftaran = date('d-m-Y', strtotime($tgl_awal));
        $dokter_pemeriksa = "";
        $carabayar = "";
        $penjamin = "";
        $ruangan_asal = ""; 
        $ruangan_nama = $request->get('ruangan_nama');

        if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];

            $tgl_pendaftaran = date('d-m-Y', strtotime($advancedFilters['tgl_pendaftaran_awal']))." s/d ".date('d-m-Y', strtotime($advancedFilters['tgl_pendaftaran_akhir']));
        }
        if (isset($advancedFilters['carabayar_nama']) ) {
            $carabayar = ($advancedFilters['carabayar_nama']) ? $advancedFilters['carabayar_nama'] : "" ;
        }
        if (isset($advancedFilters['penjamin_nama']) ) {
            $penjamin = ($advancedFilters['penjamin_nama']) ? $advancedFilters['penjamin_nama'] : "" ;
        }
        if (isset($advancedFilters['ruangan_nama']) ) {
            $ruangan_asal = ($advancedFilters['ruangan_nama']) ? $advancedFilters['ruangan_nama'] : "" ;
        }
        if (isset($advancedFilters['nama_pegawai']) ) {
            $dokter_pemeriksa = ($advancedFilters['nama_pegawai']) ? $advancedFilters['nama_pegawai'] : "" ;
        }
        $query->andWhere(['between', '"tgl_pendaftaran"', $tgl_awal, $tgl_akhir]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('print_pdf', [
                'data' => $data,
                'tgl_pendaftaran' => $tgl_pendaftaran,
                'dokter_pemeriksa' => $dokter_pemeriksa,
                'carabayar' => $carabayar,
                'penjamin' => $penjamin,
                'ruangan_asal' => $ruangan_asal,
                ]),
            '#ruangan_nama#' => $ruangan_nama,
        ];
        $print->Output();
    }


    public function actionExportExcel()
    {
        $data = [];
        $model = new LapDaftarPasien;
        $request = Yii::$app->request;
        $id_ruangan = $request->get('ruangan_id');
        // $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
        $query = $model::find();

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
        }
        
        $query->andWhere(['between', '"tgl_pendaftaran"', $tgl_awal, $tgl_akhir]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1=date_create($value['tgl_pendaftaran']);
            $date2=date_create();
            $diff=date_diff($date1,$date2);
            $dataHariRawat = $diff->d;
            $data_baru[$counter]['No Antrian'] = $value['no_antrian'];
            $data_baru[$counter]['Tanggal pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));
            $data_baru[$counter]['Ruangan Asal'] = $value['ruangan_nama'];
            $data_baru[$counter]['No. Pendaftaran'] = $value['no_pendaftaran'];
            $data_baru[$counter]['No. Rekam Medik'] = $value['no_rekam_medik'];
            $data_baru[$counter]['Nama Pasien'] = $value['nama_pasien'];
            $data_baru[$counter]['Jenis Kelamin'] = $value['jenis_kelamin'];
            $data_baru[$counter]['Cara Bayar'] = $value['carabayar_nama'];
            $data_baru[$counter]['Penjamin'] = $value['penjamin_nama'];
            $data_baru[$counter]['Dokter Pemeriksa'] = $value['nama_pegawai'];
            $data_baru[$counter]['Status'] = $value['status_periksa'];
            $data_baru[$counter]['Catatan'] = $value['alasan_batal'];
            $counter++;
        }
        $header = ['Periode'=> $tgl_awal . ' - '.$tgl_akhir];
        $filePath = DocoHelpers::exportExcel("Laporan Daftar Pasien", $data_baru, $header, array(
                "uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    *
    * @see Fungsi get list data
    * @return array
    *
    */
    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;

            // Get status periksa
            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            // Get pegawai
            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan'));
            $data_pegawai = $find_pegawai->asArray()->all();

            // Get penjamin
            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();
            
            // Get cara bayar
            $find_carabayar = $this->getCarabayar();
            $data_carabayar = $find_carabayar->asArray()->all();

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->asArray()->all();

            // Get jenis kelamin
            $modelJenisKelamin = $this->getJenisKelamin();
            $dataJenisKelamin = $modelJenisKelamin->asArray()->all();


            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-carabayar' => $data_carabayar,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
                'data-ruangan' => $dataRuangan,
                'data-jenis-kelamin' => $dataJenisKelamin,
            ];
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
    *
    * @see Fungsi get data status periksa
    * @return array, activeQueryRecords
    *
    */
    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa FROM lookup_m WHERE lookup_type = 'status_periksa'";
        $result = Lookup::findBySql($sql);

        return $result;

    }

    /**
    *
    * @see Fungsi get data jenis kelamin
    * @return array, activeQueryRecords
    *
    */
    private function getJenisKelamin()
    {
        // Query
        $sql = "SELECT lookup_id, lookup_name, lookup_name as jenis_kelamin FROM lookup_m WHERE lookup_type = 'jenis_kelamin'";

        // Result
        $result = Lookup::findBySql($sql);

        // Return
        return $result;
    }

    /**
    *
    * @see Fungsi get data ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getRuangan()
    {
        // Query
        $sql = "SELECT ruangan_id, ruangan_nama FROM ruangan_m WHERE is_active=true AND is_deleted=false";

        // Result
        $result = Ruangan::findBySql($sql);

        // Return
        return $result;
    }

    /**
    *
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getPenjamin()
    {
         // Query
         $sql = "SELECT penjamin_id, penjamin_nama FROM penjamin_m WHERE is_active=true AND is_deleted=false";

         // Result
         $result = Penjamin::findBySql($sql);
 
         // Return
         return $result;

    }

    /**
    *
    * @see Fungsi get data carabayar
    * @return array, activeQueryRecords
    *
    */
    private function getCarabayar()
    {
         // Query
         $sql = "SELECT carabayar_id, carabayar_nama FROM carabayar_m WHERE is_active=true AND is_deleted=false";

         // Result
         $result = Ruangan::findBySql($sql);
 
         // Return
         return $result;

    }

    /**
    *
    * @see Fungsi get data pegawai ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getPegawaiRuangan($id_ruangan)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = Ruangan::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;

    }

}