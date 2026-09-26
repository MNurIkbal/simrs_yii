<?php

namespace app\modules\v1\controllers;

/**
 * @Author  : Sunarko
 * @Date    : 2018-08-14 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan daftar pasien rawat inap
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanPasienriView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LaporanDaftarPasienController extends DocoActiveController
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
        $query = $model::find();
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['Tanggal Masuk'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['Tanggal Masuk']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['Tanggal Masuk']); // Unset Advanced Filter  date range
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
        // if ($_GET['ruangan_id']) {
        //     $ruangan_id = $_GET['ruangan_id'];
        //     $query->andWhere(['ruangan_id' => $ruangan_id]);
        // }

        $query->andWhere(['between', '"Tanggal Masuk"', $start, $end]);

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
        $model = new LaporanPasienriView;
        $request = Yii::$app->request;
        $data = [];
        $id_ruangan = $request->get('ruangan_id');
        $advancedFilters = $request->get('advanced-filter', []);
        $dataKepala = (PegawaiView::find()->where(['ruangan_id'=>$id_ruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? 
            PegawaiView::find()->where(['ruangan_id'=>$id_ruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one() : '';
        // $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
        $query = $model::find();

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($advancedFilters['tgl_masuk_awal']) && isset($advancedFilters['tgl_masuk_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_masuk_awal'];
            $tgl_akhir = $advancedFilters['tgl_masuk_akhir'];
        }
        $query->andWhere(['between', '"Tanggal Masuk"', $tgl_awal, $tgl_akhir]);

        if(isset($_GET['advanced-filter'])) {
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
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        if (!empty($data)) {
            $counter = 0;

            foreach ($data as $key => $value) {
                $tempKelas = $value['Kelas Pelayanan'];
                $data[$counter]['Kelas Pelayanan'] = 'Kelas Layanan: '.$value['Kelas Pelayanan'].'<br>Kelas Titipan: -';

                if (!array_key_exists('kelas_ditagihkan_nama_pk', $value)) {
                    $value['kelas_ditagihkan_nama_pk'] = '-';
                }

                if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                    $value['kelas_ditagihkan_nama'] = '-';
                }

                if ($value['pindahkamar_id']) {
                    if ($value['is_stoptitipan_pk'] == false) {
                        if ($value['is_pasientitipan_pk']) {
                            $data[$counter]['Kelas Pelayanan'] = 'Kelas Layanan: '.$tempKelas.'<br>Kelas Titipan: '.$value['kelas_ditagihkan_nama_pk'];
                        }
                    }
                } else {
                    if ($value['is_stoptitipan'] == false) {
                        if ($value['is_pasientitipan']) {
                            $data[$counter]['Kelas Pelayanan'] = 'Kelas Layanan: '.$tempKelas.'<br>Kelas Titipan: '.$value['kelas_ditagihkan_nama'];
                        }
                    }
                }

                $counter++;
            }
        }
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('print_pdf', ['data' => $data]),
            '#tanggal_periode_akhir#' => date('d F Y', strtotime($tgl_awal)),
            '#tanggal_periode_awal#' => date('d F Y', strtotime($tgl_akhir)),
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#tanggal#' => date('d F Y'),
            '#tanggal_cetak#' => date('d F Y H:i:s'),
            '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
            '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
            '#ruangan#'=> ($dataKepala) ? $dataKepala['ruangan_nama'] : '-',
        ];

        $print->Output();
    }


    public function actionExportExcel()
    {
        $data = [];
        $model = new LaporanPasienriView;
        $request = Yii::$app->request;
        $id_ruangan = $request->get('ruangan_id');
        // $query = $model::find()->where(['ruangan_id' => $id_ruangan]);
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
        $query->andWhere(['between', '"Tanggal Masuk"', $tgl_awal, $tgl_akhir]);

        if(isset($_GET['advanced-filter'])) {
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

        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1=date_create($value['Tanggal Masuk']);
            $date2=date_create();
            $diff=date_diff($date1,$date2);
            $dataHariRawat = $diff->d;
            $data_baru[$counter]['Tanggal Masuk'] = date('d F Y H:i:s', strtotime($value['Tanggal Masuk']));
            $data_baru[$counter]['Tanggal Keluar'] = ($value['Tanggal Keluar']) ? date('d F Y H:i:s', strtotime($value['Tanggal Keluar'])) : "" ;
            $data_baru[$counter]['No. Rekam Medik'] = $value['No. Rekam Medik'];
            $data_baru[$counter]['No. Pendaftaran'] = $value['No. Pendaftaran'];
            $data_baru[$counter]['Nama Pasien'] = $value['Nama Pasien'];
            $data_baru[$counter]['Jenis Kelamin'] = $value['Jenis Kelamin'];
            $data_baru[$counter]['Dokter Penanggung Jawab'] = $value['Dokter'];
            $data_baru[$counter]['Cara Bayar / Penjamin'] = $value['Cara Bayar'].' / '.$value['Penjamin'];
            $data_baru[$counter]['Kelas Pelayanan / Kelas Tagihan'] = $value['Kelas Pelayanan'];
            $data_baru[$counter]['Kelas Titipan'] = '-';

            if (!array_key_exists('kelas_ditagihkan_nama_pk', $value)) {
                $value['kelas_ditagihkan_nama_pk'] = '-';
            }

            if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                $value['kelas_ditagihkan_nama'] = '-';
            }

            if ($value['pindahkamar_id']) {
                if ($value['is_stoptitipan_pk'] == false) {
                    if ($value['is_pasientitipan_pk']) {
                        $data_baru[$counter]['Kelas Titipan'] = $value['kelas_ditagihkan_nama_pk'];
                    }
                }
            } else {
                if ($value['is_stoptitipan'] == false) {
                    if ($value['is_pasientitipan']) {
                        $data_baru[$counter]['Kelas Titipan'] = $value['kelas_ditagihkan_nama'];
                    }
                }
            }

            $data_baru[$counter]['Jenis Kasus Penyakit'] = $value['Jenis Kasus Penyakit'];
            $data_baru[$counter]['Ruangan'] = $value['Ruangan'];
            $data_baru[$counter]['Lama Rawat'] = ($value['Lama Rawat']) ? $value['Lama Rawat']." Hari" : "" ;
            $data_baru[$counter]['Status'] = $value['status_ranap_nama'];
            $data_baru[$counter]['Catatan'] = $value['alasan_batal'];
            $counter++;
        }
        $header = ['Periode'=> date('d F Y', strtotime($tgl_awal)) . ' - '.date('d F Y', strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel("Laporan Pasien Rawat Inap", $data_baru, $header, array("uploadPath" => "./uploads"),[],[],true);

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

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->asArray()->all();

            // Get jenis Kasus Penyakit
            $modelJenisKasus = $this->getJenisKasus();
            $dataJenisKasus = $modelJenisKasus->asArray()->all();


            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-ruangan' => $dataRuangan,
                'data-jenis-kasus' => $dataJenisKasus,
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
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa FROM lookup_m WHERE lookup_type = 'status_ranap'";
        $result = LaporanPasienriView::findBySql($sql);

        return $result;

    }

    /**
    *
    * @see Fungsi get data jenis kasus penyakit
    * @return array, activeQueryRecords
    *
    */
    private function getJenisKasus()
    {
        // Query
        $sql = "SELECT jeniskasuspenyakit_id, jeniskasuspenyakit_nama, jeniskasuspenyakit_nama as jeniskasuspenyakit_nama FROM jeniskasuspenyakit_m WHERE is_deleted=false AND is_active=true";

        // Result
        $result = LaporanPasienriView::findBySql($sql);

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
        $result = LaporanPasienriView::findBySql($sql);

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
        $result = LaporanPasienriView::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;

    }

}