<?php

namespace app\modules\v1\models;

use Yii;

/**
 * @property integer $id
 * @property integer $penjualanresep_id
 * @property string $response
 * @property boolean $is_sent
 * @property string $created_date
 * @property integer $created_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class LogDokumenResepKronis extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokumenresepkronis_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['response'], 'string'],
            [['id', 'penjualanresep_id', 'is_sent', 'response', 'created_date'], 'safe'],
            [['id', 'penjualanresep_id'], 'integer'],
            [['is_sent', 'is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'response' => 'Response',
            'is_sent' => 'Is Sent',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
