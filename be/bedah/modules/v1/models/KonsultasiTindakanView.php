<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi7_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $konsultasitindakan_id
 * @property string $bagian_tubuh
 * @property int $dokter_id
 * @property string $nama_pegawai
 * @property string $alasan
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 */
class KonsultasiTindakanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi7_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'konsultasitindakan_id', 'dokter_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'konsultasitindakan_id', 'dokter_id', 'daftartindakan_id'], 'integer'],
            [['bagian_tubuh', 'alasan'], 'string', 'max' => 255],
            [['nama_pegawai'], 'string', 'max' => 50],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'konsultasitindakan_id' => 'Konsultasitindakan ID',
            'bagian_tubuh' => 'Bagian Tubuh',
            'dokter_id' => 'Dokter ID',
            'nama_pegawai' => 'Nama Pegawai',
            'alasan' => 'Alasan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
        ];
    }
}
