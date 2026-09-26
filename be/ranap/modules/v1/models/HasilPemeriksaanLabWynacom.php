<?php

namespace app\modules\v1\models;

use Yii;

class HasilPemeriksaanLabWynacom extends \Doco\components\DocoActiveRecord {

    public static function tableName()
    {
        return 'hasilpemeriksaanlab_wynacom_t';
    }

    public function rules()
    {
        return [
            [['lis_reg_no', 'his_reg_no', 'lis_test_id', 'test_name', 'his_test_id'], 'required', 'message' => '{attribute} Tidak boleh kosong!','on' => 'non_authorization'],
            [
                ['lis_reg_no', 'his_reg_no', 'lis_test_id', 'test_name', 'result', 'his_test_id','authorization_date','authorization_user'], 'required', 'message' => '{attribute} Tidak boleh kosong!'
            ],
            [
                ["result_comment","reference_value","reference_note","test_flag_sign","test_units_name","instrument_name","greaterthan_value","lessthan_value","age_year","age_month","age_days","sequence","transfer_flag","test_group","test_method","authorization_date"], 'default', 'value' => null
            ],
            [
                [
                    "lis_reg_no","lis_test_id","his_reg_no","test_name","result","result_comment","reference_value","reference_note","test_flag_sign","test_units_name","instrument_name","authorization_date","authorization_user","greaterthan_value","lessthan_value","age_year","age_month","age_days","his_test_id","sequence","transfer_flag","test_group","test_method",'is_deleted', 'is_active'
                ], 'safe'
            ],
        ];
    }
}