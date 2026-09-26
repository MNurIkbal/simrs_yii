<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 16:06:30
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-03 17:20:32
 * @Description: model kasuspenyakitruangan_mp
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_mp".
 *
 * @property integer $ruangan_id
 * @property integer $jeniskasuspenyakit_id
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
 * @property RuanganM $ruangan
 */
class KasusPenyakitRuangan extends \app\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitruangan_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'jeniskasuspenyakit_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisKasusPenyakit::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
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
    public function getJenisKasusPenyakit()
    {
        return $this->hasOne(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function extraFields()
    {
        return [
            'jeniskasuspenyakit_m' => function($item){
                return $item->jenisKasusPenyakit;
            },
            'ruangan_m' => function($item){
                return $item->ruangan;
            }
        ];
    }
}
