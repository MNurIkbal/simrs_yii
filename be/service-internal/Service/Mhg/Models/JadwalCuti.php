<?php

namespace Integrasi\Service\Mhg\Models;


class JadwalCuti extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'jadwalcuti_int';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwalcuti_id'], 'required'],
            [[
                'jadwalcuti_id', 
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
                'state',
                'created_date',
                'created_by',
                'payload',
            ], 'safe'],
            [[
                'is_sent',
                'is_sending',
            ], 'default', 'value' => false]
        ];
    }

}