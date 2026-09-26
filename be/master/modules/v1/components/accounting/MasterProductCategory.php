<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterProductCategory extends BaseModel
{
    public function getTableName()
    {
        return 'master_productcategory';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'sync_id_api',
            'parent_id',
            'name',
            'sync_is_service',
            'type',
            'active',
            'wipro_block',
            'origin_id',
            'jenis'
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
        sync_is_service,
        type,
        active,
        wipro_block,
        origin_id,
        jenis
        FROM newodoo_productcategory_v")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'parent_id',
            'id',
            'jenis',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        sync_id_api,
        $sync_type as sync_type,
        name,
        null as parent_id,
        origin_id as id,
        jenis 
        FROM newodoo_productcategory_v")
        ->queryAll();
    }
}