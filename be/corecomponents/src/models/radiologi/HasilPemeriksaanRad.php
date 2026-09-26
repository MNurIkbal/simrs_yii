<?php

namespace Doco\models\radiologi;

use Yii;
/**
 * This is the model class for table "hasilpemeriksaanrad_t".
 *
 * @property int $hasilpemeriksaanrad_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_hasilrad
 * @property string $tgl_hasilrad
 * @property int $penanggungjawab_id
 * @property int $pemeriksaanrad_id
 * @property int $tindakanpelayanan_id
 * @property int $expertise_id
 * @property bool $is_hasilkritis
 * @property string $hasil_expertise
 * @property string $kesan
 * @property string $kesimpulan
 * @property bool $is_ambilfoto
 * @property string $tgl_ambilfoto
 * @property string $tgl_uploadhasil
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
class HasilPemeriksaanRad extends \Doco\components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanrad_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'penanggungjawab_id', 'pemeriksaanrad_id', 'tindakanpelayanan_id', 'expertise_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'penanggungjawab_id', 'pemeriksaanrad_id', 'tindakanpelayanan_id', 'expertise_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_hasilrad', 'tgl_ambilfoto','status_pemeriksaan', 'tgl_verifikasi', 'tgl_uploadhasil', 'created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id'], 'safe'],
            [['is_hasilkritis', 'is_ambilfoto', 'is_deleted', 'is_active'], 'boolean'],
            [['hasil_expertise', 'kesan', 'kesimpulan', 'additional_data'], 'string'],
            [['no_hasilrad'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanrad_id' => 'Hasilpemeriksaanrad ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_hasilrad' => 'No Hasilrad',
            'tgl_hasilrad' => 'Tgl Hasilrad',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'pemeriksaanrad_id' => 'Pemeriksaaanrad ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'expertise_id' => 'Expertise ID',
            'is_hasilkritis' => 'Is Hasilkritis',
            'hasil_expertise' => 'Hasil Expertise',
            'kesan' => 'Kesan',
            'kesimpulan' => 'Kesimpulan',
            'is_ambilfoto' => 'Is Ambilfoto',
            'tgl_ambilfoto' => 'Tgl Ambilfoto',
            'tgl_uploadhasil' => 'Tgl Uploadhasil',
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
