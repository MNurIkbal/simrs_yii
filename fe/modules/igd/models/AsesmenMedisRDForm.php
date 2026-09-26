<?php

namespace app\modules\igd\models;

use Yii;

/**
 * This is the model class for table "asesmenmedisrd_t".
 *
 * @property int $asesmenmedisrd_id
 * @property int $pendaftaran_id
 * @property int $dokter_id
 * @property string $tgl_asesmen
 * @property int $jenis_asmenperawat lookupkeperawatan_m.lookup_type='jenis_asmen_perawat'
 * @property string $riwayat
 * @property string $riwayat_dahulu
 * @property int $diagnosakerja_id
 * @property int $gcseye_id
 * @property int $gcsverbal_id
 * @property int $gcsmotorik_id
 * @property string $hasil_gcs
 * @property bool $is_kapitis
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class AsesmenMedisRDForm extends \yii\base\Model
{
    public $dokter_jaga;
    public $jenis_asmenperawat;
    public $asesmenmedisrd_id;
    public $pendaftaran_id;
    public $dokter_id;
    public $diagnosakerja_id;
    public $gcseye_id;
    public $gcsverbal_id;
    public $tgl_asesmen;
    public $jumlah_gcs;
    public $riwayat;
    public $is_kapitis;
    public $hasil_gcs;
    public $gcsmotorik_id;
    public $additional_data;
    public $is_active;
    public $riwayat_dahulu;
    public $nilai_gcs;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;
    public $keluhan_utama;
    /**
     * {@inheritdoc}
     */

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_asmenperawat'], 'required'],
            [['asesmenmedisrd_id', 'pendaftaran_id', 'dokter_id', 'jenis_asmenperawat', 'diagnosakerja_id', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['asesmenmedisrd_id', 'pendaftaran_id', 'dokter_id', 'jenis_asmenperawat', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_asesmen','jumlah_gcs', 'created_date', 'last_modified_date', 'deleted_date', 'keluhan_utama'], 'safe'],
            [['riwayat', 'additional_data'], 'string'],
            [['is_kapitis', 'is_deleted', 'is_active'], 'boolean'],
            [['hasil_gcs'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'asesmenmedisrd_id' => 'Asesmenmedisrd ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'dokter_id' => \Yii::t('fe', 'Dokter Jaga'),
            'tgl_asesmen' => \Yii::t('fe', 'Tanggal Asesmen Dokter'),
            'jenis_asmenperawat' => \Yii::t('fe', 'Asesmen Dokter'),
            'riwayat' => 'Riwayat',
            'riwayat_dahulu' => \Yii::t('fe', 'Riwayat Penyakit Terdahulu'),
            'diagnosakerja_id' => \Yii::t('fe', 'Diagnosa Kerja'),
            'gcseye_id' => \Yii::t('fe', 'GCS Eye'),
            'gcsverbal_id' => \Yii::t('fe', 'GCS Verbal'),
            'gcsmotorik_id' => \Yii::t('fe', 'GCS Motorik'),
            'nilai_gcs' => \Yii::t('fe', 'Hasil Metode GCS'),
            'hasil_gcs' => \Yii::t('fe', 'Keterangan GCS'),
            'jumlah_gcs' => \Yii::t('fe', 'Hasil Metode GCS'),
            'is_kapitis' => \Yii::t('fe', 'Kapitis'),
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'keluhan_utama' => 'Keluhan Utama',
        ];
    }
}
