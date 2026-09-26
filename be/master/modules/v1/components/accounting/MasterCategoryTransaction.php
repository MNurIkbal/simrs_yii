<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterCategoryTransaction extends BaseModel
{
    public function getTableName()
    {
        return 'master_category_transaction';
    }
    
    public function getAttributes()
    {
        return [
            'kategoritransaksi_id',
            'kategoritransaksi_kode',
            'kategoritransaksi_nama',
            'additional_data',
            'created_date',
            'is_deleted',
            'is_active'
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
            SELECT
                kategoritransaksi_id,
                kategoritransaksi_kode,
                kategoritransaksi_nama,
                additional_data,
                created_date,
                is_deleted,
                is_active
            FROM kategoritransaksi_m")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'kategoritransaksi_id',
            'kategoritransaksi_kode',
            'kategoritransaksi_nama',
            'additional_data',
            'created_date',
            'is_deleted',
            'is_active'
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
            SELECT
                kategoritransaksi_id,
                kategoritransaksi_kode,
                kategoritransaksi_nama,
                additional_data,
                created_date,
                is_deleted,
                is_active
            FROM kategoritransaksi_m")
        ->queryAll();
    }
}