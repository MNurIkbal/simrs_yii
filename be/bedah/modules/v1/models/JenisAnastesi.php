<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisanastesi_m".
 *
 * @property int $jenisanastesi_id
 * @property string $jenisanastesi_nama
 * @property string $jenisanastesi_namalainnya
 * @property string $jenisanastesi_teknik
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
 * @property AnastesiM[] $anastesiMs
 * @property TypeanastesiM[] $typeanastesiMs
 */
class JenisAnastesi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenisanastesi_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisanastesi_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisanastesi_nama', 'jenisanastesi_namalainnya', 'jenisanastesi_teknik'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisanastesi_id' => 'Jenisanastesi ID',
            'jenisanastesi_nama' => 'Jenisanastesi Nama',
            'jenisanastesi_namalainnya' => 'Jenisanastesi Namalainnya',
            'jenisanastesi_teknik' => 'Jenisanastesi Teknik',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnastesiMs()
    {
        return $this->hasMany(AnastesiM::className(), ['jenisanastesi_id' => 'jenisanastesi_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeanastesiMs()
    {
        return $this->hasMany(TypeanastesiM::className(), ['jenisanastesi_id' => 'jenisanastesi_id']);
    }
}
