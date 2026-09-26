<?php

namespace app\modules\v1\models;

class PostOperativeAnestesiDrugSupport extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anestesipostoprdrugsupport_t';
    }

	public function rules()
	{
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['anestesipostoprdrugsupport_id', 'anestesipostopr_id', 'obatalkes_id', 'dose', 'time_delivery'], 'safe'],
            [['anestesipostopr_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
	}
}