<?php

namespace app\modules\v1\components\accounting;

use Yii;

class Saleorder extends BaseModel
{
    public function getTableName()
    {
        return 'saleorder';
    }
    
    public function getAttributes()
    {
        return [
            "cutoff_date",
            "sync_type",
            "name",
            "jenis",
            "sync_id_api",
            "billno",
            "confirmation_date",
            "partner_id",
            "date_order",
            "patient_type",
            "payer_id",
            "payer_code",
            "payer_type",
            "state",
            "nama_asuransi",
            "no_asuransi",
            "personal_amount",
            "payer_amount",
            "total_amount",
            "subpayer_amount",
            "primary_doc_id",
            "referral_doc_id",
            "referral_number",
            "secondary_diagnosis_code",
            "secondary_diagnosis_name",
            "primary_diagnosis_code",
            "primary_diagnosis_name",
            "repeat_diagnosis_code",
            "repeat_diagnosis_name",
            "ref_admission_no",
            "admission_type",
            "discharge_date",
            "discharge_reason",
            "tariff_id",
            "bed_type_id",
            "eligible_bed_type_id",
            "alloted_bed_type_id"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("
        select
        date(date_order) as cutoff_date,
        $sync_type as sync_type,
        name,
        jenis,
        sync_id_api,
        billno,
        confirmation_date,
        partner_id,
        date_order,
        patient_type,
        payer_id,
        payer_code,
        payer_type,
        state,
        nama_asuransi,
        no_asuransi,
        personal_amount,
        payer_amount,
        total_amount,
        subpayer_amount,
        primary_doc_id,
        referral_doc_id,
        referral_number,
        secondary_diagnosis_code,
        secondary_diagnosis_name,
        primary_diagnosis_code,
        primary_diagnosis_name,
        repeat_diagnosis_code,
        repeat_diagnosis_name,
        ref_admission_no,
        admission_type,
        discharge_date,
        discharge_reason,
        tariff_id,
        bed_type_id,
        eligible_bed_type_id,
        alloted_bed_type_id
        from newodoo_saleorder_v where date(date_order) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'billno',
            'confirmation_date',
            'partner_id',
            'date_order',
            'patient_type',
            'payer_id',
            'payer_code',
            'payer_type',
            'nama_asuransi',
            'no_asuransi',
            'personal_amount',
            'payer_amount',
            'total_amount',
            'subpayer_amount',
            'primary_doc_id',
            'referral_doc_id',
            'referral_number',
            'ref_admission_no',
            'admission_type',
            'discharge_date',
            'discharge_reason',
            'bed_type_id',
            'eligible_bed_type_id',
            'alloted_bed_type_id',
            'state',
            'jenis',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("
        select
        sync_id_api,
        $sync_type as sync_type,
        name,
        billno,
        confirmation_date,
        partner_id,
        date_order,
        patient_type,
        payer_id,
        payer_code,
        payer_type,
        nama_asuransi,
        no_asuransi,
        personal_amount,
        payer_amount,
        total_amount,
        subpayer_amount,
        primary_doc_id,
        referral_doc_id,
        referral_number,
        ref_admission_no,
        admission_type,
        discharge_date,
        discharge_reason,
        bed_type_id,
        eligible_bed_type_id,
        alloted_bed_type_id,
        state,
        jenis
        from newodoo_saleorder_v where date(date_order) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}