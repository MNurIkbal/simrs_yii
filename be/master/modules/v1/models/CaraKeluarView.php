<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carakeluar_v".
 *
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $carakeluar_namalain
 * @property string $carakeluar_kode
 * @property int $carakeluar_urutan
 * @property string $catatan
 * @property bool $is_active
 */
class CaraKeluarView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carakeluar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id', 'carakeluar_urutan'], 'default', 'value' => null],
            [['carakeluar_id', 'carakeluar_urutan'], 'integer'],
            [['catatan'], 'string'],
            [['is_active'], 'boolean'],
            [['carakeluar_nama', 'carakeluar_namalain', 'carakeluar_kode'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'carakeluar_namalain' => 'Carakeluar Namalain',
            'carakeluar_kode' => 'Carakeluar Kode',
            'carakeluar_urutan' => 'Carakeluar Urutan',
            'catatan' => 'Catatan',
            'is_active' => 'Is Active',
        ];
    }
}
