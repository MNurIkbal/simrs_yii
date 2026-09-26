<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterUom extends BaseModel
{
    public function getTableName()
    {
        return 'master_uom';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'sync_id_api',
            'name',
            'code',
            'active',
            'factor',
            'uom_type',
            'category_id',
            'wipro_block'
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
        active,
        factor,
        uom_type,
        category_id,
        wipro_block
        FROM newodoo_uom_v")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'code',
            'is_deleted',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        sync_id_api,
        $sync_type as sync_type,
        name,
        code,
        wipro_block as is_deleted
        FROM newodoo_uom_v")
        ->queryAll();
    }
}