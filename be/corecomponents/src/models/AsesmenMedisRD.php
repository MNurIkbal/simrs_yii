<?php

namespace Doco\models;

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
class AsesmenMedisRD extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asesmenmedisrd_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'dokter_id', 'jenis_asmenperawat', 'diagnosakerja_id', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'dokter_id', 'jenis_asmenperawat', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_asesmen','jumlah_gcs', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'dokter_id' => 'Dokter ID',
            'tgl_asesmen' => 'Tgl Asesmen',
            'jenis_asmenperawat' => 'Jenis Asmenperawat',
            'riwayat' => 'Riwayat',
            'riwayat_dahulu' => 'Riwayat Dahulu',
            'diagnosakerja_id' => 'Diagnosakerja ID',
            'gcseye_id' => 'Gcseye ID',
            'gcsverbal_id' => 'Gcsverbal ID',
            'gcsmotorik_id' => 'Gcsmotorik ID',
            'hasil_gcs' => 'Hasil Gcs',
            'is_kapitis' => 'Is Kapitis',
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
