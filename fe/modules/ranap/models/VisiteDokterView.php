<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-09 14:23
 */
namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "visitedokter_v".
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
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
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
 * @property string $tgl_pindahkamar
 * @property string $r_alergiobat
 * @property bool $is_hamil
 * @property string $sumber_info
 * @property string $sumber_hubungan
 * @property double $luas_permukaantubuh
 * @property double $tinggi_badan
 * @property double $berat_badan
 * @property string $tgl_cppt
 */
class VisiteDokterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visitedokter_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'pasien_id', 'hak_kelas', 'status_ranap', 'jeniskasuspenyakit_id'], 'default', 'value' => null],
            [['pasienadmisi_id', 'pendaftaran_id', 'dokter_pendaftaran_id', 'dokter_admisi_id', 'carabayar_id', 'penjamin_id', 'klsrawat', 'kelaspelayanan_id', 'ruangan_id', 'pasien_id', 'hak_kelas', 'status_ranap', 'jeniskasuspenyakit_id'], 'integer'],
            [['tgl_admisi', 'tgl_pulang', 'rencana_pulang', 'tanggal_lahir', 'tgl_pindahkamar', 'tgl_cppt'], 'safe'],
            [['r_alergiobat'], 'string'],
            [['is_hamil'], 'boolean'],
            [['luas_permukaantubuh', 'tinggi_badan', 'berat_badan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_pendaftaran', 'dokter_admisi', 'kelas_pelayanan', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['jenis_kelamin', 'stat_ranap'], 'string', 'max' => 200],
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
            'pasienadmisi_id' => Yii::t('app', 'Pasienadmisi ID'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'dokter_pendaftaran_id' => Yii::t('app', 'Dokter Pendaftaran ID'),
            'dokter_admisi_id' => Yii::t('app', 'Dokter Admisi ID'),
            'carabayar_id' => Yii::t('app', 'Carabayar ID'),
            'penjamin_id' => Yii::t('app', 'Penjamin ID'),
            'klsrawat' => Yii::t('app', 'Klsrawat'),
            'kelaspelayanan_id' => Yii::t('app', 'Kelaspelayanan ID'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'tgl_admisi' => Yii::t('app', 'Tgl Admisi'),
            'no_pendaftaran' => Yii::t('app', 'No Pendaftaran'),
            'pasien_id' => Yii::t('app', 'Pasien ID'),
            'no_rekam_medik' => Yii::t('app', 'No Rekam Medik'),
            'nama_pasien' => Yii::t('app', 'Nama Pasien'),
            'jenis_kelamin' => Yii::t('app', 'Jenis Kelamin'),
            'dokter_pendaftaran' => Yii::t('app', 'Dokter Pendaftaran'),
            'dokter_admisi' => Yii::t('app', 'Dokter Admisi'),
            'hak_kelas' => Yii::t('app', 'Hak Kelas'),
            'kelas_pelayanan' => Yii::t('app', 'Kelas Pelayanan'),
            'jeniskasuspenyakit_nama' => Yii::t('app', 'Jeniskasuspenyakit Nama'),
            'ruangan_nama' => Yii::t('app', 'Ruangan Nama'),
            'kamarruangan_nokamar' => Yii::t('app', 'Kamarruangan Nokamar'),
            'no_tempattidur' => Yii::t('app', 'No Tempattidur'),
            'tgl_pulang' => Yii::t('app', 'Tgl Pulang'),
            'rencana_pulang' => Yii::t('app', 'Rencana Pulang'),
            'carabayar_nama' => Yii::t('app', 'Carabayar Nama'),
            'penjamin_nama' => Yii::t('app', 'Penjamin Nama'),
            'status_ranap' => Yii::t('app', 'Status Ranap'),
            'stat_ranap' => Yii::t('app', 'Stat Ranap'),
            'jeniskasuspenyakit_id' => Yii::t('app', 'Jeniskasuspenyakit ID'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal Lahir'),
            'umur' => Yii::t('app', 'Umur'),
            'tgl_pindahkamar' => Yii::t('app', 'Tgl Pindahkamar'),
            'r_alergiobat' => Yii::t('app', 'R Alergiobat'),
            'is_hamil' => Yii::t('app', 'Is Hamil'),
            'sumber_info' => Yii::t('app', 'Sumber Info'),
            'sumber_hubungan' => Yii::t('app', 'Sumber Hubungan'),
            'luas_permukaantubuh' => Yii::t('app', 'Luas Permukaantubuh'),
            'tinggi_badan' => Yii::t('app', 'Tinggi Badan'),
            'berat_badan' => Yii::t('app', 'Berat Badan'),
            'tgl_cppt' => Yii::t('app', 'Tgl Cppt'),
        ];
    }
}
