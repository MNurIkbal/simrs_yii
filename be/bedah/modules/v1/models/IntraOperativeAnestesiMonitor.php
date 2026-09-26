<?php

namespace app\modules\v1\models;

class IntraOperativeAnestesiMonitor extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anestesiintraoprmonitor_t';
    }

	public function rules()
	{
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['anestesiintraopr_id', 'time', 'vinput', 'monitoring_id', 'anestesiintraoprmonitor_id'], 'safe'],
            [['anestesiintraopr_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
			'time' => \Yii::t('fe', 'Time'),
			'vinput' => \Yii::t('fe', 'Value'),
			'monitoring_id' => \Yii::t('fe', 'Monitoring ID'),
        ];
    }
}