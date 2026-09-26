<?php

/**
* @author Budi
*/

namespace app\modules\v1\models;

use Yii;

class PembayaranPiutang extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'pembayaranpiutang_t';
    }

    public function rules()
    {
        return [
            [['tgl_pembayaranpiutang', 'pendaftaran_id', 'pegawai_id', 'total_bayarpiutang'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['jenisnontunai_id'], 'required', 'on' => 'non-tunai'],
            [['pendaftaran_id', 'tgl_pembayaranpiutang', 'pegawai_id', 'catatan', 'no_pembayaranpiutang', 'total_bayarpiutang', 'pemberianpiutang_id', 'metode_pembayaran', 'jenisnontunai_id'], 'safe'],
            [['catatan'], 'string', 'max' => 300],
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
            'tgl_pembayaranpiutang' => 'Tanggal Pembayaran',
            'pendaftaran_id' => 'No Pendaftaran',
            'jenisnontunai_id' => 'Jenis Non Tunai',
        ];
    }
}