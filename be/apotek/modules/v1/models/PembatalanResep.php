<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pembatalanresep_t".
 *
 * @property integer pembatalanresep_id
 * @property integer penjualanresep_id
 * @property string tgl_pembatalan
 * @property string no_pembatalan
 * @property integer petugas_batal_id
 * @property string alasan_batal
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
 */
 class PembatalanResep extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembatalanresep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penjualanresep_id'], 'required'],
            [['pembatalanresep_id', 'penjualanresep_id','petugas_batal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembatalanresep_id' => 'Pembatalan Resep ID',
            'penjualanresep_id' => 'Penjualan Resep ID',
            'tgl_pembatalan' => 'Tanggal Pembatalan',
            'no_pembatalan' => 'No Pembatalan',
            'petugas_batal_id' => 'Petugas Batal',
            'alasan_batal' => 'Alasan Batal',
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