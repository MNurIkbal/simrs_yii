<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotagihanpenunjang_v".
 *
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property string $tglmasukpenunjang
 * @property string $no_pendaftaran
 * @property string $jeniskasuspenyakit_nama
 * @property string $kelaspelayanan_nama
 * @property string $nama_pegawai
 * @property string $ruang_pendaftaran
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property double $jumlah_tagihan
 */
class InfoTagihanPenunjangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpenunjang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
            [['tgl_pendaftaran', 'tglmasukpenunjang'], 'safe'],
            [['jumlah_tagihan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['kelaspelayanan_nama', 'nama_pegawai', 'ruang_pendaftaran', 'instalasi_nama', 'ruangan_nama', 'nama_pasien', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_pendaftaran' => 'No Pendaftaran',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'nama_pegawai' => 'Nama Pegawai',
            'ruang_pendaftaran' => 'Ruang Pendaftaran',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'jumlah_tagihan' => 'Jumlah Tagihan',
        ];
    }
}
