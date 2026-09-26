<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilbridgingradiologi_t".
 *
 * @property int hasilbridgingradiologi_id
 * @property json log_id
 * @property datetime message_time
 * @property string control_id
 * @property string patient_id
 * @property string case_no
 * @property string hospital_service
 * @property string ref_doctor_id
 * @property string ref_doctor_name
 * @property string order_no
 * @property string filler_order
 * @property string order_status
 * @property string priority
 * @property datetime transaction_time
 * @property string entered_by
 * @property string procedure_code
 * @property string procedure_name
 * @property datetime requested_time
 * @property datetime observation_time
 * @property string clinical_info
 * @property string result_status
 * @property string principal_itpr
 * @property string assistant_itpr
 * @property string transcriptionist
 * @property string value_type
 * @property string obv_id
 * @property string obv_value
 * @property string obv_value_html
 * @property string obv_status
 * @property string abnormal_flags
 * @property string image_link

 * @property string additional_data
 * @property string created_date
 * @property int created_by
 * @property int modified_count
 * @property string last_modified_date
 * @property int last_modified_by
 * @property bool is_deleted
 * @property bool is_active
 * @property string deleted_date
 * @property int deleted_by

 */
class HasilBridgingRadiologiT extends \Doco\components\DocoActiveRecord
{
    public $obv_value_text;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilbridgingradiologi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'
            ], 'default', 'value' => null],
            [[
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'
            ], 'integer'],
            [[
                'log_id',
                'message_time',
                'control_id',
                'patient_id',
                'case_no',
                'hospital_service',
                'ref_doctor_id',
                'ref_doctor_name',
                'order_no',
                'filler_order',
                'order_status',
                'priority',
                'transaction_time',
                'entered_by',
                'procedure_code',
                'procedure_name',
                'requested_time',
                'observation_time',
                'clinical_info',
                'result_status',
                'principal_itpr',
                'assistant_itpr',
                'transcriptionist',
                'value_type',
                'obv_id',
                'obv_value',
                'obv_value_html',
                'obv_status',
                'abnormal_flags',
                'obv_value_text',
                'image_link'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hasilbridgingradiologi_id' => 'Bridging Radiologi Id',
            'log_id'                    => 'Log Id',
            'message_time'              => 'Message Time',
            'control_id'                => 'Control Id',
            'patient_id'                => 'Patient Id',
            'case_no'                   => 'Case No',
            'hospital_service'          => 'HospitalService',
            'ref_doctor_id'             => 'Ref Doctor Id',
            'ref_doctor_name'           => 'Ref Doctor Name',
            'order_no'                  => 'Order No',
            'filler_order'              => 'Filler Order',
            'order_status'              => 'Order Status',
            'priority'                  => 'Priority',
            'transaction_time'          => 'Transaction Time',
            'entered_by'                => 'Entered By',
            'procedure_code'            => 'Procedure Code',
            'procedure_name'            => 'Procedure Name',
            'requested_time'            => 'Requested Time',
            'observation_time'          => 'Observation Time',
            'clinical_info'             => 'Clinical Info',
            'result_status'             => 'Result Status',
            'principal_itpr'            => 'Principal Itpr',
            'assistant_itpr'            => 'Assistant Itpr',
            'transcriptionist'          => 'Transcriptionist',
            'value_type'                => 'Value Type',
            'obv_id'                    => 'Obv Id',
            'obv_value'                 => 'Obv Value',
            'obv_value_html'            => 'Obv Value Html',
            'obv_status'                => 'Obv Status',
            'abnormal_flags'            => 'Abnormal Flags',
            'image_link'                => 'Image Link',
            
            'additional_data'           => 'Additional Data',
            'created_date'              => 'Created Date',
            'created_by'                => 'Created By',
            'modified_count'            => 'Modified Count',
            'last_modified_date'        => 'Last Modified Date',
            'last_modified_by'          => 'Last Modified By',
            'is_deleted'                => 'Is Deleted',
            'is_active'                 => 'Is Active',
            'deleted_date'              => 'Deleted Date',
            'deleted_by'                => 'Deleted By',
        ];
    }
}
