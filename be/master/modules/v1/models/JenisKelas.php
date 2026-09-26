<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jeniskelas_m".
 *
 * @property integer $jeniskelas_id
 * @property string $jeniskelas_nama
 * @property string $jeniskelas_namalainnya
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
 *
 * @property KelaspelayananM[] $kelaspelayananMs
 */
class JenisKelas extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jeniskelas_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskelas_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskelas_nama', 'jeniskelas_namalainnya'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskelas_id' => 'Jeniskelas ID',
            'jeniskelas_nama' => 'Jeniskelas Nama',
            'jeniskelas_namalainnya' => 'Jeniskelas Namalainnya',
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
    public function getKelaspelayananMs()
    {
        return $this->hasMany(KelaspelayananM::className(), ['jeniskelas_id' => 'jeniskelas_id']);
    }
}
