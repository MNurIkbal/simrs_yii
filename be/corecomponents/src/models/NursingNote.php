<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "kertas_k".
 *
 * @property int $catatankeperawatan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_catatan
 * @property string $waktu_catatan
 * @property string $kegiatan_perawat
 * @property int $kegiatankeperawatan_id
 * @property string $catatan
 * @property string $pegawai_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $is_deleted
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class NursingNote extends \Doco\components\DocoActiveRecord
{
    // protected $xssProtected = [
    //     'kertas_kode',
    //     'kertas_nama'
    // ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catatankeperawatan_t';
    }
        /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by','catatankeperawatan_id','pendaftaran_id','kegiatankeperawatan_id'], 'integer'],
            [['additional_data','catatan','tgl_catatan','waktu_catatan','pegawai_id','kegiatan_perawat'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','pasienadmisi_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kegiatan_perawat' => 'Kegiatan Perawata',
            'catatan' => 'Catatan',
            'tgl_catatan' => 'Tanggal Catatan',
            'waktu_catatan' => 'Waktu Catatan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi_id',
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
