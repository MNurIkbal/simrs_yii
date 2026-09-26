<?php

namespace SirsCore\models;

use Yii;
use Doco\components\DocoActiveRecord;

class LogActivityR extends DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logactivity_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipe', 'aksi', 'keterangan', 'alasan', 'additional_data', 'additional_detail'], 'string'],
            [['transaksi_id', 'tgl', 'additional_detail', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['transaksi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl' => 'Tanggal',
            'tipe' => 'Tipe',
            'aksi' => 'Aksi',
            'keterangan' => 'Keterangan',
            'alasan' => 'Alasan',
            'additional_data' => 'Additional Data',
            'additional_detail' => 'Additional Detail',
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
