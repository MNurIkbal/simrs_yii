<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "ruanganpegawai_mp".
 *
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property PegawaiM $pegawai
 * @property RuanganM $ruangan
 */
class PegawaiSubSpesialis extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pegawaisubspesialis_mp';
    }
}
