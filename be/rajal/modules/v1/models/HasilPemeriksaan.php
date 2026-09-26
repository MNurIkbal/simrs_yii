<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaan_m".
 *
 * @property integer $hasilpemeriksaan_id
 * @property string $hasilpemeriksaan_kode
 * @property string $hasilpemeriksaan_nama
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class HasilPemeriksaan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hasilpemeriksaan_m';
    }
}
