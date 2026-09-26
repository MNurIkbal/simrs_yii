<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pegawaisubspesialis_mp".
 *
 * @property int $pegawaisubspesialis_id
 * @property int $pegawai_id
 * @property int $subspesialis_id
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
 * @property Pegawai $pegawai
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'subspesialis_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pegawai_id', 'subspesialis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'pegawai_id', 'subspesialis_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['subspesialis_id'], 'chkUnique'],
        ];
    }

    public function chkUnique($params, $attributes)
    {
        $modelCheckSubSpesialis = self::find()->where([
            'pegawai_id' => $this->pegawai_id,
            'subspesialis_id' => $this->subspesialis_id,
            'is_deleted' => false,
        ])->one();
        if (!empty($modelCheckSubSpesialis)) {
            $this->addError("subspesialis_id", "Sub Spesialis Sudah Didaftarkan");
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
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
    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai_id']);
    }
}
