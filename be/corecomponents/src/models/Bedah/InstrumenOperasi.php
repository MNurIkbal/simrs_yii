<?php

namespace Doco\models\Bedah;

use Yii;

class InstrumenOperasi extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'instrumenoperasi_t';
    }

    public function rules()
    {
        return [
            [
                ['pasienmasukpenunjang_id', 'obatalkes_id', 'satuan_id', 'persediaan', 'tambahan', 'terpakai', 'sisa', 'is_deleted', 'is_active', 'created_by'], 'safe'
            ]
        ];
    }
}
