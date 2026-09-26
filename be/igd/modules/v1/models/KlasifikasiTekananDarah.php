<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klasifikasitekanandarah_m".
 *
 * @property int $klasifikasitekanadarah_id
 * @property string $klasifikasitekanadarah
 * @property int $sistolik_min
 * @property int $sistolik_max
 * @property int $diastolik_min
 * @property int $diastolik_max
 * @property string $kondisi_logic
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
class KlasifikasiTekananDarah extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'klasifikasitekanandarah_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasitekanadarah', 'sistolik_min', 'sistolik_max', 'diastolik_min', 'diastolik_max'], 'required'],
            [['sistolik_min', 'sistolik_max', 'diastolik_min', 'diastolik_max', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'urutan'], 'default', 'value' => null],
            [['sistolik_min', 'sistolik_max', 'diastolik_min', 'diastolik_max', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['klasifikasitekanadarah'], 'string', 'max' => 100],
            [['kondisi_logic'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'klasifikasitekanadarah_id' => 'Klasifikasitekanadarah ID',
            'klasifikasitekanadarah' => 'Klasifikasitekanadarah',
            'sistolik_min' => 'Sistolik Min',
            'sistolik_max' => 'Sistolik Max',
            'diastolik_min' => 'Diastolik Min',
            'diastolik_max' => 'Diastolik Max',
            'kondisi_logic' => 'Kondisi Logic',
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
