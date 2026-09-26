<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "inforakobat_v".
 *
 * @property int $rakobat_id
 * @property string $rokobat_nama
 * @property string $ruangan_nama
 * @property int $ruangan_id
 * @property string $status
 * @property bool $is_active
 */

class InfoRakObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforakobat_v';
    }
}



?>