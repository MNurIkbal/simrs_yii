<?php

namespace app\modules\v1\components\accounting;

use Yii;

class GrnReceipt extends BaseModel
{
    public function getTableName()
    {
        return 'grnreceipt';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "jenis",
            "sync_id_api",
            "origin",
            "title",
            "name",
            "rfq",
            "purchase_name",
            "vendor_ref",
            "currency_id",
            "date_order",
            "partner_id",
            "picking_type_id",
            "invoice_status",
            "amount_untaxed",
            "amount_tax",
            "total_discount",
            "total_tampilan",
            "amount_total",
            "state",
            "date_planned",
            "is_return",
            "ppn",
            "pph",
            "wipro_date",
            "no_approval",
            "is_consignment",
            "tipe_rekap",
            "id",
            "batch_no",
            "batch_id",
            "expiry_date",
            "no_faktur",
            "tgl_proses",
            "tgl_suratjalan"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select
        $sync_type as sync_type,
        date(tgl_proses) as cutoff_date,
        jenis,
        sync_id_api,
        origin,
        title,
        name,
        rfq,
        purchase_name,
        vendor_ref,
        currency_id,
        date_order,
        partner_id,
        picking_type_id,
        invoice_status,
        amount_untaxed,
        amount_tax,
        total_discount,
        total_tampilan,
        amount_total,
        state,
        date_planned,
        is_return,
        ppn,
        pph,
        wipro_date,
        no_approval,
        is_consignment,
        tipe_rekap,
        id,
        batch_no,
        batch_id,
        expiry_date,
        no_faktur,
        tgl_proses,
        tgl_suratjalan
        from newodoo_grnreceipt_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'name',
            'vendor_ref',
            'date_order',
            'partner_id',
            'subtotal',
            'amount_untaxed',
            'amount_total',
            'total_ppn',
            'total_discount',
            'date_planned',
            'is_return',
            'ppn',
            'is_consignment',
            'tipe_rekap',
            'location_id',
            'no_faktur',
            'tgl_proses',
            'tgl_suratjalan'
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("select
        sync_id_api,
        $sync_type as sync_type,
        name,
        vendor_ref,
        date_order,
        partner_id,
        subtotal,
        amount_untaxed,
        amount_total,
        total_ppn,
        total_discount,
        date_planned,
        is_return,
        ppn,
        is_consignment,
        tipe_rekap,
        location_id,
        no_faktur,
        tgl_proses,
        tgl_suratjalan
        from newodoo_grnreceipt_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}