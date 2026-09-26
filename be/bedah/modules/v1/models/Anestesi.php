<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "anestesi_t".
 *
 */
class Anestesi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'anestesi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'anestesi_id',
                'pasienmasukpenunjang_id',
                'anestesi_result', 
                'anestesi_regional',
                'anestesi_regional_other',
                'type_needle_size',
                'lenght_catheter_isertion',
                'anestesi_general', 
                'patient_position', 
                'preinduction', 
                'induction', 
                'maintenance', 
                'recovery', 
                'additional_data', 
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => 'ID Pasien Penunjang',
            'anastesi_result' => 'Result',
            'anestesi_regional' => 'Regional',
            'anestesi_regional_other' => 'Other Regional',
            'type_needle_size' => 'Type of Needle/Size',
			'lenght_catheter_isertion' => 'Length of Catheter Isertion',
            'anestesi_general' => 'General',
            'patient_position' => 'Patient Position',
            'preinduction' => 'Preinduction',
            'induction' => 'Induction',
            'maintenance' => 'Maintenance',
            'recovery' => 'Recovery',
        ];
    }
}
