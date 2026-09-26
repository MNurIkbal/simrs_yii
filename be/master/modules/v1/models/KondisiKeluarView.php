<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kondisikeluar_v".
 *
 * @property int $kondisikeluar_id
 * @property int $carakeluar_id
 * @property string $carakeluar_kode
 * @property string $carakeluar_nama
 * @property string $kondisikeluar_nama
 * @property string $kondisikeluar_namalain
 * @property int $carakeluar_urutan
 * @property string $catatan
 * @property bool $is_active
 */
class KondisiKeluarView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kondisikeluar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kondisikeluar_id', 'carakeluar_id', 'carakeluar_urutan'], 'default', 'value' => null],
            [['kondisikeluar_id', 'carakeluar_id', 'carakeluar_urutan'], 'integer'],
            [['catatan'], 'string'],
            [['is_active'], 'boolean'],
            [['carakeluar_kode', 'carakeluar_nama', 'kondisikeluar_nama', 'kondisikeluar_namalain'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_kode' => 'Carakeluar Kode',
            'carakeluar_nama' => 'Carakeluar Nama',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'kondisikeluar_namalain' => 'Kondisikeluar Namalain',
            'carakeluar_urutan' => 'Carakeluar Urutan',
            'catatan' => 'Catatan',
            'is_active' => 'Is Active',
        ];
    }
}
