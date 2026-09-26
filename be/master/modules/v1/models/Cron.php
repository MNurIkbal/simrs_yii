<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-19 13:21:55
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cron_k".
 *
 * @property int $cron_id
 * @property string $cron_nama
 * @property string $cron_tgl_mulai
 * @property string $token
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class Cron extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cron_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cron_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['cron_nama', 'token', 'additional_data'], 'string'],
            [['cron_tgl_mulai', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cron_id' => 'Cron ID',
            'cron_nama' => 'Cron Nama',
            'cron_tgl_mulai' => 'Cron Tgl Mulai',
            'token' => 'Token',
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
