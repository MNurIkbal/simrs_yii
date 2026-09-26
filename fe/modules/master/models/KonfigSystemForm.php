<?php

namespace app\modules\master\models;

use Yii;

class KonfigSystemForm extends \app\components\DocoBaseModel
{

    public $konfigsystem_id;
    public $is_tgltransaksimundur;
    public $is_smsgateway;
    public $is_bayarlangsung;
    public $is_jurnalotomatis;
    public $is_postingotomatis;
    public $is_bridgingbpjs;
    public $bpjs_url;
    public $bpjs_consid;
    public $bpjs_secretkey;
    public $bpjs_ppkpelayanan;
    public $bpjs_port;
    public $is_akomodasiotomatis;
    public $is_pembulatankeatas;
    public $satuanpembulatapublic;  
    public $jatuhtempo_tagihan;
    public $kso_url;
    public $kso_consid;
    public $kso_secretkey;
    public $start_antrian;
    public $tglberlaku_dari;
    public $tglberlaku_sampai;
    public $rincian_tagihan;
    public $additional_data;
    public $mail_protocol;
    public $mail_parameter;
    public $smtp_hostname;
    public $smtp_username;
    public $smtp_password;
    public $smtp_port;
    public $smtp_timeout;
    public $sync_url;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $start_antrian_status;
    public $adm_persen;
    public $adm_tindakan_id;
    public $default_biaya;
    public $url_print;
    public $kelas_pelayanan;
    public $pembayaran_langsung;
    public $satuanpembulatan;
    public $kelola_tagihan;
    public $edit_billing;
    public $is_validasi_pembayaran;
    public $expired_time_program_fisio;
    public $is_expired_time_program_fisio;
    public $is_show_obat_form_penatajasa;
    public $is_set_plafon;
    public $konfig_kelompok_tindakan;
    public $konfig_kelompok_select_all;
    public $is_print_automatic;
    public $is_set_tindakan;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'sync_url', 
                'start_antrian', 
                'mail_protocol', 
                'smtp_port', 
                'mail_parameter', 
                'smtp_timeout', 
                'smtp_hostname', 
                'smtp_username', 
                'smtp_password',
                'url_print'], 'required', 'on' => 'default'],
            [[
                'konfigsystem_id', 
                'bpjs_port', 
                'satuanpembulatan', 
                'jatuhtempo_tagihan', 
                'start_antrian', 
                'rincian_tagihan', 
                'smtp_port', 
                'smtp_timeout'], 'default', 'value' => null],
            [[
                'konfigsystem_id', 
                'bpjs_port', 
                'satuanpembulatan', 
                'jatuhtempo_tagihan', 
                'start_antrian', 
                'rincian_tagihan', 
                'smtp_port', 
                'smtp_timeout', 
                'kelola_tagihan', 
                'adm_tindakan_id',
                'expired_time_program_fisio'], 'integer'
            ],
            [[
                'start_antrian_status', 
                'default_biaya', 
                'is_tgltransaksimundur', 
                'is_smsgateway', 
                'is_bayarlangsung', 
                'is_jurnalotomatis', 
                'is_postingotomatis', 
                'is_bridgingbpjs', 
                'is_akomodasiotomatis', 
                'is_pembulatankeatas',
                'is_expired_time_program_fisio',
                'is_show_obat_form_penatajasa', 
                'is_set_plafon', 
                'konfig_kelompok_select_all',
                'is_print_automatic',
                'is_set_tindakan',
                ], 'boolean'],
            [[
                'tglberlaku_dari', 
                'tglberlaku_sampai', 
                'kelas_pelayanan', 
                'pembayaran_langsung', 
                'url_print', 
                'kelola_tagihan', 
                'edit_billing', 
                'adm_persen',
                'is_validasi_pembayaran'], 'safe'],
            [['additional_data','konfig_kelompok_tindakan'], 'string'],
            [['adm_persen', 'adm_tindakan_id'], 'required','on' => 'addBiaya'],
            [['expired_time_program_fisio'], 'required', 'message' => '{attribute} Tidak Boleh Kosong', 'when' => function ($model) {
                return $model->is_expired_time_program_fisio == true;
            }],
            [['bpjs_url', 'kso_url', 'mail_protocol', 'mail_parameter'], 'string', 'max' => 255],
            [[
                'bpjs_consid', 
                'bpjs_secretkey', 
                'bpjs_ppkpelayanan', 
                'kso_consid', 
                'kso_secretkey', 
                'smtp_hostname', 
                'smtp_username', 
                'smtp_password'], 'string', 'max' => 100],
            // [['konfigsystem_id'], 'unique'],
            [['expired_time_program_fisio'], 'integer', 'min' => 1]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigsystem_id' => 'Konfigsystem ID',
            'is_tgltransaksimundur' => 'Is Tgltransaksimundur',
            'is_smsgateway' => 'Is Smsgateway',
            'is_bayarlangsung' => 'Is Bayarlangsung',
            'is_jurnalotomatis' => 'Is Jurnalotomatis',
            'is_postingotomatis' => 'Is Postingotomatis',
            'is_bridgingbpjs' => 'Is Bridgingbpjs',
            'bpjs_url' => 'Bpjs Url',
            'bpjs_consid' => 'Bpjs Consid',
            'bpjs_secretkey' => 'Bpjs Secretkey',
            'bpjs_ppkpelayanan' => 'Bpjs Ppkpelayanan',
            'bpjs_port' => 'Bpjs Port',
            'is_akomodasiotomatis' => 'Is Akomodasiotomatis',
            'is_pembulatankeatas' => 'Is Pembulatankeatas',
            'satuanpembulatan' => 'Satuanpembulatan',
            'jatuhtempo_tagihan' => 'Jatuhtempo Tagihan',
            'kso_url' => 'Kso Url',
            'kso_consid' => 'Kso Consid',
            'kso_secretkey' => 'Kso Secretkey',
            'start_antrian' => 'Mulai Antrian',
            'tglberlaku_dari' => 'Tglberlaku Dari',
            'tglberlaku_sampai' => 'Tglberlaku Sampai',
            'rincian_tagihan' => 'Rincian Tagihan',
            'additional_data' => 'Additional Data',
           
            'mail_protocol' => 'Mail Protocol',
            'mail_parameter' => 'Mail Parameter',
            'smtp_hostname' => 'SMTP Hostname',
            'smtp_username' => 'SMTP Username',
            'smtp_password' => 'SMTP Password',
            'smtp_port' => 'SMTP Port',
            'smtp_timeout' => 'SMTP Timeout',
            'sync_url' => 'Sync Url',
            'kelas_pelayanan' => 'Kelas Default Pendaftaran Selain Ranap',
            'pembayaran_langsung' => 'Tipe Pembayaran Langsung',
            'start_antrian_status' => 'Status start antrian',
            'default_biaya' => 'Default Biaya Admin RI',
            'adm_tindakan_id' => 'Daftar Tindakan',
            'adm_persen' => 'Persentase (%)',
            'persentaselb' => 'Persentase maksimal 100% dan harus lebih dari 0 (nol)',
            'is_validasi_pembayaran' => 'Notifikasi pendaftaran telah bayar',
            'expired_time_program_fisio' => 'Hitung Mundur Kedaluwarsa Program Fisioterapi',
            'is_expired_time_program_fisio' => 'Status Kedaluwarsa Program Fisioterapi',
            'is_show_obat_form_penatajasa' => 'Datagrid BMHP Penata Jasa',
            'is_set_plafon' => 'UI Tampilan Plafon Edit Tagihan',
            'is_print_automatic' => 'Cetak Tracer Otomatis',
            'is_set_tindakan' => 'Master Tindakan Enabled/Disabled Edit',
        ];
    }
}
