<?php

namespace app\modules\v1\models;

class SoapFisioterapi extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'soapfisioterapi_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'pasien_id',
                    'terapis_id',
                    'subject',
                    'object',
                    'planning',
                    'tgl_soapfisioterapi'
                ], 'required' // REQUIRED
            ],
            [
                [
                    'pendaftaran_id',
                    'pasien_id',
                    'terapis_id',
                    'subject',
                    'object',
                    'assesment',
                    'planning',
                    'tgl_soapfisioterapi',
                    'soapfisioterapi_id',
                    'created_date',
                    'last_modified_date',
                    'deleted_date',
                    'last_modified_by'
                ], 'safe' // SAFE
            ]
        ];
    }
}