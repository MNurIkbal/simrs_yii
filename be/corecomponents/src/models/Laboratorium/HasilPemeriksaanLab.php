<?php

namespace Doco\models\Laboratorium;

use Yii;

class HasilPemeriksaanLab extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'hasilpemeriksaanlab_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'pegawailab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'pasienmasukpenunjang_id', 'pasienadmisi_id', 'pendaftaran_id', 'pegawailab_id', 'samplelab_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'tindakanpelayanan_id', 'status_pemeriksaan', 'pemeriksaanlab_id'], 'integer'],
            [['tgl_hasilpemeriksaanlab', 'tgl_pengambilanhasil', 'tgl_kritis', 'tgl_expertise', 'created_date', 'last_modified_date', 'deleted_date', 'tindakanpelayanan_id', 'status_pemeriksaan', 'pemeriksaanlab_id'], 'safe'],
            [['catatan', 'expertise', 'upload_file', 'additional_data'], 'string'],
            [['printhasillab', 'is_kritis', 'is_expertise', 'is_deleted', 'is_active'], 'boolean'],
            [['nohasilperiksalab'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilpemeriksaanlab_id' => 'Hasilpemeriksaanlab ID',
            'pasien_id' => 'Pasien ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nohasilperiksalab' => 'Nohasilperiksalab',
            'tgl_hasilpemeriksaanlab' => 'Tgl Hasilpemeriksaanlab',
            'tgl_pengambilanhasil' => 'Tgl Pengambilanhasil',
            'catatan' => 'Catatan',
            'printhasillab' => 'Printhasillab',
            'is_kritis' => 'Is Kritis',
            'tgl_kritis' => 'Tgl Kritis',
            'is_expertise' => 'Is Expertise',
            'expertise' => 'Expertise',
            'pegawailab_id' => 'Pegawailab ID',
            'upload_file' => 'Upload File',
            'tgl_expertise' => 'Tgl Expertise',
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
