<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-31 14:59:30
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-31 15:03:42
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisobatalkes_m".
 *
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_kode
 * @property string $jenisobatalkes_nama
 * @property string $jenisobatalkes_namalain
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
 * @property DiskonobatpenjaminM[] $diskonobatpenjaminMs
 * @property JenisobatalkesrekM[] $jenisobatalkesrekMs
 * @property ObatalkesM[] $obatalkesMs
 * @property ObatalkespenjaminM[] $obatalkespenjaminMs
 */
class JenisObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenisobatalkes_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisobatalkes_kode', 'jenisobatalkes_nama'], 'required'],
            [['jenisobatalkes_kode', 'jenisobatalkes_nama', 'jenisobatalkes_namalain', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_kode' => 'Jenisobatalkes Kode',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'jenisobatalkes_namalain' => 'Jenisobatalkes Namalain',
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
    public function getDiskonobatpenjaminMs()
    {
        return $this->hasMany(DiskonobatpenjaminM::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisobatalkesrekMs()
    {
        return $this->hasMany(JenisobatalkesrekM::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkesMs()
    {
        return $this->hasMany(ObatalkesM::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkespenjaminMs()
    {
        return $this->hasMany(ObatalkespenjaminM::className(), ['jenisobatalkes_id' => 'jenisobatalkes_id']);
    }
}
