<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "barang_v".
 *
 * @property int $barang_id
 * @property int $golonganbarang_id
 * @property string $golonganbarang_nama
 * @property int $kelompokbarang_id
 * @property string $kelompokbarang_nama
 * @property int $subkelompokbarang_id
 * @property string $subkelompok_nama
 * @property string $barang_nama
 * @property bool $is_active
 * @property bool $is_deleted
 */
class BarangHistoryView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'baranghistory_v';
    }
}
