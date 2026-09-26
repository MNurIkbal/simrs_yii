<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "menudiet_v".
 *
 * @property int $jenisdiet_id
 * @property string $jenisdiet_nama
 * @property int $makanandiet_id
 * @property string $makanandiet_nama
 * @property string $makanandiet_keterangan
 */
class MenuDietView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menudiet_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisdiet_id', 'makanandiet_id'], 'default', 'value' => null],
            [['jenisdiet_id', 'makanandiet_id'], 'integer'],
            [['jenisdiet_nama', 'makanandiet_nama', 'makanandiet_keterangan'], 'string'],
            [['makanandiet_keterangan'], 'string', 'max' => 255],
            [['jenisdiet_nama','jenisdiet_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdiet_id' => Yii::t('app', 'Jenis Diet ID'),
            'jenisdiet_nama' => Yii::t('app', 'Nama jenis diet'), 
            'makanandiet_id' => Yii::t('app', 'Makanan Diet ID'),
            'makanandiet_nama' => Yii::t('app', 'Nama makanan'),
            'makanandiet_keterangan' => Yii::t('app', 'Keterangan')
        ];
    }
}
