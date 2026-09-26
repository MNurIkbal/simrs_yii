<?php

namespace Integrasi\Service\Satusehat\Models;

class Satusehat extends \Integrasi\Components\ActiveRepositories
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
            [[
                'pendaftaran_id',
                'satusehat_id', 
                'is_sent',
                'id_sync_sercon',
                'sync_response',
                'state',
                'additional_id',
                'created_date',
                'created_by',
                'payload',
                'is_deleted',
                'is_active',
                'type',
                'tgl_resend',
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

    public function getEncounter()
    {
        return self::find()->where([
            'type' => 'Encounter'
        ]);
    }

    public function getServiceRequest()
    {
        return self::find()->where([
            'type' => 'ServiceRequest'
        ]);
    }

    public function getSpecimen()
    {
        return self::find()->where([
            'type' => 'Specimen'
        ]);
    }

    public function getObservationLab()
    {
        return self::find()->where([
            'type' => 'ObservationLab'
        ]);
    }
}
