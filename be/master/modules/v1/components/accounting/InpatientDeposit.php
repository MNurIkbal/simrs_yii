<?php

namespace app\modules\v1\components\accounting;

use Yii;

class InpatientDeposit extends BaseModel
{
    public function getTableName()
    {
        return 'inpatientdeposit';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "sync_id_api",
            "user_name",
            "admission_id",
            "trans_no",
            "trans_date",
            "trans_type",
            "reference_no",
            "admission_no",
            "patient_name",
            "payment_name",
            "tglproses",
            "edc_machine",
            "amount",
            "note",
            "state",
            "id",
            "tipe_rekap",
            "billing_id",
            "partner_id",
            "patient_type"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select
        $sync_type as sync_type,
        date(tglproses) as cutoff_date,
        sync_id_api,
        user_name,
        admission_id,
        trans_no,
        trans_date,
        trans_type,
        reference_no,
        admission_no,
        patient_name,
        payment_name,
        tglproses,
        edc_machine,
        amount,
        note,
        state,
        id,
        tipe_rekap,
        billing_id,
        partner_id,
        patient_type
        from newodoo_inpatientdeposit_v where date(tglproses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'trans_no',
            'trans_type',
            'trans_date',
            'admission_id',
            'admission_no',
            'reference_no',
            'user_name ',
            'patient_name',
            'payment_name',
            'tglproses',
            'edc_machine',
            'amount',
            'note',
            'state',
            'id',
            'tipe_rekap as jenis',
            'billing_id',
            'partner_id',
            'patient_type',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("select
        sync_id_api,
        $sync_type as sync_type,
        trans_no,
        trans_type,
        trans_date,
        admission_id,
        admission_no,
        reference_no,
        user_name ,
        patient_name,
        payment_name,
        tglproses,
        edc_machine,
        amount,
        note,
        state,
        id,
        tipe_rekap as jenis,
        billing_id,
        partner_id,
        patient_type 
        from newodoo_inpatientdeposit_v where date(tglproses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}