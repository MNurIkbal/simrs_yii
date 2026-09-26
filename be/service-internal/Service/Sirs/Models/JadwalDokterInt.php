<?php

namespace Integrasi\Service\Sirs\Models;


class JadwalDokterInt extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'jadwaldokter_int';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id'], 'required'],
            [[
                'jadwaldokter_id', 
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