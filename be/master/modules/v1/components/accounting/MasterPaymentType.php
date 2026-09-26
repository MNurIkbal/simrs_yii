<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterPaymentType extends BaseModel
{
    public function getTableName()
    {
        return 'master_paymenttype';
    }
    
    public function getAttributes()
    {
        return [
            'sync_type',
            'sync_id_api',
            'code',
            'name',
            'bank_id',
            'trans_type',
            'is_deleted',
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        jenisnontunai_id as sync_id_api,
        kode as code,
        nama as name,
        bank_id,
        tipe.lookup_name as trans_type,
        jenisnontunai_m.is_deleted
        from jenisnontunai_m 
        left join lookup_m tipe on tipe.lookup_id = jenisnontunai_m.tipe_pembayaran
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
            'trans_type',
            'is_deleted',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        jenisnontunai_id as sync_id_api,
        kode as code,
        nama as name,
        bank_id,
        tipe.lookup_name as trans_type,
        jenisnontunai_m.is_deleted
        from jenisnontunai_m 
        left join lookup_m tipe on tipe.lookup_id = jenisnontunai_m.tipe_pembayaran
        ")
        ->queryAll();
    }
}