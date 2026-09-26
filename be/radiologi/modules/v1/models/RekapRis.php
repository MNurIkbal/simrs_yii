<?php

namespace app\modules\v1\models;

use Yii;

class RekapRis extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekapanris_r';
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
                'no_pembayaran',
                'payload',
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
                'is_update',
                'id_sync_sercon_update',
                'sync_respon_update',
            ], 'safe'],
            [[
                'is_sent',
                'is_sending',
                'is_update',
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
