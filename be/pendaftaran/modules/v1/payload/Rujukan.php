<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;


class Rujukan extends \Doco\components\DocoBaseModel
{
    public $asalrujukan_id;
    public $rujukandari_id;
    public $diagnosa_id;
    public $no_rujukan;
    public $nama_perujuk;
    public $tanggal_rujukan;
    public $kodediagnosa_rujukan;
    public $is_rujukan;

    protected $xssProtected = [
        'tanggal_rujukan', 
        'diagnosa_id',
        'asalrujukan_id',
        'rujukandari_id',
        'no_rujukan',
        'nama_perujuk'
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [[
            //     'rujukandari_id', 
            //     'asalrujukan_id',
            //     'no_rujukan', 
            //     'nama_perujuk'
            // ], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['rujukandari_id', 'diagnosa_id'], 'default', 'value' => null],
            [[
                'tanggal_rujukan', 
                'diagnosa_id',
                'asalrujukan_id',
                'rujukandari_id',
                'no_rujukan',
                'nama_perujuk'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asalrujukan_id' => 'Asal rujukan',
            'rujukandari_id' => 'Rujukan dari',
            'diagnosa_id' => 'Diagnosa',
            'no_rujukan' => 'Nomor rujukan',
            'nama_perujuk' => 'Nama perujuk',
            'tanggal_rujukan' => 'Tanggal rujukan',
        ];
    }
}
