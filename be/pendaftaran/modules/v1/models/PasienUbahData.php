<?php

namespace app\modules\v1\models;

use Yii;
use \Doco\components\DocoActiveRecord;
use \app\modules\v1\behaviours\PasienUbahBehaviour;

class PasienUbahData extends DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public function behaviors(){
        return [
          'PasienUbahBehaviour' => [
            'class'=> PasienUbahBehaviour::class
          ]
        ];
    }

    public static function tableName()
    {
        return 'pasienubahdata_t';
    }

    public function rules()
    {
        return [
            [['alasan_ubahdata'], 'required'],
            // [['golonganpegawai_id', 'pangkat_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['additional_data'], 'string'],
            // [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            // [['is_deleted', 'is_active'], 'boolean'],
            // [['pangkat_nama', 'pangkat_namalainnya'], 'string', 'max' => 50],
            // [['golonganpegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Golonganpegawai::className(), 'targetAttribute' => ['golonganpegawai_id' => 'golonganpegawai_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienubahdata_id' =>'Pasien Ubah Data Id',
            'tgl_ubahdata' =>'Tanggal Ubah Data',
            'keterangan_ubahdata' =>'Keterangan',
            'alasan_ubahdata' =>'Alasan',
            'additional_data' =>'Data Tambahan',
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
