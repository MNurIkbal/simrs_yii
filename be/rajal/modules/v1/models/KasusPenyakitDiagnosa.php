<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 13:38:01
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-10 11:29:58
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitdiagnosa_mp".
 *
 * @property int $jeniskasuspenyakit_id
 * @property int $diagnosa_id
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
 * @property Diagnosa $diagnosa
 * @property JenisKasusPenyakit $jeniskasuspenyakit
 */
class KasusPenyakitDiagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitdiagnosa_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'diagnosa_id'], 'required'],
            [['jeniskasuspenyakit_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jeniskasuspenyakit_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['jeniskasuspenyakit_id', 'diagnosa_id'], 'unique', 'targetAttribute' => ['jeniskasuspenyakit_id', 'diagnosa_id']],
            [['diagnosa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Diagnosa::className(), 'targetAttribute' => ['diagnosa_id' => 'diagnosa_id']],
            [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisKasusPenyakit::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'diagnosa_id' => 'Diagnosa ID',
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
    public function getDiagnosa()
    {
        return $this->hasOne(Diagnosa::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }
}
