<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitobat_mp".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property integer $obatalkes_id
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
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property ObatalkesM $obatalkes
 */
class KasusPenyakitObat extends \app\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitobat_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'obatalkes_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisKasusPenyakit::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            [['obatalkes_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObatAlkes::className(), 'targetAttribute' => ['obatalkes_id' => 'obatalkes_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'obatalkes_id' => 'Obatalkes ID',
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
    public function getJeniskasuspenyakit()
    {
        return $this->hasOne(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    public static function primaryKey()
    {
        return ['jeniskasuspenyakit_id', 'obatalkes_id'];
    }
}
