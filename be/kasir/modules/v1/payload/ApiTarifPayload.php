<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class ApiTarifPayload extends \Doco\components\DocoBaseModel
{
    public $ruangan_id;
    public $penjamin_id;
    public $kelaspelayanan_id;
    public $keyword;

    public function rules()
    {
         return [
            [[
                'ruangan_id', 
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'ruangan_id',
                'penjamin_id',
                'kelaspelayanan_id'
            ], 'safe'],
            [[
                'ruangan_id',
                'penjamin_id',
                'kelaspelayanan_id'
            ], 'integer'],
            [['keyword'], 'string', 'max' => 20],
        ];
    }
}
