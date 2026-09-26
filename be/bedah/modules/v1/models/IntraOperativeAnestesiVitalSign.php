<?php

namespace app\modules\v1\models;

class IntraOperativeAnestesiVitalSign extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anestesiintraoprvisign_t';
    }

	public function rules()
	{
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['anestesiintraopr_id', 'time', 'rr', 'hr', 'systolic', 'diastolic', 'anestesiintraoprvisign_id'], 'safe'],
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
			'rr' => \Yii::t('fe', 'RR'),
			'hr' => \Yii::t('fe', 'HR'),
			'systolic' => \Yii::t('fe', 'Systolic'),
			'diastolic' => \Yii::t('fe', 'Diastolic'),
        ];
    }
}