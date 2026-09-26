<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "operasi_v".
 *
 * @property int $operasi_id
 * @property int $kegiatanoperasi_id
 * @property string $kegiatanoperasi_nama
 * @property string $operasi_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $golonganoperasi_id
 * @property string $golonganoperasi_nama
 * @property string $operasi_kode
 */
class OperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operasi_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operasi_id', 'kegiatanoperasi_id', 'daftartindakan_id', 'golonganoperasi_id'], 'default', 'value' => null],
            [['operasi_id', 'kegiatanoperasi_id', 'daftartindakan_id', 'golonganoperasi_id'], 'integer'],
            [['kegiatanoperasi_nama', 'golonganoperasi_nama'], 'string', 'max' => 100],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['operasi_kode'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'operasi_id' => 'Operasi ID',
            'kegiatanoperasi_id' => 'Kegiatanoperasi ID',
            'kegiatanoperasi_nama' => 'Kegiatanoperasi Nama',
            'operasi_nama' => 'Operasi Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'golonganoperasi_id' => 'Golonganoperasi ID',
            'golonganoperasi_nama' => 'Golonganoperasi Nama',
            'operasi_kode' => 'Operasi Kode',
        ];
    }
}
