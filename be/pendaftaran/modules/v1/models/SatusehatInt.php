<?php

namespace app\modules\v1\models;

class SatusehatInt extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'satusehat_integrasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id','type'], 'required'],
            [[
                'satusehat_id', 
                'is_sent',
                'id_sync_sercon',
                'sync_respon',
                'state',
                'additional_id',
                'created_date',
                'created_by',
                'payload',
                'is_deleted',
                'is_active',
            ], 'safe'],
            [[
                'is_sent',
                'is_deleted'
            ], 'default', 'value' => false],
            [[
                'is_active',
            ], 'default', 'value' => true]
        ];
    }

}