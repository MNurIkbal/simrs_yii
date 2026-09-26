<?php

namespace Integrasi\Service\Mhg\Models;


class PasienInt extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'patient_int';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasien_id'], 'required'],
            [[
                'state',
                'created_date',
                'created_by',
                'payload',
                'sync_respon',
            ], 'safe'],
        ];
    }
}