<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\kasir\models;

use Yii;

class KwitansiGabungForm extends \app\components\DocoBaseModel
{

    public $invoicegabung_id;
    public $diterima_dari;
    public $keterangan;
    public $jenis_kwitansi; 
    public $invoiceGabungIdEncrypt;
    protected $xssProtected = [
        'diterima_dari',
        'keterangan'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [[
                'jenis_kwitansi',
                'invoicegabung_id',
            ], 'required'],
            [[
                'invoicegabung_id',
                'diterima_dari',
                'keterangan',
                'invoiceGabungIdEncrypt'
            ],'safe']
        ];
    }
}
