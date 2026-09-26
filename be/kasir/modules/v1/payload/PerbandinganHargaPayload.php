<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class PerbandinganHargaPayload extends \Doco\components\DocoBaseModel
{
    public $kelaspelayanan_id;
    public $pendaftaran_id;

    public function rules()
    {
         return [
            [[
                'kelaspelayanan_id', 
                'pendaftaran_id', 
            ], 'required','message'=>'{attribute} Tidak Boleh Kosong'],
            [[
                'pendaftaran_id',
                'kelaspelayanan_id'
            ], 'safe'],
            [[
                'pendaftaran_id',
                'kelaspelayanan_id'
            ], 'integer']
        ];
    }
}
