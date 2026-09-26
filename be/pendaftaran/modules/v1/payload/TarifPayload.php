<?php

namespace app\modules\v1\payload;

use Yii;

class TarifPayload extends \yii\base\Model
{
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $kelompoktindakan_id;
    public $dokter_id;

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
                'kelompoktindakan_id',
                'dokter_id',
            ],'safe'],
            [[
                'ruangan_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'kelompoktindakan_id',
            ],'required'],
            [[
                'ruangan_id', 
                'kelaspelayanan_id', 
                'penjamin_id',
                'kelompoktindakan_id',
                'dokter_id',
            ], 'integer'],
        ];
    }

}
