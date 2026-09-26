<?php

namespace app\modules\v1\payload;

use Yii;

class TarifPayloadTindakan extends \yii\base\Model
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
