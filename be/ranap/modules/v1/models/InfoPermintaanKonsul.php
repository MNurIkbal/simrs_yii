<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienri_v".
 *
 * @property int $pendaftaran_id
 * @property int $dokter_pendaftaran_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $kelaspelayanan_id
 * @property int $ruangan_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $jenis_kelamin
 * @property string $dokter_pendaftaran
 * @property string $dokter_admisi
 * @property string $kelas_pelayanan
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $tgl_pindahkamar
 * @property string $tanggal_lahir
 * @property string $tgl_pulang
 * @property string $rencana_pulang
 * @property string $jeniskasuspenyakit_id
 */
class InfoPermintaanKonsul extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopermintaankonsul_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaankonsul_id','dok_dpjp_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'dokter_id'], 'default', 'value' => null],
            [['permintaankonsul_id','dok_dpjp_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat_id', 'ruangan_id', 'dokter_id'], 'integer'],
            [['tgl_admisi', 'tgl_pindahkamar', 'tanggal_lahir', 'tgl_pulang', 'rencana_pulang','jeniskasuspenyakit_id'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_pendaftaran', 'dokter_admisi', 'kelas_pelayanan', 'ruangan_nama'], 'string', 'max' => 50],
            [['jenis_kelamin', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 200],
            [['jenis_konsul'], 'string', 'max' => 100],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'waktu_permintaan' => 'Waktu Permintaan',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'dok_dpjp_id' => 'Dokter DPJP ID',
            'dok_dpjp' => 'Dokter DPJP',
            'carabayar_id' => 'Cara Bayar ID',
            'carabayar_nama' => 'Nama Cara Bayar',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin',
            'kls_rawat_id' => 'Kelas Rawat ID',
            'kls_rawat' => 'Kelas Rawat',
            'kls_hak_id' => 'Kelas Hak ID',
            'kls_hak' => 'Kelas Hak',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'jenis_konsul' => 'Jenia Konsul',
            'dokter_id' => 'Dokter ID',
            'dok_konsul' => 'Dokter Konsul',
            'status_konsul' => 'Status Dokter',
            'status_konsul_nama' => 'Nama Status Dokter',
        ];
    }
}
