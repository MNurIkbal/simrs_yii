<?php

/**
* @author Budi
*/

namespace Doco\kasir\models;

use Yii;
use app\components\DocoBaseModel;

class PembayaranPiutangForm extends DocoBaseModel
{
    protected $xssProtected = [
        'catatan'
    ];

    public $pembayaranpiutang_id;
    public $pemberianpiutang_id;
    public $tgl_pembayaranpiutang;
    public $no_pembayaranpiutang;
    public $pendaftaran_id;
    public $pegawai_id;
    public $total_bayarpiutang;
    public $total_piutang;
    public $total_sisapiutang;
    public $catatan;
    public $jenisnontunai_id;
    public $metode_pembayaran;

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

    public static function tableName()
    {
        return 'pembayaranpiutang_t';
    }

    public function rules()
    {
        return [
            [['tgl_pembayaranpiutang', 'pendaftaran_id', 'pegawai_id', 'total_bayarpiutang', 'metode_pembayaran'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['jenisnontunai_id'], 'required', 'on' => 'non-tunai'],
            [['pendaftaran_id', 'tgl_pembayaranpiutang', 'pegawai_id', 'catatan', 'no_pembayaranpiutang', 'total_piutang', 'total_bayarpiutang', 'total_sisapiutang', 'jenisnontunai_id', 'metode_pembayaran'], 'safe'],
            [['catatan'], 'string', 'max' => 300],
            ['total_bayarpiutang', 'compare', 'compareAttribute' => 'total_sisapiutang', 'operator' => '<=','message' => 'Jumlah Piutang tidak boleh lebih besar dari Sisa Piutang'],
            ['total_bayarpiutang', 'compare', 'operator' => '>', 'compareValue' => 0, 'message' => 'Total Pembayaran harus lebih besar dari 0']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Di Approve Oleh',
            'total_bayarpiutang' => 'Jumlah Yang Dibayar',
            'total_piutang' => 'Balance Piutang',
            'tgl_pembayaranpiutang' => 'Tanggal Pembayaran',
            'pendaftaran_id' => 'No Pendaftaran',
            'jenisnontunai_id' => 'Jenis Non Tunai',
        ];
    }
}