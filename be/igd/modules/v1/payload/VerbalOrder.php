<?php

namespace app\modules\v1\payload;


use Doco\components\DocoBaseModel;

class VerbalOrder extends DocoBaseModel
{

    public $pendaftaran_id;
    public $ruangan_id;
    public $instruksi;
    public $pemberi_instruksi_id;
    public $fee_konsul;

    protected $xssProtected = [
        'instruksi'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'pendaftaran_id',
                'ruangan_id',
                'instruksi',
                'pemberi_instruksi_id',
                'fee_konsul',
            ], 'safe'],
            [[
                'pendaftaran_id',
                'ruangan_id',
                'pemberi_instruksi_id',
                'fee_konsul',
            ], 'integer'],
            [[
                'pendaftaran_id',
                'ruangan_id',
                'instruksi',
                'pemberi_instruksi_id',
                'fee_konsul',
            ], 'required']
        ];
    }

}