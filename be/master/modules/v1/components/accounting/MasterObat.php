<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterObat extends BaseModel
{
    public function getTableName()
    {
        return 'master_obat';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "sync_id_api",
            "active",
            "sale_ok",
            "purchase_ok",
            "name",
            "categ_id",
            "uom_id",
            "uom2_id",
            "uom_po_id",
            "default_code",
            "type",
            "wipro_block",
            "strength",
            "catalog_code",
            "brand",
            "manufacturer_code",
            "manufacturer_name",
            "pharmacology",
            "shelf",
            "conversion_rate",
            'sync_is_package',
            'sync_is_service'
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        sync_id_api,
        active,
        sale_ok,
        purchase_ok,
        name,
        categ_id,
        uom_id,
        uom2_id,
        uom_po_id,
        default_code,
        type,
        wipro_block,
        strength,
        catalog_code,
        brand,
        manufacturer_code,
        manufacturer_name,
        pharmacalogy,
        shelf,
        conversion_rate,
        sync_is_package,
        sync_is_service
        FROM int_obat")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'categ_id',
            'default_code',
            'sync_is_service',
            'sync_is_package',
            'uom_id',
            'uom_po_id',
            'conversion_rate',
            'is_deleted',
            'servicecategory_id',
            'servicegroup_id',
            'groupinacbg_id',
            'is_inventaris'
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        sync_id_api,
        $sync_type as sync_type,
        name,
        categ_id,
        default_code,
        sync_is_service,
        sync_is_package,
        uom_id,
        uom_po_id,
        conversion_rate,
        wipro_block as is_deleted,
        servicecategory_id,
        servicegroup_id,
        groupinacbg_id,
        is_inventaris
        FROM int_obat")
        ->queryAll();
    }
}