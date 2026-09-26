<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "nilaipemeriksaanlab_v".
 *
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pemeriksaanlab_id
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property string $nama_sample
 * @property string $nama_pasien
 * @property string $jeniskelamin
 * @property string $umur
 * @property int $ambilsample_id
 */
class NilaiPemeriksaanLabView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nilaipemeriksaanlab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'pemeriksaanlab_id', 'daftartindakan_id', 'ambilsample_id'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'pemeriksaanlab_id', 'daftartindakan_id', 'ambilsample_id'], 'integer'],
            [['jenispemeriksaanlab_nama', 'umur'], 'string', 'max' => 30],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['nama_sample'], 'string', 'max' => 100],
            [['nama_pasien'], 'string', 'max' => 50],
            [['jeniskelamin'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'nama_sample' => 'Nama Sample',
            'nama_pasien' => 'Nama Pasien',
            'jeniskelamin' => 'Jeniskelamin',
            'umur' => 'Umur',
            'ambilsample_id' => 'Ambilsample ID',
        ];
    }
}
