<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LogPelayanan extends \Integrasi\Components\ActiveRepositories
{
    public static function tableName()
    {
        return 'logpelayanan_r';
    }

    public function rules()
    {
        return [
            [[
                'is_tindakan',
                'pelayanan_id',
                'hargasatuan_sebelum',
                'hargasatuan_sesudah',
                'hargacyto_sebelum',
                'hargacyto_sesudah',
                'hargapenyulit_sebelum',
                'hargapenyulit_sesudah',
                'created_date',
                'created_by',
                'is_deleted',
                'is_active',
                'verify_by',
            ],'safe'],
            [['is_deleted'], 'default', 'value'=> false],
            [['is_active'], 'default', 'value'=> true],
        ];
    }
}
