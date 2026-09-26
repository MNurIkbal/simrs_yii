<?php

namespace Integrasi\Service\Sirs\Models;


class IntegrasiAsuransi extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'integrasi_asuransi_r';
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
                'kunjungan_id', 
                'payload',
                'sync_respon',
            ], 'safe'],
            [[
                'pendaftaran_id', 
                'kunjungan_id',
                'payload',
                'sync_respon',
            ], 'default', 'value' => null],
        ];
    }
}
