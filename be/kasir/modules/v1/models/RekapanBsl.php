<?php

namespace app\modules\v1\models;

class RekapanBsl extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekapanbsl_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [[
                'pendaftaran_id', 
                'pasienmasukpenunjang_id',
                'tindakanpelayanan_id',
                'daftartindakan_id',
                'tipepaket_id',
                'no_pembayaran',
                'payload',
                'is_sent',
                'is_sending',
                'is_deleted',
            ], 'safe'],
            [[
                'is_sent',
                'is_sending',
            ], 'default', 'value' => false]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
