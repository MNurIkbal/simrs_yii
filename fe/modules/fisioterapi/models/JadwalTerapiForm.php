<?php

/**
 * @author Chacha Nurholis <chacha@sirs.co.id>
 * Web Developer at Sirs
 */

namespace app\modules\fisioterapi\models;

use yii\base\Model;

/**
 * This is the model class for table "jadwalterapifisio_t".
 *
 * @property int $jadwalterapifisio_id
 * @property int $programterapi_id
 * @property int $programterapidetail_id
 * @property int $pasien_id
 * @property timestamp $tgl_penjadwalan_awal
 * @property timestamp $tgl_realisasi
 * @property int $pendaftaran_id
 * @property text $additional_data
 * @property timestamp $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property timestamp $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property timestamp $deleted_date
 * @property int $deleted_by
 * @property int $kunjunganke
 * @property int $status_kunjungan_fisio
 * @property timestamp $tgl_penjadwalan_akhir
 * @property int $pegawai_id
 * @property int $tgl_penjadwalan
 * @property text $keterangan_drop_out
 */
class JadwalTerapiForm extends Model
{
    protected $xssProtected = ['keterangan_drop_out'];

    public $jadwalterapifisio_id;
    public $programterapi_id;
    public $programterapidetail_id;
    public $pasien_id;
    public $tgl_penjadwalan_awal;
    public $tgl_realisasi;
    public $pendaftaran_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $kunjunganke;
    public $status_kunjungan_fisio;
    public $tgl_penjadwalan_akhir;
    public $pegawai_id;
    public $tgl_penjadwalan;
    public $keterangan_drop_out;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'jadwalterapifisio_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keterangan_drop_out'], 'string', 'max' => 100, 'message' => 'Maksimal karakter adalah 100']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
