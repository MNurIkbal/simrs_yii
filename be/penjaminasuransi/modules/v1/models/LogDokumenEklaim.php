<?php

namespace app\modules\v1\models;

use Yii;

/**
 * @property integer $id_log
 * @property integer $dokumen_id
 * @property integer $pendaftaran_id
 * @property string $type_dokumen
 * @property boolean $status
 * @property string $created_date
 * @property integer $created_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class LogDokumenEklaim extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokumeneklaim_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_dokumen'], 'string'],
            [['id_log', 'dokumen_id', 'pendaftaran_id', 'type_dokumen', 'status','created_date'], 'safe'],
            [['id_log', 'created_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_log' => 'Log Activity ID',
            'dokumen_id' => 'Dokumen ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'type_dokumen' => 'Type Dokumen',
            'status' => 'Status',
            'alasan' => 'Alasan',
            'additional_data' => 'Additional Data',
            'additional_detail' => 'Additional Detail',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
