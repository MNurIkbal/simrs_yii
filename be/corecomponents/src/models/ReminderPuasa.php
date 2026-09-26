<?php

namespace Doco\models;

class ReminderPuasa extends \Doco\components\DocoActiveRecord
{
    /**
     * table name
     */
    public static function tableName()
    {
        return 'reminderpuasa_t';
    }

    /**
     * This function will return rules
     * 
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function rules()
    {
        return [
            [
                [
                    'instruksi_id',
                    'pendaftaran_id',
                    'tgl_akhir_puasa',
                    'tgl_awal_puasa',
                    'tgl_tindakan',
                    'additional_data',
                    'created_date',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'is_deleted',
                    'is_active',
                    'deleted_date',
                    'deleted_by',
                ], 'safe',
            ],
        ];
    }
}
