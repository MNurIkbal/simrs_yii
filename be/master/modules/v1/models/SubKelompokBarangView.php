<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "subkelompokbarang_v".
 *
 * @property int $subkelompokbarang_id
 * @property int $kelompokbarang_id
 * @property string $kelompokbarang_nama
 * @property string $subkelompok_kode
 * @property string $subkelompok_nama
 * @property string $subkelompok_namalain
 * @property bool $is_active
 * @property bool $is_deleted
 */
class SubKelompokBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subkelompokbarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subkelompokbarang_id', 'kelompokbarang_id'], 'default', 'value' => null],
            [['subkelompokbarang_id', 'kelompokbarang_id'], 'integer'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['kelompokbarang_nama', 'subkelompok_nama'], 'string', 'max' => 100],
            [['subkelompok_kode'], 'string', 'max' => 50],
            [['subkelompok_namalain'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subkelompokbarang_id' => 'Subkelompokbarang ID',
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'kelompokbarang_nama' => 'Kelompokbarang Nama',
            'subkelompok_kode' => 'Subkelompok Kode',
            'subkelompok_nama' => 'Subkelompok Nama',
            'subkelompok_namalain' => 'Subkelompok Namalain',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
