<?php

namespace app\modules\kasir\models;

use Yii;

class InvoiceGabungForm extends \yii\base\Model
{
    public $tgl_invoicegabung;
    public $tgl_invoicegabung_cetak;
    public $no_invoicegabung;
    public $total_invoicegabung;
    public $status_invoicegabung;
    public $cache_data;
    public $pendaftaran_id_cetak;
    public $penjamin_id_cetak;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_invoicegabung', 'cache_data', 'penjamin_id_cetak', 'pendaftaran_id_cetak'], 'required', 'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [[
                'tgl_invoicegabung', 'no_invoicegabung', 'total_invoicegabung', 'status_invoicegabung', 'tgl_invoicegabung_cetak', 'pendaftaran_id_cetak', 'penjamin_id_cetak'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl_invoicegabung' => 'Tanggal Invoice',
            'tgl_invoicegabung_cetak' => 'Tanggal Transaksi Tercetak',
            'no_invoicegabung' => 'No Invoice',
            'total_invoicegabung' => 'Total Invoice',
            'status_invoicegabung' => 'Status',
            'cache_data' => 'Detail Nomor Invoice',
            'pendaftaran_id_cetak' => 'No Pendaftaran Tercetak',
            'penjamin_id_cetak' => 'Nama Penjamin Tercetak',
        ];
    }
}
