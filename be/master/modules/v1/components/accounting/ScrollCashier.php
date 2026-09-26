<?php

namespace app\modules\v1\components\accounting;

use Yii;

class ScrollCashier extends BaseModel
{
    public function getTableName()
    {
        return 'scroll_cashier';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "sync_id_api",
            "user_name",
            "trans_type",
            "facility_name",
            "tglproses",
            "payment_name",
            "edc_machine",
            "total_collect",
            "note",
            "admission_id",
            "admission_no",
            "state",
            "keterangan",
            "id",
            "is_update",
            "tipe_rekap",
            "billing_id",
            "paymenttype_id",
            "edc_id",
            "bank_id"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select
        $sync_type as sync_type,
        date(tglproses) as cutoff_date,
        sync_id_api,
        user_name,
        trans_type,
        facility_name,
        tglproses,
        payment_name,
        edc_machine,
        total_collect,
        note,
        admission_id,
        admission_no,
        state,
        keterangan,
        id,
        is_update,
        tipe_rekap,
        billing_id,
        paymenttype_id,
        edc_id,
        bank_id
        from newodoo_scroll_v where date(tglproses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'user_name',
            'trans_type',
            'facility_name',
            'tglproses',
            'payment_name',
            'edc_machine',
            'total_collect',
            'note',
            'admission_id',
            'admission_no',
            'billing_id',
            'keterangan',
            'id',
            'tipe_rekap',
            'paymenttype_id',
            'edc_id',
            'bank_id'
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("select
        sync_id_api,
        $sync_type as sync_type,
        user_name,
        trans_type ,
        facility_name ,
        tglproses ,
        payment_name ,
        edc_machine ,
        total_collect ,
        note ,
        admission_id ,
        admission_no ,
        billing_id ,
        keterangan ,
        id,
        tipe_rekap,
        paymenttype_id,
        edc_id,
        bank_id
        from newodoo_scroll_v where date(tglproses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}