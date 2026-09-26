<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanrekapkinerja_v".
 *
 * @property string $tgl_sensus
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $jmltt_kapasitas
 * @property int $jmltt_tersedia
 * @property int $jml_pasiensebelumnya
 * @property int $pasien_masuk
 * @property int $pasien_pindahan
 * @property int $jml_pasien
 * @property int $pasien_keluarhidup
 * @property int $pasien_keluardipindahkan
 * @property int $pasien_keluarmeninggalkur48
 * @property int $pasien_keluarmeninggalleb48
 * @property int $pasien_keluarjumlah
 * @property int $pasien_akhir
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $hp
 * @property int $los
 * @property string $alos
 * @property string $bor
 * @property string $toi
 * @property string $bto
 * @property string $ndr
 * @property string $gdr
 */
class LaporanrekapkinerjaV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanrekapkinerja_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_sensus'], 'safe'],
            [['ruangan_id', 'jmltt_kapasitas', 'jmltt_tersedia', 'jml_pasiensebelumnya', 'pasien_masuk', 'pasien_pindahan', 'jml_pasien', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_keluarjumlah', 'pasien_akhir', 'kelaspelayanan_id', 'hp', 'los'], 'default', 'value' => null],
            [['ruangan_id', 'jmltt_kapasitas', 'jmltt_tersedia', 'jml_pasiensebelumnya', 'pasien_masuk', 'pasien_pindahan', 'jml_pasien', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_keluarjumlah', 'pasien_akhir', 'kelaspelayanan_id', 'hp', 'los'], 'integer'],
            [['alos', 'bor', 'toi', 'bto', 'ndr', 'gdr'], 'string'],
            [['ruangan_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_sensus' => 'Tgl Sensus',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'jmltt_kapasitas' => 'Jmltt Kapasitas',
            'jmltt_tersedia' => 'Jmltt Tersedia',
            'jml_pasiensebelumnya' => 'Jml Pasiensebelumnya',
            'pasien_masuk' => 'Pasien Masuk',
            'pasien_pindahan' => 'Pasien Pindahan',
            'jml_pasien' => 'Jml Pasien',
            'pasien_keluarhidup' => 'Pasien Keluarhidup',
            'pasien_keluardipindahkan' => 'Pasien Keluardipindahkan',
            'pasien_keluarmeninggalkur48' => 'Pasien Keluarmeninggalkur48',
            'pasien_keluarmeninggalleb48' => 'Pasien Keluarmeninggalleb48',
            'pasien_keluarjumlah' => 'Pasien Keluarjumlah',
            'pasien_akhir' => 'Pasien Akhir',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'hp' => 'Hp',
            'los' => 'Los',
            'alos' => 'Alos',
            'bor' => 'Bor',
            'toi' => 'Toi',
            'bto' => 'Bto',
            'ndr' => 'Ndr',
            'gdr' => 'Gdr',
        ];
    }
}
