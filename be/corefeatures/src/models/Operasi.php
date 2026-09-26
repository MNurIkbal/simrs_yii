<?php

namespace SirsCore\models;

use Yii;

class Operasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operasi_kode', 'daftartindakan_id', 'kegiatanoperasi_id', 'golonganoperasi_id'], 'required'],
            [['additional_data'], 'string'],
            [['operasi_nama', 'operasi_kode', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['operasi_nama', 'operasi_kode'], 'string', 'max' => 50],
            [['operasi_kode'], 'trim'],
        ];
    }

    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'operasi_id' => 'Operasi ID',
            'operasi_nama' => 'Nama Operasi',
            'operasi_kode' => 'Kode Operasi',
            'daftartindakan_id' => 'Daftar Tindakan ID',
            'kegiatanoperasi_id' => 'Kegiatan Operasi ID',
            'golonganoperasi_id' => 'Golongan Operasi ID',
            'operasi_namalainnya' => 'Nama Operasi Lainnya',
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

    public function getTindakan()
    {
        return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
    }
}
