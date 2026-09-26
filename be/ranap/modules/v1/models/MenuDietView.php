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
 * @property string $row_number
 * @property bool $is_active
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
            [['jenisdiet_id', 'makanandiet_id', 'row_number'], 'default', 'value' => null],
            [['jenisdiet_id', 'makanandiet_id', 'row_number'], 'integer'],
            [['makanandiet_keterangan'], 'string'],
            [['is_active'], 'boolean'],
            [['jenisdiet_nama'], 'string', 'max' => 50],
            [['makanandiet_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdiet_id' => 'Jenisdiet ID',
            'jenisdiet_nama' => 'Jenisdiet Nama',
            'makanandiet_id' => 'Makanandiet ID',
            'makanandiet_nama' => 'Makanandiet Nama',
            'makanandiet_keterangan' => 'Makanandiet Keterangan',
            'row_number' => 'Row Number',
            'is_active' => 'Is Active',
        ];
    }
}
