<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\kasir\models;

use Yii;

class CetakInvoiceForm extends \app\components\DocoBaseModel
{

    public $pembayaranpelayanan_id;
    public $pembayaran_id;
    public $jenis_invoice; 
    public $kelompok;
    public $detailInvoice;

    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [[
                'pembayaranpelayanan_id',
                'pembayaran_id',
                'jenis_invoice'
            ], 'required'],
            [[
                'pembayaranpelayanan_id',
                'pembayaran_id',
                'jenis_invoice',
                'kelompok',
                'detailInvoice'
            ],'safe'],
        ];
    }
}
