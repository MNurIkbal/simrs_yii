<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "daftartindakan_v".
 *
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property bool $is_active
 */
class DaftartindakanV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daftartindakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'kelompoktindakan_id'], 'default', 'value' => null],
            [['daftartindakan_id', 'kelompoktindakan_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['kelompoktindakan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'is_active' => 'Is Active',
        ];
    }
}
