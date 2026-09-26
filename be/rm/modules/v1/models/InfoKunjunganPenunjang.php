<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporankunjunganpenunjang_v".
 *
 * @property string $no_pendaftaran
 * @property string $tglmasukpenunjang
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property string $jumlah_tindakan
 * @property int instalasi_id
 * @property string instalasi_nama
 * @property string unit
 * @property int penjamin_id
 * @property string penjamin_nama
 * @property int jeniskegiatantindakan_id
 * @property string jeniskegiatantindakan_nama

 */
class InfoKunjunganPenunjang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporankunjunganpenunjang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_pendaftaran' => 'No Pendaftaran',
            'tglmasukpenunjang' => 'Tanggal Masuk Penunjang',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'unit' => 'Unit',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'jeniskegiatantindakan_id' => 'Jenis Kegiatan Tindakan ID',
            'jeniskegiatantindakan_nama' => 'Jenis Kegiatan Tindakan Nama',
            'daftartindakan_id' => 'Daftar Tindakan ID',
            'daftartindakan_nama' => 'Daftar Tindakan Nama',
            'jumlah_tindakan' => 'Jumlah Tindakan',
        ];
    }
}
