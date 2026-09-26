<?php

namespace app\modules\ranap\models;

use Yii;

class RujukanPulangForm extends \yii\base\Model
{
    public $rujukanpulang_id;
    public $pendaftaran_id; 
    public $pasienadmisi_id; 
    public $rujukan_dituju; 
    public $pic_rujukan_dituju; 
    public $diagnosa_masuk; 
    public $diagnosa_keluar; 
    public $keluhan_utama; 
    public $riwayat_penyakit_sekarang; 
    public $riwayat_penyakit_dahulu; 
    public $anamnesis_keluhan_utama; 
    public $anamnesis_kesadaran; 
    public $anamnesis_saturasi_o2; 
    public $anamnesis_tensi; 
    public $anamnesis_suhu; 
    public $anamnesis_nadi; 
    public $anamnesis_pernafasan; 
    public $alasan_dirujuk; 
    public $pemeriksaan_penunjang; 
    public $tindakan_medis; 
    public $tindakan_terapi; 
    public $tindakan_lainnya; 
    public $derajat_0; 
    public $derajat_1; 
    public $derajat_2; 
    public $derajat_3; 
    public $tanggal_rujukan; 
    public $keadaan_umum; 
    public $kesadaran; 
    public $tensi; 
    public $suhu; 
    public $nadi; 
    public $pernafasan; 
    public $saturasi_o2; 
    public $catatan_penting; 
    public $pegawai_id; 
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    
    public $pegawai_nama;
    public $pegawai_kode_bpjs;
    public $diagnosa_prb;

    public function rules() 
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['rujukan_dituju', 'pic_rujukan_dituju', 'diagnosa_masuk', 'diagnosa_keluar', 'keluhan_utama', 'riwayat_penyakit_sekarang', 'riwayat_penyakit_dahulu', 'anamnesis_keluhan_utama', 'anamnesis_kesadaran', 'anamnesis_saturasi_o2', 'anamnesis_tensi', 'anamnesis_suhu', 'anamnesis_nadi','anamnesis_pernafasan',
                'alasan_dirujuk','pemeriksaan_penunjang','tindakan_medis','tindakan_terapi','tindakan_lainnya',
                'derajat_0','derajat_1','derajat_2','derajat_3','tanggal_rujukan','keadaan_umum','kesadaran','tensi','suhu','nadi',
                'pernafasan','saturasi_o2','catatan_penting','pegawai_id','additional_data',
                'created_by'], 'default', 'value' => null],
            [['rujukanpulang_id','pendaftaran_id', 'pasienadmisi_id' ,'rujukan_dituju', 'pic_rujukan_dituju', 'diagnosa_masuk', 'diagnosa_keluar', 'keluhan_utama', 'riwayat_penyakit_sekarang', 'riwayat_penyakit_dahulu', 'anamnesis_keluhan_utama', 'anamnesis_kesadaran', 'anamnesis_saturasi_o2', 'anamnesis_tensi', 'anamnesis_suhu', 'anamnesis_nadi','anamnesis_pernafasan',
                'alasan_dirujuk','pemeriksaan_penunjang','tindakan_medis','tindakan_terapi','tindakan_lainnya',
                'derajat_0','derajat_1','derajat_2','derajat_3','tanggal_rujukan','keadaan_umum','kesadaran','tensi','suhu','nadi',
                'pernafasan','saturasi_o2','catatan_penting', 'pegawai_kode_bpjs', 'pegawai_nama', 'diagnosa_prb'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'rujukanpulang_id' => 'rujukanpulang id',
            'pendaftaran_id' => 'pendaftaran id',
            'pasienadmisi_id' => 'pasienadmisi id',
            'rujukan_dituju' => 'RS Yang Dituju',
            'pic_rujukan_dituju' => 'PIC RS Yang Dituju',
            'diagnosa_masuk' => 'Diagnosa Masuk RS',
            'diagnosa_keluar' => 'Diagnosa Keluar RS',
            'keluhan_utama' => 'Keluhan Utama',
            'riwayat_penyakit_sekarang' => 'Riwayat Penyakit Sekarang',
            'riwayat_penyakit_dahulu' => 'Riwayat Penyakit Dahulu',
            'anamnesis_keluhan_utama' => 'Keluhan Utama',
            'anamnesis_kesadaran' => 'Kesadaran',
            'anamnesis_saturasi_o2' => 'SpO2',
            'anamnesis_tensi' => 'Tensi',
            'anamnesis_suhu' => 'Suhu',
            'anamnesis_nadi' => 'Nadi',
            'anamnesis_pernafasan' => 'Pernafasan',
            'alasan_dirujuk' => 'Alasan Dirujuk',
            'pemeriksaan_penunjang' => 'Pemeriksaan Penunjang',
            'tindakan_medis' => 'Tindakan Medis',
            'tindakan_terapi' => 'Tindakan Terapi',
            'tindakan_lainnya' => 'Tindakan Lainnya',
            'derajat_0' => 'Derajat 0',
            'derajat_1' => 'Derajat 1',
            'derajat_2' => 'Derajat 2',
            'derajat_3' => 'Derajat 3',
            'tanggal_rujukan' => 'Tanggal Rujukan',
            'keadaan_umum' => 'Keadaan Umum',
            'kesadaran' => 'Kesadaran',
            'tensi' => 'Tensi',
            'suhu' => 'Suhu',
            'nadi' => 'Nadi',
            'pernafasan' => 'Pernafasan',
            'saturasi_o2' => 'SpO2',
            'catatan_penting' => 'Catatan Penting',
            'pegawai_id' => 'pegawai id',
            'additional_data' => 'additional data',
            'created_date' => 'created date',
            'created_by' => 'created by',
            'deleted_date' => '',
            'deleted_by' => '',
            'diagnosa_prb' => 'Diagnosa Keluar RS',
        ];
    }
}
