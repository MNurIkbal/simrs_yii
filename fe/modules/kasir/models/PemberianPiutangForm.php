<?php

/**
* @author Budi
*/

namespace Doco\kasir\models;

use Yii;
use app\components\DocoBaseModel;

class PemberianPiutangForm extends DocoBaseModel
{
    protected $xssProtected = [
        'catatan'
    ];

    public $pemberianpiutang_id;
    public $tgl_pemberianpiutang;
    public $no_pemberianpiutang;
    public $pendaftaran_id;
    public $pegawai_id;
    public $total_piutang;
    public $total_sisapiutang;
    public $total_bayarpiutang;
    public $catatan;
    public $status_piutang;
    public $total_tagihan;

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
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $pegawaimengetahui_id;
    public $penjualanresep_id;
    public $no_resep;


    public function rules()
    {
        return [
            [['tgl_pemberianpiutang', 'pendaftaran_id', 'pegawai_id', 'total_piutang','catatan','pegawaimengetahui_id'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['pendaftaran_id', 'tgl_pemberianpiutang', 'no_pendaftaran', 'pegawai_id', 'catatan', 'total_sisapiutang', 'no_pemberianpiutang', 'tgl_pendaftaran', 'total_tagihan', 'pegawaimengetahui_id', 'penjualanresep_id', 'no_resep'], 'safe'],
            [['catatan'], 'string', 'max' => 300],
            ['total_piutang', 'compare', 'compareAttribute' => 'total_tagihan', 'operator' => '<=','message' => 'Jumlah Piutang tidak boleh lebih besar dari Total Tagihan'],
            ['total_piutang', 'compare', 'operator' => '>', 'compareValue' => 0, 'message' => 'Jumlah Piutang harus lebih besar dari 0']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Di Approve Oleh',
            'total_bayarpiutang' => 'Sudah Bayar',
            'total_sisapiutang' => 'Balance Piutang',
            'total_piutang' => 'Jumlah Piutang',
            'tgl_pemberianpiutang' => 'Tanggal Piutang',
            'pendaftaran_id' => 'No Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran / Nama Pasien / No Resep / No Rekam Medik',
            'pegawaimengetahui_id' => 'Nama Karyawan',
            'penjualanresep_id' => 'Penjualan Resep ID',
            'no_resep' => 'No Resep',
        ];
    }
}