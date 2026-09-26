<?php

/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfKunjunganRsView;
use app\modules\v1\models\InfKunjunganRsTracerView;
use Doco\models\pendaftaran\InfoKunjunganRiView;
use app\modules\v1\models\InfoKunjunganRd;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\KeluargaPasienV;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Laporanr2kV;
use app\modules\v1\models\Laporanr2mkV;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\LoginPemakaiView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\SySuratreferalV;
use app\modules\v1\models\SyPasienView;

use Picqer\Barcode\BarcodeGeneratorPNG;

use app\modules\v1\cache\Cache;
use Da\QrCode\QrCode;

class PrintIdentitasPasienProcess extends \Doco\components\DocoBaseProcessExtension 
{
    protected function processFlow()
    {
        #code...
        $request = Yii::$app->request;
        $jenis = $request->get('param', null);
        if ($jenis != 'ranap' && $jenis != 'penunjang') {
            $model = new InfKunjunganRsView;
        } else if ($jenis == 'penunjang') {
            $model = new InfPasienPenunjang;
        } else if ($jenis == 'ranap'){
            $model = new InfoKunjunganRiView;
        } else {
            $model = new InfKunjunganRsView;
        }
        $pasien_id = $request->get('pasien_id',null);
        if(!$pasien_id){
            throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
        }
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        if(!$pendaftaran_id){
            throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
        }
        $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

        if($jenis == 'penunjang'){
            $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
        } else if($jenis == 'igd') {
            $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
        } else{
            $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
        }
        if(!$kunjungan){
            throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
        }

        $modelPasien = new PasienV;
        $modelPasien = $modelPasien::find()->where(['pasien_id'=>$pasien_id])->one();
        $modelKp = new KeluargaPasienV;
        $modelKp = $modelKp::find()->where(['pasien_id'=>$pasien_id])->one();

        if($jenis == "ranap"){
            $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_admisi)?$kunjungan->tgl_admisi:'');
            $tanggal_pendaftaran_convert = date('d-m-Y H:i:s', $strtotime_tanggal_kunjungan);
        }else if($jenis == "penunjang"){
            $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tglmasukpenunjang)?$kunjungan->tglmasukpenunjang :'');
            $tanggal_pendaftaran_convert = date('d-m-Y H:i:s', $strtotime_tanggal_kunjungan);
        }else{
            $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_pendaftaran)?$kunjungan->tgl_pendaftaran:'');
            $tanggal_pendaftaran_convert = date('d-m-Y H:i:s', $strtotime_tanggal_kunjungan);
        }
        $tgl_pendaftaran = explode(" ", $tanggal_pendaftaran_convert)[0];
        $jam_pendaftaran = explode(" ", $tanggal_pendaftaran_convert)[1];
        $tahun_lahir = explode("-", $kunjungan->tanggal_lahir)[0];
        $bulan_lahir = explode("-", $kunjungan->tanggal_lahir)[1];
        $tanggal_lahir = explode("-", $kunjungan->tanggal_lahir)[2];
        $tahun_umur = explode(" ", $kunjungan->umur)[0];
        $bulan_umur = explode(" ", $kunjungan->umur)[2];
        $tanggal_umur = explode(" ", $kunjungan->umur)[4];
        switch($kunjungan->jeniskelamin){
            case DocoConstants::VAR_LK:
                $jenis_kelamin = '  -';
                break;
            case DocoConstants::VAR_PR:
                $jenis_kelamin = '-';
                break;
            default:
                $jenis_kelamin = '';
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#tgl_pendaftaran#' => $tgl_pendaftaran,
            '#jam_pendaftaran#' => $jam_pendaftaran,
            '#no_pendaftaran#' => isset($kunjungan->no_pendaftaran
            )?$kunjungan->no_pendaftaran:'',
            '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik
            )?$kunjungan->no_rekam_medik:'',
            '#namadepan#' => isset($kunjungan->namadepan
            )?$kunjungan->namadepan:'',
            '#nama_pasien#' => isset($kunjungan->nama_pasien
            )?$kunjungan->nama_pasien:'',
            '#jenis_kelamin#' => $jenis_kelamin,
            '#alamat_pasien#' => isset($kunjungan->alamat_pasien
            )?$kunjungan->alamat_pasien:'',
            '#kelurahan_nama#' => isset($kunjungan->kelurahan_nama
            )?$kunjungan->kelurahan_nama:'',
            '#kecamatan_nama#' => isset($kunjungan->kecamatan_nama
            )?$kunjungan->kecamatan_nama:'',
            '#kode_pos#' => isset($modelPasien->kode_pos)?$modelPasien->kode_pos:'',
            '#no_telepon_pasien#' => isset($kunjungan->no_telepon_pasien
            )?$kunjungan->no_telepon_pasien:'',
            '#pekerjaan_nama#' => isset($kunjungan->pekerjaan_nama
            )?$kunjungan->pekerjaan_nama:'',
            '#tahun_lahir#' => $tahun_lahir,
            '#bulan_lahir#' => $bulan_lahir,
            '#tanggal_lahir#' => $tanggal_lahir,
            '#tahun_umur#' => $tahun_umur,
            '#bulan_umur#' => $bulan_umur,
            '#tanggal_umur#' => $tanggal_umur,
            '#status_perkawinan#' => isset($modelPasien->status_perkawinan
            )?$modelPasien->status_perkawinan:'',
            '#agama#' => isset($modelPasien->agama_pasien
            )?$modelPasien->agama_pasien:'',
            '#kabupaten_nama#' => isset($kunjungan->kabupaten_nama
            )?$kunjungan->kabupaten_nama:'',
            '#suku_nama#' => isset($kunjungan->suku_nama)?$kunjungan->suku_nama:'',
            '#warga_negara#' => isset($modelPasien->warganegara
            )?$modelPasien->warganegara:'',
            '#golongan_darah#' => isset($modelPasien->golongan_darah
            )?$modelPasien->golongan_darah:'',
            '#ruangan_nama#' => isset($kunjungan->ruangan_nama)?$kunjungan->ruangan_nama:'',
            '#kelaspelayanan_nama#' => isset($kunjungan->kelaspelayanan_nama)?$kunjungan->kelaspelayanan_nama:'',
            '#nama_pegawai#' => isset($kunjungan->nama_pegawai)?$kunjungan->nama_pegawai:'',
            '#pj_namadepan_nama#' => isset($kunjungan->pj_namadepan_nama
            )?$kunjungan->pj_namadepan_nama:'',
            '#penanggungjawab_nama#' => isset($kunjungan->penanggungjawab_nama
            )?$kunjungan->penanggungjawab_nama:'',
            '#penanggungjawab_alamat#' => isset($kunjungan->penanggungjawab_alamat
            )?$kunjungan->penanggungjawab_alamat:'',
            '#penanggungjawab_notelp#' => isset($kunjungan->penanggungjawab_notelp
            )?$kunjungan->penanggungjawab_notelp:'',
            '#pj_pekerjaan_nama#' => isset($kunjungan->pj_pekerjaan_nama
            )?$kunjungan->pj_pekerjaan_nama:'',
            '#pj_kelurahan_nama#' => isset($kunjungan->pj_kelurahan_nama
            )?$kunjungan->pj_kelurahan_nama:'',
            '#pj_kecamatan_nama#' => isset($kunjungan->pj_kecamatan_nama
            )?$kunjungan->pj_kecamatan_nama:'',
            '#keluarga_namadepan_nama#' => isset($modelKp->keluarga_namadepan_nama
            )?$modelKp->keluarga_namadepan_nama:'',
            '#keluarga_nama#' => isset($modelKp->keluarga_nama
            )?$modelKp->keluarga_nama:'',
            '#keluarga_alamat#' => isset($modelKp->keluarga_alamat
            )?$modelKp->keluarga_alamat:'',
            '#keluarga_no_telepon#' => isset($modelKp->keluarga_no_telepon
            )?$modelKp->keluarga_no_telepon:'',
            '#keluarga_pekerjaan_nama#' => isset($modelKp->keluarga_pekerjaan_nama
            )?$modelKp->keluarga_pekerjaan_nama:'',
            '#keluarga_kelurahan_nama#' => isset($modelKp->keluarga_kelurahan_nama
            )?$modelKp->keluarga_kelurahan_nama:'',
            '#keluarga_kecamatan_nama#' => isset($modelKp->keluarga_kecamatan_nama
            )?$modelKp->keluarga_kecamatan_nama:'',
            '#nama_perujuk#' => isset($modelKp->nama_perujuk
            )?$modelKp->nama_perujuk:'',
            '#asalrujukan_nama#' => isset($modelKp->asalrujukan_nama
            )?$modelKp->asalrujukan_nama:'',
            '#kodediagnosa_rujukan#' => isset($modelKp->kodediagnosa_rujukan
            )?$modelKp->kodediagnosa_rujukan:'',
        ];
        $print->Output();
    }

}