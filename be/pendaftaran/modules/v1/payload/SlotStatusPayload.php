<?php 

namespace app\modules\v1\payload;

use Yii;

class SlotStatusPayload extends \yii\base\Model
{
    public $slot_doctor_id;
    public $slot_date;
    public $slot_start;
    public $slot_end;
    public $slot_type;
    public $slot_sequence;

    public function rules()
    {
        return [
            [[
                'slot_doctor_id', 
                'slot_date', 
                'slot_start', 
                'slot_end', 
                'slot_sequence', 
                'slot_type', 
            ], 'safe'],
            [['slot_doctor_id'], 'integer'],
            ['slot_date','datetime','format' => 'php:Y-m-d'],
            [['slot_start', 'slot_end'],'datetime','format' => 'php:H:i'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'slot_doctor_id' => 'Doctor id',
            'slot_date' => 'Slot date',
            'slot_start' => 'Slot start',
            'slot_end' => 'Slot end',
            'slot_sequence' => 'Slot sequence',
            'slot_type' => 'Slot type',
        ];
    }
}