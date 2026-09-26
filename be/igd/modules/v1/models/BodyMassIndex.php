<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bodymassindex_m".
 *
 * @property int $bodymassindex_id
 * @property string $bmi_range
 * @property double $bmi_minimum
 * @property double $bmi_maksimum
 * @property string $bmi_sign
 * @property string $bmi_defenisi
 * @property string $bmi_pesan
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
class BodyMassIndex extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bodymassindex_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bmi_range', 'bmi_minimum', 'bmi_maksimum', 'bmi_defenisi'], 'required'],
            [['bmi_minimum', 'bmi_maksimum'], 'number'],
            [['bmi_defenisi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bmi_range'], 'string', 'max' => 50],
            [['bmi_sign'], 'string', 'max' => 2],
            [['bmi_pesan'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bodymassindex_id' => 'Bodymassindex ID',
            'bmi_range' => 'Bmi Range',
            'bmi_minimum' => 'Bmi Minimum',
            'bmi_maksimum' => 'Bmi Maksimum',
            'bmi_sign' => 'Bmi Sign',
            'bmi_defenisi' => 'Bmi Defenisi',
            'bmi_pesan' => 'Bmi Pesan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
