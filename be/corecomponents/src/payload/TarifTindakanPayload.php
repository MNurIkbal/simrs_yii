<?php

namespace Doco\payload;

use Yii;
use Doco\components\DocoBaseModel;

class TarifTindakanPayload extends DocoBaseModel
{
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $instalasi_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'ruangan_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'instalasi_id',
            ],'safe'],
            [[
                'ruangan_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'instalasi_id',
            ],'required'],
            [[
                'ruangan_id', 
                'kelaspelayanan_id', 
                'penjamin_id',
                'instalasi_id',
            ], 'integer'],
        ];
    }

}
