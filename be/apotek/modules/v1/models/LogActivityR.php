<?php

namespace app\modules\v1\models;

use Yii;

/**
 * @property integer $logactivity_id
 * @property string $tgl
 * @property string $tipe
 * @property string $aksi
 * @property string $keterangan
 * @property string $alasan
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $transaksi_id
 * @property string $additional_detail
 */
class LogActivityR extends \Doco\components\DocoActiveRecord
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
            [['transaksi_id', 'tgl', 'additional_detail', 'created_date', 'last_modified_date', 'deleted_date','tipe','alasan','keterangan'], 'safe'],
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
            'logactivity_id' => 'Log Activity ID',
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
