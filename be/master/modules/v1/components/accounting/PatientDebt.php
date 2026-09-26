<?php

namespace app\modules\v1\components\accounting;

use Yii;

class PatientDebt extends BaseModel
{
    public function getTableName()
    {
        return 'patientdebt';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "tipe",
            "sync_id_api",
            "user_name",
            "trans_no",
            "trans_date",
            "trans_type",
            "reference_no",
            "partner_id",
            "admission_id",
            "admission_no",
            "billing_id",
            "billing_no",
            "patient_name",
            "payment_name",
            "tglproses",
            "edc_machine",
            "amount",
            "note",
            "state",
            "patient_type",
            "keterangan_rekap"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select 
        $sync_type as sync_type,
        date(tglproses) as cutoff_date,
        tipe,
        sync_id_api,
        user_name,
        trans_no,
        trans_date,
        trans_type,
        reference_no,
        partner_id,
        admission_id,
        admission_no,
        billing_id,
        billing_no,
        patient_name,
        payment_name,
        tglproses,
        edc_machine,
        amount,
        note,
        state,
        patient_type,
        keterangan_rekap
        from newodoo_patientdebt_v where date(tglproses) = :date")
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
            'billing_no',
            'user_name ',
            'patient_name',
            'payment_name',
            'tglproses',
            'edc_machine',
            'amount',
            'note',
            'state',
            'jenis',
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
        billing_no,
        user_name ,
        patient_name,
        payment_name,
        tglproses,
        edc_machine,
        amount,
        note,
        state,
        tipe as jenis,
        billing_id,
        partner_id,
        patient_type 
        from newodoo_patientdebt_v where date(tglproses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}