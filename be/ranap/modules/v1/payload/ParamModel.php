<?php

namespace app\modules\v1\payload;

use Yii;


class ParamModel extends \yii\base\Model
{
    public $ruangan_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $instruksi_id;
    public $cppt_id;
    public $pendaftaran_id;
    public $group_jenis;


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
                'instruksi_id',
                'cppt_id',
                'pendaftaran_id',
                'group_jenis',
            ],'safe'],
            [[
                'ruangan_id', 
                'kelaspelayanan_id', 
                'penjamin_id',
                'instruksi_id',
                'cppt_id',
                'pendaftaran_id',
                'group_jenis',
            ], 'integer'],
        ];
    }

}
