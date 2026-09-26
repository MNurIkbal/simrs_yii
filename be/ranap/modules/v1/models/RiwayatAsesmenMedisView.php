<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-13 17:20:28
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayatasesmenmedis_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $asesmenmedis_id
 * @property string $keluhan_tambahan
 * @property string $r_penyakitsekarang
 * @property int $lama_sakit
 * @property string $r_penyakitdahulu
 * @property string $r_penyakitkeluarga
 * @property string $r_imunisasi
 * @property string $r_peskk
 * @property string $tgl_asesmenmedis
 * @property string $sumber_info
 * @property string $sumber_hubungan
 * @property bool $is_merokok
 * @property int $jml_rokok
 * @property string $obat_diberikan
 * @property string $r_makanan
 * @property string $r_kelahiran
 * @property string $r_alergiobat
 * @property string $keterangan
 * @property int $td_systolic
 * @property int $td_diastolic
 * @property string $tekanan_darah
 * @property string $hasil_td
 * @property double $tinggi_badan
 * @property double $berat_badan
 * @property double $bb_ideal
 * @property double $imt
 * @property string $ket_imt
 * @property int $detak_nadi
 * @property int $pernapasan
 * @property string $denyut_jantung
 * @property int $suhu_tubuh
 * @property int $gcs_eye
 * @property int $gcs_verbal
 * @property int $gcs_motorik
 * @property string $hasil_gcs
 * @property string $kontak
 * @property string $metod_asmennyeri
 */
class RiwayatAsesmenMedisView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'riwayatasesmenmedis_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'asesmenmedis_id', 'lama_sakit', 'jml_rokok', 'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'gcs_eye', 'gcs_verbal', 'gcs_motorik'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'asesmenmedis_id', 'lama_sakit', 'jml_rokok', 'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'gcs_eye', 'gcs_verbal', 'gcs_motorik'], 'integer'],
            [['keluhan_tambahan', 'r_penyakitsekarang', 'r_penyakitdahulu', 'r_penyakitkeluarga', 'r_imunisasi', 'r_peskk', 'obat_diberikan', 'r_makanan', 'r_kelahiran', 'r_alergiobat', 'keterangan'], 'string'],
            [['tgl_asesmenmedis'], 'safe'],
            [['is_merokok'], 'boolean'],
            [['tinggi_badan', 'berat_badan', 'bb_ideal', 'imt'], 'number'],
            [['sumber_info', 'tekanan_darah', 'ket_imt', 'denyut_jantung', 'hasil_gcs', 'kontak', 'metod_asmennyeri'], 'string', 'max' => 100],
            [['sumber_hubungan', 'hasil_td'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'asesmenmedis_id' => 'Asesmenmedis ID',
            'keluhan_tambahan' => 'Keluhan Tambahan',
            'r_penyakitsekarang' => 'R Penyakitsekarang',
            'lama_sakit' => 'Lama Sakit',
            'r_penyakitdahulu' => 'R Penyakitdahulu',
            'r_penyakitkeluarga' => 'R Penyakitkeluarga',
            'r_imunisasi' => 'R Imunisasi',
            'r_peskk' => 'R Peskk',
            'tgl_asesmenmedis' => 'Tgl Asesmenmedis',
            'sumber_info' => 'Sumber Info',
            'sumber_hubungan' => 'Sumber Hubungan',
            'is_merokok' => 'Is Merokok',
            'jml_rokok' => 'Jml Rokok',
            'obat_diberikan' => 'Obat Diberikan',
            'r_makanan' => 'R Makanan',
            'r_kelahiran' => 'R Kelahiran',
            'r_alergiobat' => 'R Alergiobat',
            'keterangan' => 'Keterangan',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Td Diastolic',
            'tekanan_darah' => 'Tekanan Darah',
            'hasil_td' => 'Hasil Td',
            'tinggi_badan' => 'Tinggi Badan',
            'berat_badan' => 'Berat Badan',
            'bb_ideal' => 'Bb Ideal',
            'imt' => 'Imt',
            'ket_imt' => 'Ket Imt',
            'detak_nadi' => 'Detak Nadi',
            'pernapasan' => 'Pernapasan',
            'denyut_jantung' => 'Denyut Jantung',
            'suhu_tubuh' => 'Suhu Tubuh',
            'gcs_eye' => 'Gcs Eye',
            'gcs_verbal' => 'Gcs Verbal',
            'gcs_motorik' => 'Gcs Motorik',
            'hasil_gcs' => 'Hasil Gcs',
            'kontak' => 'Kontak',
            'metod_asmennyeri' => 'Metod Asmennyeri',
        ];
    }
}
