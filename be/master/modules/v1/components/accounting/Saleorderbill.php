<?php

namespace app\modules\v1\components\accounting;

use Yii;

class Saleorderbill extends BaseModel
{
    public function getTableName()
    {
        return 'saleorderbill';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "id",
            "sync_id_api",
            "admission_id",
            "admission_no",
            "billno",
            "confirmation_date",
            "cancel_date",
            "partner_id",
            "payer_id",
            "payer_code",
            "payer_type",
            "patient_type",
            "state",
            "personal_amount",
            "payer_amount",
            "total_amount",
            "nama_asuransi",
            "no_asuransi",
            "penjualanresep",
            "is_update",
            "tgl_proses",
            "status_create",
            "subpayer_amount",
            "tariff_id",
            "primary_doc_id",
            "referral_doc_id",
            "referral_number",
            "cob_bill",
            "cob_billno",
            "cob_sequence",
            "is_bpjs",
            "inacbgs_code",
            "inacbgs_amount"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("
        select 
            $sync_type as sync_type,
            date(tgl_proses) as cutoff_date,
            id,
            sync_id_api,
            admission_id,
            admission_no,
            billno,
            confirmation_date,
            cancel_date,
            partner_id,
            payer_id,
            payer_code,
            payer_type,
            patient_type,
            state,
            personal_amount,
            payer_amount,
            total_amount,
            nama_asuransi,
            no_asuransi,
            penjualanresep,
            is_update,
            tgl_proses,
            status_create,
            subpayer_amount,
            tariff_id,
            primary_doc_id,
            referral_doc_id,
            referral_number,
            cob_bill,
            cob_billno,
            cob_sequence,
            is_bpjs,
            inacbgs_code,
            inacbgs_amount
            from newodoo_saleorderbill_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'admission_id',
            'admission_no',
            'billno',
            'confirmation_date',
            'cancel_date',
            'partner_id',
            'payer_id',
            'payer_code',
            'payer_type',
            'patient_type',
            'state',
            'personal_amount',
            'payer_amount',
            'total_amount',
            'nama_asuransi',
            'no_asuransi',
            'penjualanresep',
            'is_update',
            'tgl_proses',
            'status_create',
            'subpayer_amount',
            'primary_doc_id',
            'referral_doc_id',
            'referral_number',
            'cob_bill',
            'cob_billno',
            'receivables_amount',
            'reference_gabungbilling',
            'no_sep',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("
        select 
            sync_id_api,
            $sync_type as sync_type,
            admission_id,
            admission_no,
            billno,
            confirmation_date,
            cancel_date,
            partner_id,
            payer_id,
            payer_code,
            payer_type,
            patient_type,
            state,
            personal_amount,
            payer_amount,
            total_amount,
            nama_asuransi,
            no_asuransi,
            penjualanresep,
            is_update,
            tgl_proses,
            status_create,
            subpayer_amount,
            primary_doc_id,
            referral_doc_id,
            referral_number,
            cob_bill,
            cob_billno,
            receivables_amount,
            reference_gabungbilling,
            no_sep
            from newodoo_saleorderbill_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}