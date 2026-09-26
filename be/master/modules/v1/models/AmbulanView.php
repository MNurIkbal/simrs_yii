<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ambulan_v".
 *
 * @property int $ambulan_id
 * @property int $barang_id
 * @property string $barang_nama
 * @property string $barang_merk
 * @property string $no_polisi
 * @property string $is_emergency
 * @property string $keterangan
 * @property bool $is_active
 * @property int $status
 * @property string $lookup_name
 */
class AmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ambulan_id', 'barang_id', 'status'], 'default', 'value' => null],
            [['ambulan_id', 'barang_id', 'status'], 'integer'],
            [['is_emergency', 'keterangan'], 'string'],
            [['is_active'], 'boolean'],
            [['barang_nama'], 'string', 'max' => 100],
            [['barang_merk'], 'string', 'max' => 50],
            [['no_polisi'], 'string', 'max' => 20],
            [['lookup_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ambulan_id' => 'Ambulan ID',
            'barang_id' => 'Barang ID',
            'barang_nama' => 'Barang Nama',
            'barang_merk' => 'Barang Merk',
            'no_polisi' => 'No Polisi',
            'is_emergency' => 'Is Emergency',
            'keterangan' => 'Keterangan',
            'is_active' => 'Is Active',
            'status' => 'Status',
            'lookup_name' => 'Lookup Name',
        ];
    }
}
