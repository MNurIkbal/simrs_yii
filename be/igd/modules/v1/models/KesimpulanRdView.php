<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kesimpulanrd_v".
 *
 * @property int $kesimpulanrd_id
 * @property int $pendaftaran_id
 * @property int $pasienpulang_id
 * @property string $carakeluar_nama
 * @property string $kondisikeluar_nama
 * @property string $tglpasienpulang
 * @property string $tgl_meninggal
 * @property string $instruksi_lanjutan
 * @property string $tgl_lanjut_rawat
 * @property int $poliklinik_id
 * @property int $dokter_id
 * @property string $kondisi
 * @property int $hr
 * @property int $rr
 * @property int $spo2
 * @property int $t
 * @property int $gcs_eye_id
 * @property int $nilai_eye
 * @property int $gcs_verbal_id
 * @property int $nilai_verbal
 * @property int $gcs_motorik_id
 * @property int $nilai_motorik
 * @property int $hasil_gcs
 * @property string $gcs_kategori
 * @property bool $is_kapitis
 * @property int $reseptur_id
 */
class KesimpulanRdView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kesimpulanrd_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kesimpulanrd_id', 'pendaftaran_id', 'pasienpulang_id', 'poliklinik_id', 'dokter_id', 'hr', 'rr', 'spo2', 't', 'gcs_eye_id', 'nilai_eye', 'gcs_verbal_id', 'nilai_verbal', 'gcs_motorik_id', 'nilai_motorik', 'hasil_gcs', 'reseptur_id'], 'default', 'value' => null],
            [['kesimpulanrd_id', 'pendaftaran_id', 'pasienpulang_id', 'poliklinik_id', 'dokter_id', 'hr', 'rr', 'spo2', 't', 'gcs_eye_id', 'nilai_eye', 'gcs_verbal_id', 'nilai_verbal', 'gcs_motorik_id', 'nilai_motorik', 'hasil_gcs', 'reseptur_id'], 'integer'],
            [['tglpasienpulang', 'tgl_meninggal', 'tgl_lanjut_rawat'], 'safe'],
            [['instruksi_lanjutan', 'kondisi'], 'string'],
            [['is_kapitis'], 'boolean'],
            [['carakeluar_nama', 'kondisikeluar_nama'], 'string', 'max' => 100],
            [['gcs_kategori'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kesimpulanrd_id' => 'Kesimpulanrd ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'tglpasienpulang' => 'Tglpasienpulang',
            'tgl_meninggal' => 'Tgl Meninggal',
            'instruksi_lanjutan' => 'Instruksi Lanjutan',
            'tgl_lanjut_rawat' => 'Tgl Lanjut Rawat',
            'poliklinik_id' => 'Poliklinik ID',
            'dokter_id' => 'Dokter ID',
            'kondisi' => 'Kondisi',
            'hr' => 'Hr',
            'rr' => 'Rr',
            'spo2' => 'Spo2',
            't' => 'T',
            'gcs_eye_id' => 'Gcs Eye ID',
            'nilai_eye' => 'Nilai Eye',
            'gcs_verbal_id' => 'Gcs Verbal ID',
            'nilai_verbal' => 'Nilai Verbal',
            'gcs_motorik_id' => 'Gcs Motorik ID',
            'nilai_motorik' => 'Nilai Motorik',
            'hasil_gcs' => 'Hasil Gcs',
            'gcs_kategori' => 'Gcs Kategori',
            'is_kapitis' => 'Is Kapitis',
            'reseptur_id' => 'Reseptur ID',
        ];
    }
}
