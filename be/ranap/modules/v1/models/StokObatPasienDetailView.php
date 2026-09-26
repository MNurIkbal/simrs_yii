<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-27 10:52:48
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-27 10:54:37
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatpasiendetail_v".
 *
 * @property int $pendaftaran_id
 * @property string $tipe_pemberian
 * @property int $stokobatpasien_id
 * @property string $nomor
 * @property int $jenisobatalkes_id
 * @property string $jenisobatalkes_nama
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $signa
 * @property int $stok_sisa
 * @property int $stok_dipakai
 */
class StokObatPasienDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokobatpasiendetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'stokobatpasien_id', 'jenisobatalkes_id', 'obatalkes_id', 'stok_sisa', 'stok_dipakai'], 'default', 'value' => null],
            [['pendaftaran_id', 'stokobatpasien_id', 'jenisobatalkes_id', 'obatalkes_id', 'stok_sisa', 'stok_dipakai'], 'integer'],
            [['tipe_pemberian', 'nomor', 'jenisobatalkes_nama', 'signa'], 'string'],
            [['nama_obat'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tipe_pemberian' => 'Tipe Pemberian',
            'stokobatpasien_id' => 'Stokobatpasien ID',
            'nomor' => 'Nomor',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'jenisobatalkes_nama' => 'Jenisobatalkes Nama',
            'obatalkes_id' => 'Obatalkes ID',
            'nama_obat' => 'Nama Obat',
            'signa' => 'Signa',
            'stok_sisa' => 'Stok Sisa',
            'stok_dipakai' => 'Stok Dipakai',
        ];
    }
}