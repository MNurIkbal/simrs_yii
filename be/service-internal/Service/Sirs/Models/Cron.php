<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "cron_k".
 *
 * @property int $cron_id
 * @property string $cron_nama
 * @property string $cron_tgl_mulai
 * @property string $token
 * @property string $url
 * @property string $cron_tgl_akhir
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
 * @property bool $is_sync
 */
class Cron extends \Integrasi\Components\ActiveRepositories
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
            [['cron_tgl_mulai', 'cron_tgl_akhir', 'created_date', 'last_modified_date', 'deleted_date', 'is_sync'], 'safe'],
            [['url', 'additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['cron_nama', 'token'], 'string', 'max' => 255],
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
            'url' => 'Url',
            'cron_tgl_akhir' => 'Cron Tgl Akhir',
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
            'is_sync' => 'Is Sync',
        ];
    }
}