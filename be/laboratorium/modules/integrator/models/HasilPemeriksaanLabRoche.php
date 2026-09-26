<?php

namespace app\modules\integrator\models;

use Yii;

class HasilPemeriksaanLabRoche extends \Doco\components\DocoActiveRecord {

    public static function tableName()
    {
        return 'hasilpemeriksaanlab_roche_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'logid',
                    'ts',
                    'key',
                    'data_id',
                    'data_reqid',
                    'data_key',
                    'log',
                    'patient_id',
                    'patient_name',
                    'date_of_birth',
                    'gender',
                    'address',
                    'patient_class',
                    'case_no',
                    'order_ctrl',
                    'order_no',
                    'placer_order_no',
                    'order_status',
                    'transaction_time',
                    'result_time',
                    'result_status',
                    'priority',
                    'set_id',
                    'value_type',
                    'obv_id',
                    'obv_name',
                    'value',
                    'unit_text',
                    'ref_range_1',
                    'ref_range_2',
                    'abnormal_flag',
                    'obv_status',
                    'obv_time',
                    'observer',
                    'method',
                    'specimen_type',
                    'specimen_name',
                    'specimen_collection_time',
                    'additional_data',
                ],
                'safe'
            ],
            [
                ['is_deleted'], 
                'default', 
                'value' => 0
            ],
            [
                ['is_active'], 
                'default', 
                'value' => 1
            ],
        ];
    }
}