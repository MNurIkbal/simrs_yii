<?php

namespace app\modules\v1\models;

class PermintaanMakan extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'permintaanmakan_t';
    }

    public function rules()
    {
        return [
            [['pendaftaran_id', 'peg_pemesan_id', 'catatan_diet'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'peg_pemesan_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'peg_pemesan_id', 'status', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_permintaanmakan', 'waktu_pembatalan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alasan_pembatalan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_permintaanmakan'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'permintaaanmakan_id' => 'Permintaan Makan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasien Admisi ID',
            'no_permintaanmakan' => 'No Permintaan Makan',
            'tgl_permintaanmakan' => 'Tgl Permintaan Makan',
            'peg_pemesan_id' => 'Pegawai Pemesan ID',
            'status' => 'Status',
            'waktu_pembatalan' => 'Waktu Pembatalan',
            'alasan_pembatalan' => 'Alasan Pembatalan',
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
