<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\InstruksiTindakan;
/**
 * This is the model class for table "infopasienri_v".
 *
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property int $dokter_pendaftaran_id
 * @property int $dokter_admisi_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $klsrawat
 * @property int $kelaspelayanan_id
 * @property int $ruangan_id
 * @property string $tgl_admisi
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $jenis_kelamin
 * @property string $dokter_pendaftaran
 * @property string $dokter_admisi
 * @property int $hak_kelas
 * @property string $kelas_pelayanan
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $tgl_pindahkamar
 * @property string $tanggal_lahir
 * @property string $tgl_pulang
 * @property string $rencana_pulang
 * @property string $umur
 * @property string $jeniskasuspenyakit_id
 * @property InstruksiTindakan[] $InstruksiTindakan
 */
class InfoPasienRanap extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienri_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'hak_kelas', 'diagnosa_mana'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'hak_kelas'], 'integer'],
            [['tgl_admisi', 'tgl_pindahkamar', 'tanggal_lahir', 'tgl_pulang', 'rencana_pulang','jeniskasuspenyakit_id', 'diagnosa_nama'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_pendaftaran', 'dokter_admisi', 'kelas_pelayanan', 'ruangan_nama'], 'string', 'max' => 50],
            [['jenis_kelamin', 'carabayar_nama', 'penjamin_nama', 'umur'], 'string', 'max' => 200],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
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
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokter_pendaftaran_id' => 'Dokter Pendaftaran ID',
            'dokter_admisi_id' => 'Dokter Admisi ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_nama' => 'Carabayar',
            'penjamin_nama' => 'Penjamin',
            'klsrawat' => 'Klsrawat',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_admisi' => 'Tgl Admisi',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'dokter_pendaftaran' => 'Dokter Pendaftaran',
            'dokter_admisi' => 'Dokter Admisi',
            'hak_kelas' => 'Hak Kelas',
            'kelas_pelayanan' => 'Kelas Pelayanan',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'tgl_pulang' => 'Tgl Pulang',
            'tanggal_lahir' => 'Tgl Lahir',
            'umur' => 'Umur',
            'rencana_pulang' => 'Rencana Pulang',
            'diagnosa_nama' => 'diagnosa nama'
        ];
    }

    public static function primaryKey() {
        return ['pasienadmisi_id'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermintaanKonsul()
    {
        return $this->hasMany(PermintaanKonsul::className(), ['pasienadmisi_id' => 'pasienadmisi_id']);
    }

    public function getInstruksiTindakanTransaksi()
    {
        return $this->hasMany(InstruksiTindakan::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }
}
