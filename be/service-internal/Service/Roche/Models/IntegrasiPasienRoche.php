<?php

namespace Integrasi\Service\Roche\Models;


class IntegrasiPasienRoche extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'integrasi_pasien_roche_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id'], 'required'],
            [[
                'pasien_id', 
                'payload',
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
            ], 'safe'],
        ];
    }

}
