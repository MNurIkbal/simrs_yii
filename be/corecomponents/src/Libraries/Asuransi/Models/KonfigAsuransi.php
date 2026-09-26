<?php

namespace Doco\Libraries\Asuransi\Models;

use Yii;

class KonfigAsuransi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'konfigasuransi_k';
    }

    public function rules()
    {
        return [
            [['created_date', 'last_modified_date', 'deleted_date','provider_id'], 'safe'],
        ];
    }
}
