<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class BslPayload extends \Doco\components\DocoBaseModel
{
    public $SyncIdApi;

    public function rules()
    {
         return [
            [[
                'SyncIdApi', 
            ], 'safe'],
            [['SyncIdApi'], 'required']
        ];
    }
}