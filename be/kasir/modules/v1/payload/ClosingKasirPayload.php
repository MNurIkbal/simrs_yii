<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class ClosingKasirPayload extends \Doco\components\DocoBaseModel
{
    public $saldo_awal;
    public $shift_id;
    public $catatan;

    protected $xssProtected = [
        'catatan',
    ];
    
    public function rules()
    {
         return [
            [[
                'saldo_awal', 
                'shift_id',
                'catatan',
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'saldo_awal', 
                'shift_id',
                'catatan',
            ], 'safe'],
            [[
                'shift_id',
                'saldo_awal',
            ], 'integer', 'min' => 0]
        ];
    }

    public function attributeLabels()
    {
        return [
            'saldo_awal' => 'Saldo Awal',
            'shift_id' => 'Shift',
            'catatan' => 'Catatan'
        ];
    }
}
