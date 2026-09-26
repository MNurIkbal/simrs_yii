<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kategoritindakan_m".
 *
 * @property int $kategoritindakan_id
 * @property string $kategoritindakan_nama
 * @property string $kategoritindakan_namalainnya
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
 * @property DaftartindakanM[] $daftartindakanMs
 */
class KategoriTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kategoritindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kategoritindakan_nama', 'kategoritindakan_namalainnya'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kategoritindakan_nama' => 'Kategoritindakan Nama',
            'kategoritindakan_namalainnya' => 'Kategoritindakan Namalainnya',
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
    public function getDaftartindakanMs()
    {
        return $this->hasMany(DaftartindakanM::className(), ['kategoritindakan_id' => 'kategoritindakan_id']);
    }

    public function listKategoriTindakan()
    {
        $query = self::find()->where(['is_active' => 1])->all();

        return $query;
    }
}
