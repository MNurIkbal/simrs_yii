<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterGroupInacbg extends BaseModel
{
    public function getTableName()
    {
        return 'master_groupinacbg';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'sync_id_api',
            'name',
            'code',
            'is_obat',
            'inacbgs_field',
            'catatan',
            'is_deleted'
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        sync_id_api,
        name,
        code,
        is_obat,
        inacbgs_field,
        catatan,
        is_deleted
        FROM newodoo_groupinacbg")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_type',
            'sync_id_api',
            'name',
            'code',
            'is_obat',
            'inacbgs_field',
            'catatan',
            'is_deleted'
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        sync_id_api,
        name,
        code,
        is_obat,
        inacbgs_field,
        catatan,
        is_deleted
        FROM newodoo_groupinacbg")
        ->queryAll();
    }
}