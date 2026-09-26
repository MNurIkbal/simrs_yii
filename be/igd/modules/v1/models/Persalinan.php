<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "persalinan_t".
 *
 * @property int $persalinan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_persalinan
 * @property string $penolong
 * @property string $tempat_persalinan
 * @property int $rujuk_kala lookupkeperawatan.lookup_type='rujuk_kala'
 * @property string $alasan_merujuk
 * @property string $tempat_rujukan
 * @property int $pendamping lookupkeperawatan.lookup_type='pendamping'
 * @property int $masalah_persalinan lookupkeperawatan.lookup_type='masalah_persalinan'
 * @property bool $k1_gariswaspada
 * @property string $k1_masalah
 * @property string $k1_pelaksanaanmasalah
 * @property string $k1_hasil
 * @property bool $k2_episitomi
 * @property string $k2_indikasi
 * @property int $k2_pendamping lookupkeperawatan.lookup_type='pendamping'
 * @property bool $k2_gawatjanin
 * @property string $k2_tindakanjanin
 * @property string $k2_hasil
 * @property bool $k2_distosiabahu
 * @property string $k2_tindakandistosia
 * @property string $k2_masalah
 * @property string $k3 JSON Format *lookupkeperawatan.lookup_type='laserisasi'
 * @property string $k4_keadaanumum
 * @property int $k4_td_systolic
 * @property int $k4_td_diastolic
 * @property int $k4_detaknadi
 * @property int $k4_pernapasan
 * @property string $k4_masalah
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
class Persalinan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persalinan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'rujuk_kala', 'pendamping', 'masalah_persalinan', 'k2_pendamping', 'k4_td_systolic', 'k4_td_diastolic', 'k4_detaknadi', 'k4_pernapasan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','penolong'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'rujuk_kala', 'pendamping', 'masalah_persalinan', 'k2_pendamping', 'k4_td_systolic', 'k4_td_diastolic', 'k4_detaknadi', 'k4_pernapasan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','penolong'], 'integer'],
            [['tgl_persalinan', 'created_date', 'last_modified_date', 'deleted_date', "k3", 'jenis_persalinan','penolong'], 'safe'],
            [['k1_gariswaspada', 'k2_episitomi', 'k2_gawatjanin', 'k2_distosiabahu', 'is_deleted', 'is_active'], 'boolean'],
            [['k1_masalah', 'k1_pelaksanaanmasalah', 'k1_hasil', 'k2_tindakanjanin', 'k2_tindakandistosia', 'k2_masalah', 'k3', 'k4_keadaanumum', 'k4_masalah', 'additional_data'], 'string'],
            [['tempat_persalinan', 'alasan_merujuk', 'tempat_rujukan', 'k2_indikasi', 'k2_hasil'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'persalinan_id' => 'Persalinan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_persalinan' => 'Tgl Persalinan',
            'penolong' => 'Penolong',
            'tempat_persalinan' => 'Tempat Persalinan',
            'rujuk_kala' => 'Rujuk Kala',
            'alasan_merujuk' => 'Alasan Merujuk',
            'tempat_rujukan' => 'Tempat Rujukan',
            'pendamping' => 'Pendamping',
            'masalah_persalinan' => 'Masalah Persalinan',
            'k1_gariswaspada' => 'K1 Gariswaspada',
            'k1_masalah' => 'K1 Masalah',
            'k1_pelaksanaanmasalah' => 'K1 Pelaksanaanmasalah',
            'k1_hasil' => 'K1 Hasil',
            'k2_episitomi' => 'K2 Episitomi',
            'k2_indikasi' => 'K2 Indikasi',
            'k2_pendamping' => 'K2 Pendamping',
            'k2_gawatjanin' => 'K2 Gawatjanin',
            'k2_tindakanjanin' => 'K2 Tindakanjanin',
            'k2_hasil' => 'K2 Hasil',
            'k2_distosiabahu' => 'K2 Distosiabahu',
            'k2_tindakandistosia' => 'K2 Tindakandistosia',
            'k2_masalah' => 'K2 Masalah',
            'k3' => 'K3',
            'k4_keadaanumum' => 'K4 Keadaanumum',
            'k4_td_systolic' => 'K4 Td Systolic',
            'k4_td_diastolic' => 'K4 Td Diastolic',
            'k4_detaknadi' => 'K4 Detaknadi',
            'k4_pernapasan' => 'K4 Pernapasan',
            'k4_masalah' => 'K4 Masalah',
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
