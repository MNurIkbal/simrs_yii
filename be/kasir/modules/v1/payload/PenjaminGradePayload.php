<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class PenjaminGradePayload extends \Doco\components\DocoBaseModel
{
    public $penjamin_id;

    public function rules()
    {
         return [


            
            [[
                'penjamin_id',
            ], 'safe'],
            [[
                'penjamin_id',
            ], 'integer'],
            [[
                'penjamin_id',
            ], 'required'],
        ];
    }
}
