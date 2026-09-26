<?php

namespace Integrasi\Service\Roche\Models;


class IntegrasiRoche extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'integrasi_roche_r';
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
                'pasienkirimkeunitlain_id',
                'payload',
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
                'state',
            ], 'safe'],
            [[
                'pendaftaran_id', 
                'pasienmasukpenunjang_id',
                'pasienkirimkeunitlain_id',
                'payload',
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
                'state',
            ], 'default', 'value' => null],
        ];
    }

}
