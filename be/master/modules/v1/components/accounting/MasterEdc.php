<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterEdc extends BaseModel
{
    public function getTableName()
    {
        return 'master_edc';
    }
    
    public function getAttributes()
    {
        return [
            'sync_type',
            'sync_id_api',
            'code',
            'name',
            'bank_id',
            'is_deleted',
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        edclist_id as sync_id_api,
        edclist_kode as code,
        edclist_namamesin as name,
        edclist_bank as bank_id,
        is_deleted
        FROM edclist_m
        ")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_type',
            'sync_id_api',
            'code',
            'name',
            'bank_id',
            'is_deleted',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        edclist_id as sync_id_api,
        edclist_kode as code,
        edclist_namamesin as name,
        edclist_bank as bank_id,
        is_deleted
        FROM edclist_m
        ")
        ->queryAll();
    }
}