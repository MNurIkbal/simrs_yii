<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterRuangan extends BaseModel
{
    public function getTableName()
    {
        return 'master_ruangan';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'sync_id_api',
            'parent_id',
            'name',
            'active',
            'is_store',
            'is_mainstore',
            'is_substore',
            'is_cartstore',
            'wipro_block'
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        sync_id_api,
        parent_id,
        name,
        active,
        is_store,
        is_mainstore,
        is_substore,
        is_cartstore,
        wipro_block
        FROM int_ruangan_v")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'is_deleted',
            'instalasi_id',
            'instalasi_nama',
            'bpjs_code',
            'is_poliklinik',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        sync_id_api,
        $sync_type as sync_type,
        name,
        wipro_block as is_deleted,
        instalasi_id,
        instalasi_nama,
        kode_ruangan_bpjs as bpjs_code,
        is_poliklinik
        FROM int_ruangan_v")
        ->queryAll();
    }
}