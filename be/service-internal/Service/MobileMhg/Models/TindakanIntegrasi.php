<?php

namespace Integrasi\Service\MobileMhg\Models;


class TindakanIntegrasi extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'tindakanintegration_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tindakan_id', 'tindakan_kode', 'status', 'admin_charge', 'sync_response', 'is_error', 'state', 'created_by', 'id_sync_sercon', 'created_date', 'tindakan_nama'], 'required'],
        ];
    }

}
