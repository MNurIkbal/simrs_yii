<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "asuransi_t".
 *
 * @property int $asuransi_id
 * @property int $provider_id
 * @property int $penjamin_id
 * @property string $created_date
 * @property int $created_by
 * @property string $additional_data
 * @property string $additional_pendaftaran
 * @property int $record_status
 */

class AsuransiT extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'asuransi_t';
    }
}
