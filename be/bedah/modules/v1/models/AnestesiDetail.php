<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "anestesidetail_t".
 *
 */
class AnestesiDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'anestesidetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'anestesidetail_id',
                'anestesi_id',
                'obatalkes_id',
                'dose', 
                'time_delivery',
                'additional_data', 
                'created_date',
                'is_deleted',
                'is_active',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'anestesidetail_id' => 'ID Anestesi Detail',
            'anestesi_id' => 'ID Anestesi',
            'obatalkes_id' => 'Drug',
            'dose' => 'Dose',
            'time_delivery' => 'Time Delivery',
            'created_date' => 'Created Date',
        ];
    }

    public static function deleteByAnestesiId($anestesiId)
    {
        $today = date('Y-m-d H:i:s');
        $query = "UPDATE 
                    anestesidetail_t
                SET 
                    is_deleted = true,
                    deleted_date = '{$today}'
                WHERE 
                    anestesi_id = {$anestesiId}
            ";
        $result = Yii::$app->db->createCommand($query)->queryOne();
        return true;
    }
}
