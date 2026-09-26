<?php

namespace app\modules\v1\models;

use Yii;

class PersalinanDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'persalinandetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'persalinandetail_id',
                'persalinan_id',
                'pendaftaran_id',
                'created_by', 
                'modified_count', 
                'last_modified_by',
                'deleted_by'
            ], 'integer'],
            [[
                'is_deleted', 
                'is_active'
            ], 'boolean'],
            [[
                'additional_data'
            ], 'string'],
            [[
                'persalinandetail_id',
                'persalinan_id',
                'pendaftaran_id',
                'jam_ke',
                'waktu',
                'td_systolic',
                'td_diastolic',
                'detak_nadi',
                'suhu',
                'tinggi_fundus',
                'kontraksi_uterus',
                'darah_keluar',
                'created_by', 
                'modified_count', 
                'last_modified_by',
                'deleted_by',
                'kandung_kemih',
            ], 'safe'],
        ];
    }

}
