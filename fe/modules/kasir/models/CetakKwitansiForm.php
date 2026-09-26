<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\kasir\models;

use Yii;

class CetakKwitansiForm extends \app\components\DocoBaseModel
{

    public $pembayaranpelayanan_id;
    public $pembayaran_id;
    public $jenis_kwitansi; 
    public $diterima_dari;
    public $keterangan;
    public $penjamin_id;

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
            ], 'required', 'message' => '{attribute} belum dipilih!'],
            ['jenis_kwitansi', 'each', 'rule' => ['integer', 'skipOnEmpty' => false]],
            [[
                'pembayaranpelayanan_id',
                'pembayaran_id',
                'jenis_kwitansi',
                'diterima_dari',
                'keterangan',
                'penjamin_id'
            ],'safe']
        ];
    }
}
