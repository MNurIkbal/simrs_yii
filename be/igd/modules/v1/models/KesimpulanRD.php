<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kesimpulanrd_t".
 *
 * @property int $kesimpulanrd_id
 * @property int $pendaftaran_id
 * @property int $pasienpulang_id
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
 * @property int $gcs_verbal_id
 * @property int $gcs_motorik_id
 * @property int $hasil_gcs
 * @property string $gcs_kategori
 * @property bool $is_kapitis
 * @property int $reseptur_id
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
class KesimpulanRD extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kesimpulanrd_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienpulang_id', 'poliklinik_id', 'dokter_id', 'hr', 'rr', 'spo2', 't', 'gcs_eye_id', 'gcs_verbal_id', 'gcs_motorik_id', 'hasil_gcs', 'reseptur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienpulang_id', 'poliklinik_id', 'dokter_id', 'hr', 'rr', 'spo2', 'gcs_eye_id', 'gcs_verbal_id', 'gcs_motorik_id', 'hasil_gcs', 'reseptur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instruksi_lanjutan', 'kondisi', 'additional_data', 't'], 'string'],
            [['tgl_lanjut_rawat', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_kapitis', 'is_deleted', 'is_active'], 'boolean'],
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
            'gcs_verbal_id' => 'Gcs Verbal ID',
            'gcs_motorik_id' => 'Gcs Motorik ID',
            'hasil_gcs' => 'Hasil Gcs',
            'gcs_kategori' => 'Gcs Kategori',
            'is_kapitis' => 'Is Kapitis',
            'reseptur_id' => 'Reseptur ID',
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
        ];
    }
}
