<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tipediskondetail_m".
 *
 * @property int $tipediskon_id
 * @property int $jenislayanan_id
 * @property int $layanan_id
 * @property int $is_ditagihkan
 * @property int $disc_persen
 * @property int $max_dijamin
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
class TipeDiskonDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipediskondetail_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipediskon_id', 'jenislayanan_id'], 'required'],
            [['tipediskon_id', 'jenislayanan_id', 'layanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'disc_persen', 'max_dijamin', 'is_deleted', 'is_active'], 'safe'],
        ];
    }

    
}
