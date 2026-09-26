<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-02 15:48:29
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-02 15:49:07
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayatpemeriksaanfisik_v".
 *
 * @property int $pemeriksaanfisik_id
 * @property int $pendaftaran_id
 * @property string $ruangan_nama
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $jenis_kelamin
 * @property string $tanggal_lahir
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $dokter
 * @property string $perawat
 * @property string $tglperiksafisik
 * @property string $keadaanumum
 * @property string $inspeksi
 * @property string $palpasi
 * @property string $perkusi
 * @property string $auskultasi
 * @property string $tekanandarah
 * @property double $meanarteripressure
 * @property int $detaknadi
 * @property string $pernapasan
 * @property string $suhutubuh
 * @property double $tinggibadan_cm
 * @property double $beratbadan_kg
 * @property double $bb_ideal
 * @property string $bmi_defenisi
 * @property string $kelainanpadabagtubuh
 * @property int $gcs_eye
 * @property int $gcs_verbal
 * @property int $gcs_motorik
 * @property bool $is_kapitis
 * @property string $gcs_nama
 * @property bool $jn_paten
 * @property bool $jn_obstruktifpartial
 * @property bool $jn_obstruktifnormal
 * @property bool $jn_stridor
 * @property bool $jn_gargling
 * @property bool $pgp_normal
 * @property bool $pgp_kussmaul
 * @property bool $pgp_takipnea
 * @property bool $pgp_dangkal
 * @property bool $pgp_retraktif
 * @property bool $pgd_simetri
 * @property bool $pgd_asimetri
 * @property int $sirkulasi_nadicarotis
 * @property int $sirkulasi_nadiradialis
 * @property bool $cfr_kecil_2
 * @property bool $cfr_besar_2
 * @property bool $kulit_normal
 * @property bool $kulit_jaundice
 * @property bool $kulit_cyanosis
 * @property bool $kulit_pucat
 * @property bool $kulit_berkeringat
 * @property string $akral
 * @property string $namabagtubuh
 * @property string $catatan_tubuh
 * @property string $bmi_range
 * @property double $bmi_minimum
 * @property double $bmi_maksimum
 * @property string $bmi_sign
 */
class RiwayatPemeriksaanFisik extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'riwayatpemeriksaanfisik_v';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pemeriksaanfisik_id', 'pendaftaran_id', 'detaknadi', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis'], 'default', 'value' => null],
			[['pemeriksaanfisik_id', 'pendaftaran_id', 'detaknadi', 'gcs_eye', 'gcs_verbal', 'gcs_motorik', 'sirkulasi_nadicarotis', 'sirkulasi_nadiradialis'], 'integer'],
			[['tanggal_lahir', 'tglperiksafisik'], 'safe'],
			[['meanarteripressure', 'tinggibadan_cm', 'beratbadan_kg', 'bb_ideal', 'bmi_minimum', 'bmi_maksimum'], 'number'],
			[['pernapasan', 'bmi_defenisi', 'catatan_tubuh'], 'string'],
			[['is_kapitis', 'jn_paten', 'jn_obstruktifpartial', 'jn_obstruktifnormal', 'jn_stridor', 'jn_gargling', 'pgp_normal', 'pgp_kussmaul', 'pgp_takipnea', 'pgp_dangkal', 'pgp_retraktif', 'pgd_simetri', 'pgd_asimetri', 'cfr_kecil_2', 'cfr_besar_2', 'kulit_normal', 'kulit_jaundice', 'kulit_cyanosis', 'kulit_pucat', 'kulit_berkeringat'], 'boolean'],
			[['ruangan_nama', 'nama_pasien', 'carabayar_nama', 'penjamin_nama', 'dokter', 'perawat', 'gcs_nama', 'bmi_range'], 'string', 'max' => 50],
			[['no_pendaftaran', 'tekanandarah'], 'string', 'max' => 20],
			[['no_rekam_medik', 'suhutubuh'], 'string', 'max' => 10],
			[['jenis_kelamin', 'akral', 'namabagtubuh'], 'string', 'max' => 200],
			[['keadaanumum', 'inspeksi', 'palpasi', 'perkusi', 'auskultasi'], 'string', 'max' => 500],
			[['kelainanpadabagtubuh'], 'string', 'max' => 30],
			[['bmi_sign'], 'string', 'max' => 2],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
			'pendaftaran_id' => 'Pendaftaran ID',
			'ruangan_nama' => 'Ruangan Nama',
			'no_pendaftaran' => 'No Pendaftaran',
			'no_rekam_medik' => 'No Rekam Medik',
			'nama_pasien' => 'Nama Pasien',
			'jenis_kelamin' => 'Jenis Kelamin',
			'tanggal_lahir' => 'Tanggal Lahir',
			'carabayar_nama' => 'Carabayar Nama',
			'penjamin_nama' => 'Penjamin Nama',
			'dokter' => 'Dokter',
			'perawat' => 'Perawat',
			'tglperiksafisik' => 'Tglperiksafisik',
			'keadaanumum' => 'Keadaanumum',
			'inspeksi' => 'Inspeksi',
			'palpasi' => 'Palpasi',
			'perkusi' => 'Perkusi',
			'auskultasi' => 'Auskultasi',
			'tekanandarah' => 'Tekanandarah',
			'meanarteripressure' => 'Meanarteripressure',
			'detaknadi' => 'Detaknadi',
			'pernapasan' => 'Pernapasan',
			'suhutubuh' => 'Suhutubuh',
			'tinggibadan_cm' => 'Tinggibadan Cm',
			'beratbadan_kg' => 'Beratbadan Kg',
			'bb_ideal' => 'Bb Ideal',
			'bmi_defenisi' => 'Bmi Defenisi',
			'kelainanpadabagtubuh' => 'Kelainanpadabagtubuh',
			'gcs_eye' => 'Gcs Eye',
			'gcs_verbal' => 'Gcs Verbal',
			'gcs_motorik' => 'Gcs Motorik',
			'is_kapitis' => 'Is Kapitis',
			'gcs_nama' => 'Gcs Nama',
			'jn_paten' => 'Jn Paten',
			'jn_obstruktifpartial' => 'Jn Obstruktifpartial',
			'jn_obstruktifnormal' => 'Jn Obstruktifnormal',
			'jn_stridor' => 'Jn Stridor',
			'jn_gargling' => 'Jn Gargling',
			'pgp_normal' => 'Pgp Normal',
			'pgp_kussmaul' => 'Pgp Kussmaul',
			'pgp_takipnea' => 'Pgp Takipnea',
			'pgp_dangkal' => 'Pgp Dangkal',
			'pgp_retraktif' => 'Pgp Retraktif',
			'pgd_simetri' => 'Pgd Simetri',
			'pgd_asimetri' => 'Pgd Asimetri',
			'sirkulasi_nadicarotis' => 'Sirkulasi Nadicarotis',
			'sirkulasi_nadiradialis' => 'Sirkulasi Nadiradialis',
			'cfr_kecil_2' => 'Cfr Kecil 2',
			'cfr_besar_2' => 'Cfr Besar 2',
			'kulit_normal' => 'Kulit Normal',
			'kulit_jaundice' => 'Kulit Jaundice',
			'kulit_cyanosis' => 'Kulit Cyanosis',
			'kulit_pucat' => 'Kulit Pucat',
			'kulit_berkeringat' => 'Kulit Berkeringat',
			'akral' => 'Akral',
			'namabagtubuh' => 'Namabagtubuh',
			'catatan_tubuh' => 'Catatan Tubuh',
			'bmi_range' => 'Bmi Range',
			'bmi_minimum' => 'Bmi Minimum',
			'bmi_maksimum' => 'Bmi Maksimum',
			'bmi_sign' => 'Bmi Sign',
		];
	}
}
