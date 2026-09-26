<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class BatalTagihanPayload extends \Doco\components\DocoBaseModel
{
    const PENUNJANG = 'penunjang';
    const NON_PENUNJANG = 'non_penunjang';

    public $no_masukpenunjang;
    public $no_pendaftaran;
    public $ruangan_id;
    public $detail_tindakan = [];
    public $detail_obat = [];

    public function rules()
    {
         return [
            [[
                'no_masukpenunjang', 
            ], 'required', 'on' => self::PENUNJANG],
            [[
                'no_pendaftaran', 
            ], 'required', 'on' => self::NON_PENUNJANG],

            // ['ruangan_id', 'required', 'when' => function($model) {
            //   return empty($model->detail_tindakan);
            // }],
            
            [[
                'no_masukpenunjang',
                'no_pendaftaran',
                'ruangan_id',
                'detail_tindakan',
                'detail_obat'
            ], 'safe'],
            [[
                'ruangan_id',
            ], 'integer'],
        ];
    }
}
