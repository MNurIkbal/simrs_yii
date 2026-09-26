<?php

namespace app\modules\v1\components\accounting;

use Yii;

class GrnReceiptDetail extends BaseModel
{
    public function getTableName()
    {
        return 'grnreceiptdetail';
    }
    
    public function getAttributes()
    {
        return [
           "sync_type",
            "cutoff_date",
            "jenis",
            "sync_id_api",
            "picking_id",
            "order_id",
            "sequence",
            "partner_id",
            "product_id",
            "product_categ_id",
            "name",
            "normal_price",
            "price_unit",
            "discount_value",
            "discount_persen",
            "product_uom",
            "is_conversion",
            "is_consignment",
            "converted",
            "convertion_rate",
            "product_qty",
            "qty_received",
            "taxes_id",
            "price_tax",
            "has_tax",
            "price_total",
            "price_subtotal",
            "date_planned",
            "currency_id",
            "state",
            "id",
            "tipe_rekap",
            "batch_no",
            "batch_id",
            "expiry_date",
            "ppn",
            "pph"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select
        $sync_type as sync_type,
        date(date_planned) as cutoff_date,
        jenis,
        sync_id_api,
        picking_id,
        order_id,
        sequence,
        partner_id,
        product_id,
        product_categ_id,
        name,
        normal_price,
        price_unit,
        discount_value,
        discount_persen,
        product_uom,
        is_conversion,
        is_consignment,
        converted,
        convertion_rate,
        product_qty,
        qty_received,
        taxes_id,
        price_tax,
        has_tax,
        price_total,
        price_subtotal,
        date_planned,
        currency_id,
        state,
        id,
        tipe_rekap,
        batch_no,
        batch_id,
        expiry_date,
        ppn,
        pph
        from newodoo_grnreceiptdetail_v where date(date_planned) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'order_id',
            'partner_id',
            'product_id',
            'product_categ_id',
            'name',
            'price_unit',
            'discount_value',
            'discount_persen',
            'product_uom',
            'product_qty',
            'qty_received',
            'taxes_id',
            'price_tax',
            'price_total',
            'price_subtotal',
            'date_planned',
            'tipe_rekap',
            'expiry_date',
            'ppn',
            'qty_grn',
            'uom_grn',
            'uom_grn_id',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("select
        sync_id_api,
        $sync_type as sync_type,
        order_id,
        partner_id,
        product_id,
        product_categ_id,
        name,
        price_unit,
        discount_value,
        discount_persen,
        product_uom,
        product_qty,
        qty_received,
        taxes_id,
        price_tax,
        price_total,
        price_subtotal,
        date_planned,
        tipe_rekap,
        expiry_date,
        ppn,
        qty_grn,
        uom_grn,
        uom_grn_id
        from newodoo_grnreceiptdetail_v where date(date_planned) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}