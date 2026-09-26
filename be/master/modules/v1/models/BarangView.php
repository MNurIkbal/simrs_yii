<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "barang_v".
 *
 * @property int $barang_id
 * @property int $golonganbarang_id
 * @property string $golonganbarang_nama
 * @property int $kelompokbarang_id
 * @property string $kelompokbarang_nama
 * @property int $subkelompokbarang_id
 * @property string $subkelompok_nama
 * @property string $barang_nama
 * @property bool $is_active
 * @property bool $is_deleted
 */
class BarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_id', 'golonganbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id'], 'default', 'value' => null],
            [['barang_id', 'golonganbarang_id', 'kelompokbarang_id', 'subkelompokbarang_id'], 'integer'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['golonganbarang_nama'], 'string', 'max' => 200],
            [['kelompokbarang_nama', 'subkelompok_nama', 'barang_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'barang_id' => 'Barang ID',
            'golonganbarang_id' => 'Golonganbarang ID',
            'golonganbarang_nama' => 'Golonganbarang Nama',
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'kelompokbarang_nama' => 'Kelompokbarang Nama',
            'subkelompokbarang_id' => 'Subkelompokbarang ID',
            'subkelompok_nama' => 'Subkelompok Nama',
            'barang_nama' => 'Barang Nama',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
