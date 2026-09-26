<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakanspesialis_mp".
 *
 * @property int $spesialis_id
 * @property int $daftartindakan_id
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
 * @property DaftartindakanM $daftartindakan
 * @property SpesialisM $spesialis
 */
class TindakanSpesialis extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tindakanspesialis_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spesialis_id', 'daftartindakan_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['spesialis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id', 'spesialis_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['daftartindakan_id'], 'chkUnique'],
        ];
    }

    public function chkUnique($params, $attributes)
    {
        $model = self::find()->where(['daftartindakan_id' => $this->daftartindakan_id, 'spesialis_id' => $this->spesialis_id, 'is_deleted' => false])->one();
        if (!empty($model)) {
            $this->addError("daftartindakan_id", "Daftar Tindakan Sudah Didaftarkan");
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
            'spesialis_id' => 'Spesialis ID',
            'daftartindakan_id' => 'Daftartindakan ID',
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
    public function getDaftartindakan()
    {
        return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpesialis()
    {
        return $this->hasOne(Spesialis::className(), ['spesialis_id' => 'spesialis_id']);
    }
}
