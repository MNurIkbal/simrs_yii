<?php

namespace app\modules\master\models;

use app\components\DocoBaseModel;

class TipePaketForm extends DocoBaseModel
{
    public $tipepaket_id;
    public $tipepaket_nama;
    public $tipepaket_kode;
    public $tipepaket_namalainnya;
    public $keterangan_tipepaket;
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
    public $is_mcu;

    protected $xssProtected = [
        'tipepaket_nama',
        'tipepaket_kode',
        'tipepaket_namalainnya',
        'keterangan_tipepaket'
    ];

    public static function tableName () {
        return 'tipepaket_m';
    }

    public function rules()
    {
        return [
            [['tipepaket_nama'], 'required', 'message' => 'Nama Paket Harus Diisi'],
            [['tipepaket_kode'], 'required', 'message' => 'Kode Paket Harus Diisi'],
            [['tipepaket_namalainnya'], 'required', 'message' => 'Nama Lainnya Harus Diisi'],
            [['keterangan_tipepaket', 'additional_data'], 'string'],
            [['is_mcu', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tipepaket_nama', 'tipepaket_namalainnya'], 'string', 'max' => 50],
            [['tipepaket_kode'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipepaket_id' => 'Tipe Paket ID',
            'tipepaket_nama' => 'Tipe Paket Nama',
            'tipepaket_kode' => 'Tipe Paket Kode',
            'tipepaket_namalainnya' => 'Tipe Paket Namalainnya',
            'keterangan_tipepaket' => 'Keterangan Tipe Paket',
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
