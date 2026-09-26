<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "Pasien Penunjang".
 *
 */
class InfPasienPenunjang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienpenunjang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
       
        ];
    }
}
