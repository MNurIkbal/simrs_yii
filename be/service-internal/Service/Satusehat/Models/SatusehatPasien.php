<?php

namespace Integrasi\Service\Satusehat\Models;

class SatusehatPasien extends \Integrasi\Components\ActiveRepositories
{
    public static function tableName()
    {
        return 'pasien_satusehat_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pasien_id',
                'satusehat_pasien_id',
                'satusehat_integration_id',
                'created_date',
                'created_by',
                'deleted_date',
                'deleted_by',
            ], 'safe'],
            [[
                'is_deleted'
            ], 'default', 'value' => false],
            [[
                'is_active',
            ], 'default', 'value' => true]
        ];
    }
}