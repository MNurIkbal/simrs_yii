<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterBank extends BaseModel
{
    public function getTableName()
    {
        return 'master_bank';
    }
    
    public function getAttributes()
    {
        return [
            'sync_type',
            'sync_id_api',
            'name',
            'account_name',
            'account_number',
            'account_branch',
            'is_deleted',
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        bank_id as sync_id_api,
        nama_bank as name,
        nama_pemilikrek as account_name,
        no_rekening as account_number,
        cabang as account_branch,
        is_deleted
        FROM bank_m 
        ")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_type',
            'sync_id_api',
            'name',
            'account_name',
            'account_number',
            'account_branch',
            'is_deleted',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        bank_id as sync_id_api,
        nama_bank as name,
        nama_pemilikrek as account_name,
        no_rekening as account_number,
        cabang as account_branch,
        is_deleted
        FROM bank_m
        ")
        ->queryAll();
    }
}