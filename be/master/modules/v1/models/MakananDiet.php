<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "makanandiet_m".
 *
 * @property int $makanandiet_id
 * @property string $makanandiet_kode
 * @property int $makanandiet_id
 * @property string $makanandiet_nama
 * @property string $makanandiet_keterangan
 */
class MakananDiet extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'makanandiet_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['makanandiet_id'], 'integer'],
            [['makanandiet_kode', 'makanandiet_nama', 'makanandiet_keterangan'], 'string'],
            [['makanandiet_keterangan', 'makanandiet_nama'], 'string', 'max' => 255],
            [['makanandiet_kode'], 'string', 'max' => 100],

            [['additional_data'], 'string'],
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
            'makanandiet_id' => Yii::t('app', 'Makanan Diet ID'),
            'makanandiet_kode' => Yii::t('app', 'Kode Makanan'), 
            'makanandiet_nama' => Yii::t('app', 'Nama Makanan'),
            'makanandiet_keterangan' => Yii::t('app', 'Keterangan')
        ];
    }
}
