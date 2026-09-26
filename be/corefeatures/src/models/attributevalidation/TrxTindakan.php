<?php

namespace SirsCore\models\attributevalidation;

use Yii;

class TrxTindakan extends \yii\base\Model
{
    public $primary_key;
    public $pendaftaran_id;
    public $pasien_id;
    public $penjamin_id;
    public $carabayar_id;
    public $pasienadmisi_id;
    public $kelaspelayanan_id;

    public function rules()
    {
        return [
            [[
                'primary_key','pendaftaran_id','pasien_id','penjamin_id','carabayar_id'
            ], 'required'],
            [['pasienadmisi_id','kelaspelayanan_id'], 'default', 'value' => null],
            [['primary_key','pendaftaran_id','pasien_id','penjamin_id','carabayar_id','pasienadmisi_id','kelaspelayanan_id'],'safe']
        ];
    }
}