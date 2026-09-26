<?php

namespace app\modules\v1\payload;


use Doco\components\DocoBaseModel;

class InstruksiDpjp extends DocoBaseModel
{
    public $pendaftaran_id;
    public $ruangan_id;
    public $instruksi;
    public $pemberi_instruksi_id;
    public $fee_konsul;
    public $kamarruangan_id;
    public $kamartempattidur_id;
    public $kamar_tempattidur;

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
                'kamarruangan_id',
                'kamartempattidur_id',
                'kamar_tempattidur',
            ], 'safe'],
            [[
                'pendaftaran_id',
                'ruangan_id',
                'pemberi_instruksi_id',
                'fee_konsul',
                'kamarruangan_id',
                'kamartempattidur_id',
            ], 'integer'],
            [[
                'pendaftaran_id',
                'ruangan_id',
                'instruksi',
                'pemberi_instruksi_id',
            ], 'required']
        ];
    }

}