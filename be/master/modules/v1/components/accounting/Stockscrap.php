<?php

namespace app\modules\v1\components\accounting;

use Yii;

class Stockscrap extends BaseModel
{
    public function getTableName()
    {
        return 'stockscrap';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            "cutoff_date",
            "jenis",
            "tipe_rekap",
            "sync_id_api",
            "origin",
            "admission_id",
            "transaction_datetime",
            "transaction_date",
            "trans_type",
            "location_id",
            "scrap_location_id",
            "lot_id",
            "categ_id",
            "service_categ_id",
            "product_id",
            "name",
            "product_uom_id",
            "scrap_qty",
            "cost",
            "cost_total",
            "state",
            "id",
            "tanggal_transaksi"
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("select
        $sync_type as sync_type,
        date(tanggal_transaksi) as cutoff_date,
        jenis,
        tipe_rekap,
        sync_id_api,
        origin,
        admission_id,
        transaction_datetime,
        transaction_date,
        trans_type,
        location_id,
        scrap_location_id,
        lot_id,
        categ_id,
        service_categ_id,
        product_id,
        name,
        product_uom_id,
        scrap_qty,
        cost,
        cost_total,
        state,
        id,
        tanggal_transaksi
        from newodoo_stockscrap_v where date(tanggal_transaksi) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'origin',
            'admission_id',
            'tanggal_transaksi',
            'transaction_datetime',
            'transaction_date',
            'trans_type',
            'location_id',
            'scrap_location_id',
            'lot_id',
            'categ_id',
            'service_categ_id',
            'product_id',
            'name',
            'product_uom_id',
            'scrap_qty',
            'cost',
            'cost_total',
            'state',
            'id',
            'jenis',
            'tipe_rekap',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("select
        sync_id_api,
        $sync_type as sync_type,
        origin,
        admission_id,
        tanggal_transaksi,
        transaction_datetime,
        transaction_date,
        trans_type,
        location_id,
        scrap_location_id,
        lot_id,
        categ_id,
        service_categ_id,
        product_id,
        name,
        product_uom_id,
        scrap_qty,
        cost,
        cost_total,
        state,
        id,
        jenis,
        tipe_rekap 
        from newodoo_stockscrap_v where date(tanggal_transaksi) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}