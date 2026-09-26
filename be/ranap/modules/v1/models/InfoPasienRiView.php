<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienri_v".
 *
 * @property int $pasienadmisi_id
 * @property int $pendaftaran_id
 * @property string $tgl_pendaftaran
 * @property int $dokter_pendaftaran_id
 * @property int $dokter_admisi_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $klsrawat
 * @property int $kelaspelayanan_id
 * @property int $ruangan_id
 * @property string $tgl_admisi
 * @property string $no_pendaftaran
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $alamat_pasien
 * @property string $jenis_kelamin
 * @property string $dokter_pendaftaran
 * @property string $dokter_admisi
 * @property int $hak_kelas
 * @property string $kelas_pelayanan
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $tgl_pulang
 * @property string $rencana_pulang
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property int $status_ranap
 * @property string $stat_ranap
 * @property int $jeniskasuspenyakit_id
 * @property string $tanggal_lahir
 * @property string $umur
 * @property int $golonganumur_id
 * @property string $tgl_pindahkamar
 * @property string $r_alergiobat
 * @property bool $is_hamil
 * @property string $sumber_info
 * @property string $sumber_hubungan
 * @property double $luas_permukaantubuh
 * @property double $tinggi_badan
 * @property double $berat_badan
 * @property string $r_penyakitkeluarga
 * @property string $r_imunisasi
 * @property int $diagnosa_id
 * @property int $kamarruangan_id
 * @property int $kamartempattidur_id
 * @property string $photopasien
 * @property int $caramasuk_id
 * @property string $caramasuk_nama
 * @property bool $discharge_plan
 * @property string $obatan_rumah
 * @property bool $obat_darirumah
 */
class InfoPasienRiView extends \Doco\components\DocoActiveRecord
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
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'pasien_id', 'hak_kelas', 'status_ranap', 'jeniskasuspenyakit_id', 'golonganumur_id', 'diagnosa_id', 'kamarruangan_id', 'kamartempattidur_id', 'caramasuk_id'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'pasien_id', 'hak_kelas', 'status_ranap', 'jeniskasuspenyakit_id', 'golonganumur_id', 'diagnosa_id', 'kamarruangan_id', 'kamartempattidur_id', 'caramasuk_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_admisi', 'tgl_pulang', 'rencana_pulang', 'tanggal_lahir', 'tgl_pindahkamar'], 'safe'],
            [['alamat_pasien', 'r_alergiobat', 'r_penyakitkeluarga', 'r_imunisasi', 'obatan_rumah'], 'string'],
            [['is_hamil', 'discharge_plan', 'obat_darirumah'], 'boolean'],
            [['luas_permukaantubuh', 'tinggi_badan', 'berat_badan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_pendaftaran', 'dokter_admisi', 'kelas_pelayanan', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'caramasuk_nama'], 'string', 'max' => 50],
            [['jenis_kelamin', 'stat_ranap', 'photopasien'], 'string', 'max' => 200],
            [['jeniskasuspenyakit_nama', 'sumber_info'], 'string', 'max' => 100],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur', 'sumber_hubungan'], 'string', 'max' => 255],
            [['umur'], 'string', 'max' => 30],
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
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'dokter_pendaftaran_id' => 'Dokter Pendaftaran ID',
            'dokter_admisi_id' => 'Dokter Admisi ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'klsrawat' => 'Klsrawat',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_admisi' => 'Tgl Admisi',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'alamat_pasien' => 'Alamat Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'dokter_pendaftaran' => 'Dokter Pendaftaran',
            'dokter_admisi' => 'Dokter Admisi',
            'hak_kelas' => 'Hak Kelas',
            'kelas_pelayanan' => 'Kelas Pelayanan',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'tgl_pulang' => 'Tgl Pulang',
            'rencana_pulang' => 'Rencana Pulang',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'status_ranap' => 'Status Ranap',
            'stat_ranap' => 'Stat Ranap',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'tanggal_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'golonganumur_id' => 'Golonganumur ID',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'r_alergiobat' => 'R Alergiobat',
            'is_hamil' => 'Is Hamil',
            'sumber_info' => 'Sumber Info',
            'sumber_hubungan' => 'Sumber Hubungan',
            'luas_permukaantubuh' => 'Luas Permukaantubuh',
            'tinggi_badan' => 'Tinggi Badan',
            'berat_badan' => 'Berat Badan',
            'r_penyakitkeluarga' => 'R Penyakitkeluarga',
            'r_imunisasi' => 'R Imunisasi',
            'diagnosa_id' => 'Diagnosa ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'photopasien' => 'Photopasien',
            'caramasuk_id' => 'Caramasuk ID',
            'caramasuk_nama' => 'Caramasuk Nama',
            'discharge_plan' => 'Discharge Plan',
            'obatan_rumah' => 'Obatan Rumah',
            'obat_darirumah' => 'Obat Darirumah',
        ];
    }
}
