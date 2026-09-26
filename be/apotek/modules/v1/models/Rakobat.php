<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rakobat_m".
 *
 * @property integer $rakobat_id
 * @property string $rakobat_nama
 * @property integer $ruangan_id
 * @property integer $parentrakobat_id
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
class Rakobat extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rakobat_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}
