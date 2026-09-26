<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "datesync_v".
 *
 * @property int $cron_id
 * @property string $cron_nama
 * @property string $cron_date
 */
class DateSyncView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datesync_v';
    }
}