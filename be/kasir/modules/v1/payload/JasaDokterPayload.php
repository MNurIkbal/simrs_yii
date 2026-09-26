<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class JasaDokterPayload extends \Doco\components\DocoBaseModel
{
    public $id;
    public $kelompok;

    protected $xssProtected = [
        'kelompok',
    ];
    
    public function rules()
    {
         return [
            [[
                'id', 
                'kelompok',
            ], 'safe'],
            [[
                'id',
            ], 'integer', 'min' => 0]
        ];
    }
}
