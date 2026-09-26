<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rinciankelompoktindakan_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property int $kelompoktindakan_nama
 * @property int $total
 */
class SummaryKelompokTindakanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'summarykelompoktindakan_v';
    }
}
