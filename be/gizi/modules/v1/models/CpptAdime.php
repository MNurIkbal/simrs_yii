<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

class CpptAdime extends DocoActiveRecord {
    public static function tableName()
    {
        return 'cpptadime_t';
    }

    public function rules()
    {
        return [
        	[['pendaftaran_id', 'pasienadmisi_id', 'pagt_id', 'asesmen_gizi', 'diagnosa_gizi',
        		'intervensi_gizi', 'monitoring', 'evaluasi', 'suggestion',
            ],'safe']
        ];
    }
}
