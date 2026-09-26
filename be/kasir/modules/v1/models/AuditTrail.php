<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%audit_trail_k}}".
 *
 * @property int $audit_trail_id
 * @property string $service_name
 * @property string $status
 * @property string $detail
 * @property string $action
 * @property string $messages
 * @property string $stamp
 * @property int $loginpemakai_id
 * @property string $ip_address
 * @property string $url_referer
 * @property string $browser
 * @property string $http_method
 */
class AuditTrail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audit_trail_k}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audit_trail_id', 'loginpemakai_id'], 'default', 'value' => null],
            [['audit_trail_id', 'loginpemakai_id'], 'integer'],
            [['service_name','detail', 'action', 'messages', 'url_referer', 'browser', 'http_method'], 'string'],
            [['stamp','status','service_name'], 'safe'],
            [['ip_address'], 'string', 'max' => 50],
            [['audit_trail_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audit_trail_id' => 'Audit Trail ID',
            'service_name' => 'Service Name',
            'status' => 'Status',
            'detail' => 'Detail',
            'action' => 'Action',
            'messages' => 'Messages',
            'stamp' => 'Stamp',
            'loginpemakai_id' => 'Loginpemakai ID',
            'ip_address' => 'Ip Address',
            'url_referer' => 'Url Referer',
            'browser' => 'Browser',
            'http_method' => 'Http Method',
        ];
    }
}