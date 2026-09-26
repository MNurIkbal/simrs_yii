<?php

namespace app\modules\v1\models;

class IntraOperativeAnestesi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anestesiintraopr_t';
    }

	public function rules()
	{
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['start_induction', 'end_induction', 'length_anesthesia', 'length_surgery', 'start_surgery', 'end_surgery', 'patient_exit', 'pasienmasukpenunjang_id'], 'safe'],
            [['pasienmasukpenunjang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
	}

    public function attributeLabels()
    {
        return [
			'start_induction' => \Yii::t('fe', 'Start in Induction'),
			'end_induction' => \Yii::t('fe', 'End in Induction'),
			'start_surgery' => \Yii::t('fe', 'Start of Surgery'),
			'end_surgery' => \Yii::t('fe', 'End of Surgery'),
			'length_anesthesia' => \Yii::t('fe', 'Length of Anesthesia'),
			'length_surgery' => \Yii::t('fe', 'Length of Surgery'),
			'patient_exit' => \Yii::t('fe', 'Patient Exit at'),
        ];
    }
}