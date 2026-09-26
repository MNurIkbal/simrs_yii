<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class EditTindakanPayload extends \Doco\components\DocoBaseModel
{
    public $no_pendaftaran;
    public $detail_tindakan = [];
    public $tindakanpelayanan_id;

    public function rules()
    {
         return [
            [[
                'no_pendaftaran', 
                'detail_tindakan',
                'tindakanpelayanan_id'
            ], 'required'],
            [[
                'no_pendaftaran',
                'detail_tindakan',
                'tindakanpelayanan_id'
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakan Pelayanan ID',
        ];
    }
}
