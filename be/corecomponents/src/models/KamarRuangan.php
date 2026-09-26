<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "kamarruangan_m".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property string $kamarruangan_nokamar
 * @property int $kamarruangan_jenis lookup_type='jenis_kamar'
 * @property string $kamarruangan_deskripsi
 * @property string $kamarruangan_image
 * @property string $keterangan_kamar lookup_type='keterangan_kamar'
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
 * @property double $jumlah_tt
 * @property int $kamaruangan_tipe 0=fixed, 1=fleksibel
 * @property int $jeniskasuspenyakit_id
 * @property int $klasifikasikamar_id
 *
 */
class KamarRuangan extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kamarruangan_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kamarruangan_nokamar', 'kamarruangan_jenis','jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'kamaruangan_tipe','jeniskasuspenyakit_id', 'klasifikasikamar_id', 'id_t_tt_rsonline'], 'integer'],
            [['kamarruangan_deskripsi', 'kamarruangan_image', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'kamarruangan_deskripsi'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jumlah_tt'], 'number'],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['keterangan_kamar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kamarruangan_nokamar' => 'Nama Kamar',
            'kamarruangan_jenis' => 'Kamarruangan Jenis',
            'kamarruangan_deskripsi' => 'Kamarruangan Deskripsi',
            'kamarruangan_image' => 'Kamarruangan Image',
            'keterangan_kamar' => 'Keterangan Kamar',
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
            'jumlah_tt' => 'Jumlah Tt',
            'kamaruangan_tipe' => 'Kamaruangan Tipe',
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'klasifikasikamar_id' => 'Klasifikasi Kamar',
        ];
    }
}
