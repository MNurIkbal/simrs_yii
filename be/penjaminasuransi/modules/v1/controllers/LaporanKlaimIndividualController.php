<?php

/**
 * @Author: Sigit
 * @Date:   2019-10-17 10:19:42
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\LaporanKlaimInacbgView;
use app\modules\v1\models\LaporanKlaimInacbgDetailView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PegawaiAksesView;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class LaporanKlaimIndividualController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanKlaimInacbgView';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    public function actionGetDataFilter()
    {
        $modelLookup = new Lookup;

        $penjamin = $modelLookup::find()
        ->where([
            'is_active' => true,
            'lookup_type' => 'inacbg_penjamin'
        ])
        ->orderBy('lookup_name ASC')->all();

        $jenisRawat = [
            ['code' => 'RI', 'label' => 'Rawat Inap'],
            ['code' => 'RJ', 'label' => 'Rawat Jalan'],
        ];

        $periode = $modelLookup::find()
        ->where([
            'is_active' => true,
            'lookup_type' => 'bpjs_periode'
        ])
        ->orderBy('lookup_name ASC')->all();

        $kelasRawat = KelasPelayanan::find()
        ->where([
            'is_active' => true,
        ])
        ->orderBy('kelaspelayanan_nama ASC')->all();

        $caraPulang = $modelLookup::find()
        ->where([
            'is_active' => true,
            'lookup_type' => 'carapulang_inacbg'
        ])
        ->orderBy('lookup_name ASC')->all();

        $jenisTarif = $modelLookup::find()
        ->where([
            'is_active' => true,
            'lookup_type' => 'kode_tarifbpjs'
        ])
        ->orderBy('lookup_name ASC')->all();

        $petugas = PegawaiAksesView::find()
        ->where([
            'instalasi_id' => 29
        ])
        ->orderBy('nama_pegawai ASC')->all();

        $severity = $modelLookup::find()
        ->where([
            'is_active' => true,
            'lookup_type' => 'bpjs_saverity'
        ])
        ->orderBy('lookup_name ASC')->all();

        return [
            'jenis_rawat' => $jenisRawat,
            'periode' => $periode,
            'penjamin' => $penjamin,
            'kelas_rawat' => $kelasRawat,
            'cara_pulang' => $caraPulang,
            'jenis_tarif' => $jenisTarif,
            'petugas' => $petugas,
            'severity' => $severity
        ];
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $model = new LaporanKlaimInacbgView;
        $query = $model::find();

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_masuk'])) {
                $exploded = explode(' - ', $_GET['advanced-filter']['tgl_masuk']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }

                $query->andWhere(['between', 'tgl_masuk', $start, $end]);
                unset($_GET['advanced-filter']['tgl_masuk']);
            }

            if (isset($_GET['advanced-filter']['tgl_keluar'])) {
                $exploded = explode(' - ', $_GET['advanced-filter']['tgl_keluar']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }

                $query->andWhere(['between', 'tgl_keluar', $start, $end]);
                unset($_GET['advanced-filter']['tgl_keluar']);
            }

            if (isset($_GET['advanced-filter']['tgl_group'])) {
                $exploded = explode(' - ', $_GET['advanced-filter']['tgl_group']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }

                $query->andWhere(['between', 'tgl_group', $start, $end]);
                unset($_GET['advanced-filter']['tgl_group']);
            }
        }

        // $query->andWhere(['between', 'tgl_masuk', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDataInacbg()
    {
        $request = Yii::$app->request;
        $model = new LaporanKlaimInacbgDetailView;
        $query = $model::find();
        $filter =  $request->get('filter');
        if ($filter) {
            $start = null;
            $end = null;
             if ($filter['periode']) {
                $exploded = explode(' - ', $filter['tgl_periode']);
                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }
                if ($filter['periode'] == 648 && $start && $end) {
                    $query->andWhere(['between', 'admission_date', $start, $end]);
                } else if ($filter['periode'] == 647 && $start && $end) {
                    $query->andWhere(['between', 'discharge_date', $start, $end]);
                }
            }
            if ($filter['groupingTarget']) {
                $exploded = explode(' - ', $_GET['filter']['groupingTarget']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                    $query->andWhere(['between', 'tgl_group', $start, $end]);
                }                
            }   
            if ($filter['tipe']) {
                $tipe = $filter['tipe'];
                $query->andWhere(['tipe' => $tipe]);
            }
            if ($filter['penjamin_id']) {
                $penjamin_id = $filter['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
            }
            if ($filter['jenis_tarif_id']) {
                $jenis_tarif_id = $filter['jenis_tarif_id'];
                $query->andWhere(['jenis_tarif_id' => $jenis_tarif_id]);
            }
            if ($filter['petugas_id']) {
                $petugas_id = $filter['petugas_id'];
                $query->andWhere(['petugas_id' => $petugas_id]);
            }
            if ($filter['kelaspelayanan_id']) {
                $kelaspelayanan_id = $filter['kelaspelayanan_id'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }
            if ($filter['cara_pulang']) {
                $cara_pulang = $filter['cara_pulang'];
                $query->andWhere(['cara_pulang' => $cara_pulang]);
            }
        }
            
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanKlaimInacbgDetailView;
        $query = $model::find();
        
         $filter =  $request->get('filter');
        if ($filter) {
            $start = null;
            $end = null;
            if ($filter['periode']) {
                $exploded = explode(' - ', $filter['tgl_periode']);
                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }
                if ($filter['periode'] == 648 && $start && $end) {
                    $query->andWhere(['between', 'admission_date', $start, $end]);
                } else if ($filter['periode'] == 647 && $start && $end) {
                    $query->andWhere(['between', 'discharge_date', $start, $end]);
                }
            }
            if ($filter['groupingTarget']) {
                $exploded = explode(' - ', $_GET['filter']['groupingTarget']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                    $query->andWhere(['between', 'tgl_group', $start, $end]);
                }                
            }   
            if ($filter['tipe']) {
                $tipe = $filter['tipe'];
                $query->andWhere(['tipe' => $tipe]);
            }
            if ($filter['penjamin_id']) {
                $penjamin_id = $filter['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
            }
            if ($filter['jenis_tarif_id']) {
                $jenis_tarif_id = $filter['jenis_tarif_id'];
                $query->andWhere(['jenis_tarif_id' => $jenis_tarif_id]);
            }
            if ($filter['petugas_id']) {
                $petugas_id = $filter['petugas_id'];
                $query->andWhere(['petugas_id' => $petugas_id]);
            }
            if ($filter['kelaspelayanan_id']) {
                $kelaspelayanan_id = $filter['kelaspelayanan_id'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }
            if ($filter['cara_pulang']) {
                $cara_pulang = $filter['cara_pulang'];
                $query->andWhere(['cara_pulang' => $cara_pulang]);
            }
        }

        $result = [];
        $header = [];
        $title = 'Rekap Klaim';

        $data = $query->asArray()->all();
        foreach ($data as $key => $value) {
            $nama_kelas = '-';
            if (isset($value['kelas_rawat']) && $value['kelas_rawat']) {
                if (array_key_exists($value['kelas_rawat'],DocoConstants::$LIST_KELAS)){
                    $nama_kelas = DocoConstants::$LIST_KELAS[$value['kelas_rawat']];
                } 
            }
            $item = [
                'KODE_RS'       => $value['kode_rs'] ? $value['kode_rs'] : '-',
                'KELAS_RS'      => $value['kelas_rs'] ? $value['kelas_rs'] : '-',
                'KELAS_RAWAT'   => $nama_kelas,
                'KODE_TARIF'    => $value['kode_tarif'] ? $value['kode_tarif'] : '-',
                'PTD'           => $value['ptd'] ? $value['ptd'] : '-',
                'ADMISSION_DATE'=> $value['admission_date'] ? $value['admission_date'] : '-',
                'DISCHARGE_DATE'=> $value['discharge_date'] ? $value['discharge_date'] : '-',
                'BIRTH_DATE'    => $value['birth_date'] ? $value['birth_date'] : '-',
                'BIRTH_WEIGHT'  => $value['birth_weight'] ? $value['birth_weight'] : 0,
                'SEX'           => $value['sex'] ? $value['sex'] : '-',
                'DISCHARGE_STATUS'=> $value['discharge_status'] ? $value['discharge_status'] : '-',
                'DIAGLIST'      => $value['diaglist'] ? $value['diaglist'] : '-',
                'PROCLIST'      => $value['proclist'] ? $value['proclist'] : '-',
                'ADL1'          => $value['adl1'] ? $value['adl1'] : '-',
                'ADL2'          => $value['adl2'] ? $value['adl2'] : '-',
                'IN_SP'         => $value['in_sp'] ? $value['in_sp'] : '-',
                'IN_SR'         => $value['in_sr'] ? $value['in_sr'] : '-',
                'IN_SI'         => $value['in_si'] ? $value['in_si'] : '-',
                'IN_SD'         => $value['in_sd'] ? $value['in_sd'] : '-',
                'INACBG'        => $value['inacbg'] ? $value['inacbg'] : '-',
                'SUBACUTE'      => $value['subacute'] ? $value['subacute'] : '-',
                'CHRONIC'       => $value['chronic'] ? $value['chronic'] : '-',
                'SP'            => $value['sp'] ? $value['sp'] : '-',
                'SR'            => $value['sr'] ? $value['sr'] : '-',
                'SI'            => $value['si'] ? $value['si'] : '-',
                'SD'            => $value['sd'] ? $value['sd'] : '-',
                'DESKRIPSI_INACBG'=> $value['deskripsi_inacbg'] ? $value['deskripsi_inacbg'] : '-',
                'TARIF_INACBG'  => $value['tarif_inacbg'] ? $value['tarif_inacbg'] : 0,
                'TARIF_SUBACUTE'=> $value['tarif_subacute'] ? $value['tarif_subacute'] : 0,
                'TARIF_CHRONIC' => $value['tarif_chronic'] ? $value['tarif_chronic'] : '-',
                'DESKRIPSI_SP'  => $value['deskripsi_sp'] ? $value['deskripsi_sp'] : '-',
                'TARIF_SP'      => $value['tarif_sp'] ? $value['tarif_sp'] : 0,
                'DESKRIPSI_SR'  => $value['deskripsi_sr'] ? $value['deskripsi_sr'] : '-',
                'TARIF_SR'      => $value['tarif_sr'] ? $value['tarif_sr'] : 0,
                'DESKRIPSI_SI'  => $value['deskripsi_si'] ? $value['deskripsi_si'] : '-',
                'DESKRIPSI_SD'  => $value['deskripsi_sd'] ? $value['deskripsi_sd'] : '-',
                'TARIF_SD'      => $value['tarif_sd'] ? $value['tarif_sd'] : 0,
                'TOTAL_TARIF'   => $value['total_tarif'] ? $value['total_tarif'] : 0,
                'TARIF_RS'      => $value['tarif_rs'] ? $value['tarif_rs'] : 0,
                'TARIF_POLI_EKS'=> $value['tarif_poli_eks'] ? $value['tarif_poli_eks'] : 0,
                'LOS'           => $value['los'] ? $value['los'] : '-',
                'ICU_INDIKATOR' => $value['icu_indikator'] ? $value['icu_indikator'] : 0,
                'ICU_LOS'       => $value['icu_los'] ? $value['icu_los'] : 0,
                'VENT_HOUR'     => $value['vent_hour'] ? $value['vent_hour'] : 0,
                'NAMA_PASIEN'   => $value['nama_pasien'] ? $value['nama_pasien'] : '-',
                'MRN'           => $value['mrn'] ? $value['mrn'] : '-',
                'UMUR_TAHUN'    => $value['umur_tahun'] ? $value['umur_tahun'] : '-',
                'UMUR_HARI'     => $value['umur_hari'] ? $value['umur_hari'] : '-',
                'DPJP'          => $value['dpjp'] ? $value['dpjp'] : '-',
                'SEP'           => $value['sep'] ? $value['sep'] : '-',
                'NOKARTU'       => $value['nokartu'] ? $value['nokartu'] : '-',
                'PAYOR_ID'      => $value['payor_id'] ? $value['payor_id'] : '-',
                'CODER_ID'      => $value['coder_id'] ? $value['coder_id'] : '-',
                'VERSI_INACBG'  => $value['versi_inacbg'] ? $value['versi_inacbg'] : '-',
                'VERSI_GROUPER' => $value['versi_grouper'] ? $value['versi_grouper'] : '-',
                'C1'            => $value['c1'] ? $value['c1'] : '-',
                'C2'            => $value['c2'] ? $value['c2'] : '-',
                'C3'            => $value['c3'] ? $value['c3'] : '-',
                'C4'            => $value['c4'] ? $value['c4'] : '-',
            ];
            array_push($result, $item);
        }
        $filePath = DocoHelpers::exportExcel($title, $result, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_laporan# => Menampilkan Table Pasien Rajal Bpjs
     * @attribute #periode# => Periode laporan
     * @attribute #nama_pengguna# => Nama Pengguna
     * @attribute #tgl_cetak# => Nama Pengguna
     * @attribute #nip# => NIP
     **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $model = new LaporanKlaimInacbgDetailView;
        $query = $model::find();

        $filter =  $request->get('filter');
        if ($filter) {
            $start = null;
            $end = null;
             if ($filter['periode']) {
                $exploded = explode(' - ', $filter['tgl_periode']);
                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                }
                if ($filter['periode'] == 648 && $start && $end) {
                    $query->andWhere(['between', 'admission_date', $start, $end]);
                } else if ($filter['periode'] == 647 && $start && $end) {
                    $query->andWhere(['between', 'discharge_date', $start, $end]);
                }
            }
            if ($filter['groupingTarget']) {
                $exploded = explode(' - ', $_GET['filter']['groupingTarget']);

                if (count($exploded) == 2) {
                    $start = date('Y-m-d', strtotime($exploded[0])).' 00:00:00';
                    $end = date('Y-m-d', strtotime($exploded[1])).' 23:59:59';
                    $query->andWhere(['between', 'tgl_group', $start, $end]);
                }                
            }   
            if ($filter['tipe']) {
                $tipe = $filter['tipe'];
                $query->andWhere(['tipe' => $tipe]);
            }
            if ($filter['penjamin_id']) {
                $penjamin_id = $filter['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
            }
            if ($filter['jenis_tarif_id']) {
                $jenis_tarif_id = $filter['jenis_tarif_id'];
                $query->andWhere(['jenis_tarif_id' => $jenis_tarif_id]);
            }
            if ($filter['petugas_id']) {
                $petugas_id = $filter['petugas_id'];
                $query->andWhere(['petugas_id' => $petugas_id]);
            }
            if ($filter['kelaspelayanan_id']) {
                $kelaspelayanan_id = $filter['kelaspelayanan_id'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }
            if ($filter['cara_pulang']) {
                $cara_pulang = $filter['cara_pulang'];
                $query->andWhere(['cara_pulang' => $cara_pulang]);
            }
        }

        $data = $query->asArray()->all();
        $result = [];
        foreach ($data as $key => $value) {
            $kode_rs = '-';
            $kelas_rs = '-';
            if ($value['kode_rs']) {
                $exp = explode('-', $value['kode_rs']);
                if (isset($exp[2]) && $exp[2]) {
                    $exp2 = explode(':', $exp[2]);
                    $kelas_rs = (isset($exp2[1]) && $exp2[1]) ? ltrim($exp2[1], 'TARIF RS') : '-'; 
                }
            }
            $nama_kelas = '-';
            if (isset($value['kelas_rawat']) && $value['kelas_rawat']) {
                if (array_key_exists($value['kelas_rawat'],DocoConstants::$LIST_KELAS)){
                    $nama_kelas = DocoConstants::$LIST_KELAS[$value['kelas_rawat']];
                } 
            }
            $item = [
                'KODE_RS'       => $kode_rs,
                'KELAS_RS'      => $kelas_rs,
                'KELAS_RAWAT'   => $nama_kelas,
                'KODE_TARIF'    => $value['kode_tarif'] ? $value['kode_tarif'] : '-',
                'PTD'           => $value['ptd'] ? $value['ptd'] : '-',
                'ADMISSION_DATE'=> $value['admission_date'] ? $value['admission_date'] : '-',
                'DISCHARGE_DATE'=> $value['discharge_date'] ? $value['discharge_date'] : '-',
                'BIRTH_DATE'    => $value['birth_date'] ? $value['birth_date'] : '-',
                'BIRTH_WEIGHT'  => $value['birth_weight'] ? $value['birth_weight'] : 0,
                'SEX'           => $value['sex'] ? $value['sex'] : '-',
                'DISCHARGE_STATUS'=> $value['discharge_status'] ? $value['discharge_status'] : '-',
                'DIAGLIST'      => $value['diaglist'] ? $value['diaglist'] : '-',
                'PROCLIST'      => $value['proclist'] ? $value['proclist'] : '-',
                'ADL1'          => $value['adl1'] ? $value['adl1'] : '-',
                'ADL2'          => $value['adl2'] ? $value['adl2'] : '-',
                'IN_SP'         => $value['in_sp'] ? $value['in_sp'] : '-',
                'IN_SR'         => $value['in_sr'] ? $value['in_sr'] : '-',
                'IN_SI'         => $value['in_si'] ? $value['in_si'] : '-',
                'IN_SD'         => $value['in_sd'] ? $value['in_sd'] : '-',
                'INACBG'        => $value['inacbg'] ? $value['inacbg'] : '-',
                'SUBACUTE'      => $value['subacute'] ? $value['subacute'] : '-',
                'CHRONIC'       => $value['chronic'] ? $value['chronic'] : '-',
                'SP'            => $value['sp'] ? $value['sp'] : '-',
                'SR'            => $value['sr'] ? $value['sr'] : '-',
                'SI'            => $value['si'] ? $value['si'] : '-',
                'SD'            => $value['sd'] ? $value['sd'] : '-',
                'DESKRIPSI_INACBG'=> $value['deskripsi_inacbg'] ? $value['deskripsi_inacbg'] : '-',
                'TARIF_INACBG'  => $value['tarif_inacbg'] ? $value['tarif_inacbg'] : 0,
                'TARIF_SUBACUTE'=> $value['tarif_subacute'] ? $value['tarif_subacute'] : 0,
                'TARIF_CHRONIC' => $value['tarif_chronic'] ? $value['tarif_chronic'] : '-',
                'DESKRIPSI_SP'  => $value['deskripsi_sp'] ? $value['deskripsi_sp'] : '-',
                'TARIF_SP'      => $value['tarif_sp'] ? $value['tarif_sp'] : 0,
                'DESKRIPSI_SR'  => $value['deskripsi_sr'] ? $value['deskripsi_sr'] : '-',
                'TARIF_SR'      => $value['tarif_sr'] ? $value['tarif_sr'] : 0,
                'DESKRIPSI_SI'  => $value['deskripsi_si'] ? $value['deskripsi_si'] : '-',
                'DESKRIPSI_SD'  => $value['deskripsi_sd'] ? $value['deskripsi_sd'] : '-',
                'TARIF_SD'      => $value['tarif_sd'] ? $value['tarif_sd'] : 0,
                'TOTAL_TARIF'   => $value['total_tarif'] ? $value['total_tarif'] : 0,
                'TARIF_RS'      => $value['tarif_rs'] ? $value['tarif_rs'] : 0,
                'TARIF_POLI_EKS'=> $value['tarif_poli_eks'] ? $value['tarif_poli_eks'] : 0,
                'LOS'           => $value['los'] ? $value['los'] : '-',
                'ICU_INDIKATOR' => $value['icu_indikator'] ? $value['icu_indikator'] : 0,
                'ICU_LOS'       => $value['icu_los'] ? $value['icu_los'] : 0,
                'VENT_HOUR'     => $value['vent_hour'] ? $value['vent_hour'] : 0,
                'NAMA_PASIEN'   => $value['nama_pasien'] ? $value['nama_pasien'] : '-',
                'MRN'           => $value['mrn'] ? $value['mrn'] : '-',
                'UMUR_TAHUN'    => $value['umur_tahun'] ? $value['umur_tahun'] : '-',
                'UMUR_HARI'     => $value['umur_hari'] ? $value['umur_hari'] : '-',
                'DPJP'          => $value['dpjp'] ? $value['dpjp'] : '-',
                'SEP'           => $value['sep'] ? $value['sep'] : '-',
                'NOKARTU'       => $value['nokartu'] ? $value['nokartu'] : '-',
                'PAYOR_ID'      => $value['payor_id'] ? $value['payor_id'] : '-',
                'CODER_ID'      => $value['coder_id'] ? $value['coder_id'] : '-',
                'VERSI_INACBG'  => $value['versi_inacbg'] ? $value['versi_inacbg'] : '-',
                'VERSI_GROUPER' => $value['versi_grouper'] ? $value['versi_grouper'] : '-',
                'C1'            => $value['c1'] ? $value['c1'] : '-',
                'C2'            => $value['c2'] ? $value['c2'] : '-',
                'C3'            => $value['c3'] ? $value['c3'] : '-',
                'C4'            => $value['c4'] ? $value['c4'] : '-',
            ];
            array_push($result, $item);
        }
        
        $print = new DocoPrint();
        $print->attributes = [
            '#table_laporan#' => $this->renderPartial('index',['data' => $result]),
            '#periode#' => date('d F Y'),
            '#nama_pengguna#'=> '',
            '#tgl_cetak#'=> date('d F Y'),
            '#nip#'=>'',
        ];
        $print->Output();
    }
}