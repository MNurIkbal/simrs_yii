<?php

namespace SirsCore\models;

use Yii;

/**
 * This is the model class for table "timoperasi_t".
 *
 * @property int $timoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $posisi_tim lookup_type='tim_operasi'
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
 */
class TimOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timoperasi_v';
    }

    /**
     * {@inheritdoc}
     */
    
}
