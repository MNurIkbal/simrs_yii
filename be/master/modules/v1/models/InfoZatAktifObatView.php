<?php

namespace app\modules\v1\models;


/**
 * This is the model class for table "infozataktifobat_v".
 *
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $zataktif_id
 * @property string $zataktif_nama
 * @property bool $is_primary
 */

class InfoZatAktifObatView extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'infozataktifobat_v';
    }

}

?>
