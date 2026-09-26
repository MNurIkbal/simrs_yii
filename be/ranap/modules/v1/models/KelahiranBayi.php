<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelahiranbayi_t".
 *
 * @property int $kelahiranbayi_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $bayi_urut
 * @property double $berat_badan
 * @property double $tinggi_badan
 * @property int $jenis_kelamin lookup_type='jenis_kelamin'
 * @property int $penilaian lookupkeperawatan='penilaian'
 * @property int $kondisi_bayi lookupkeperawatan='kondisi_bayi'
 * @property int $normal_tindakan lookupkeperawatan='bayinormal_tindakan'
 * @property int $asfiksia lookupkeperawatan='asfiksia'
 * @property int $asfiksia_tindakan lookupkeperawatan='asfiksia_tindakan'
 * @property string $keterangan_kondisi
 * @property bool $is_asi
 * @property string $keterangan_asi
 * @property string $masalah_lain
 * @property string $hasil
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
class KelahiranBayi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelahiranbayi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'tgl_lahir'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'bayi_urut', 'jenis_kelamin', 'penilaian', 'kondisi_bayi', 'normal_tindakan', 'asfiksia', 'asfiksia_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'bayi_urut', 'jenis_kelamin', 'penilaian', 'kondisi_bayi', 'asfiksia', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['berat_badan', 'tinggi_badan'], 'number'],
            [['keterangan_kondisi', 'masalah_lain', 'hasil', 'additional_data'], 'string'],
            [['is_asi', 'is_deleted', 'is_active'], 'boolean'],
            [['asfiksia', 'berat_badan', 'tinggi_badan', 'is_kondisi_cacat', 'keterangan_cacat', 'is_kondisi_hipotermi', 'keterangan_hipotermi', 
            'created_date', 'last_modified_date', 'deleted_date', 'warna_kulit', 'pegawai_id', 'kamartempattidur_id', 'golongan_darah', 'lingkar_kepala'], 'safe'],
            [['keterangan_asi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelahiranbayi_id' => 'Kelahiranbayi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'bayi_urut' => 'Bayi Urut',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'penilaian' => 'Penilaian',
            'kondisi_bayi' => 'Kondisi Bayi',
            'normal_tindakan' => 'Normal Tindakan',
            'asfiksia' => 'Asfiksia',
            'asfiksia_tindakan' => 'Asfiksia Tindakan',
            'keterangan_kondisi' => 'Keterangan Kondisi',
            'is_asi' => 'Is Asi',
            'keterangan_asi' => 'Keterangan Asi',
            'masalah_lain' => 'Masalah Lain',
            'hasil' => 'Hasil',
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
