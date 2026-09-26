<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "paketruangan_v".
 *
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_deleted
 * @property string $created_date
 */
class PaketRuanganV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketruangan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipepaket_id', 'ruangan_id'], 'default', 'value' => null],
            [['tipepaket_id', 'ruangan_id'], 'integer'],
            [['is_active', 'is_default', 'is_deleted'], 'boolean'],
            [['created_date'], 'safe'],
            [['tipepaket_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'is_active' => 'Is Active',
            'is_default' => 'Is Default',
            'is_deleted' => 'Is Deleted',
            'created_date' => 'Created Date',
        ];
    }
}
