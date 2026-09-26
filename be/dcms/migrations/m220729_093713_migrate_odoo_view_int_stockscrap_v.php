<?php

use yii\db\Migration;

/**
 * Class m220729_093713_migrate_odoo_view_int_stockscrap_v
 */
class m220729_093713_migrate_odoo_view_int_stockscrap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_stockscrap_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_stockscrap_v" AS  SELECT \'obat\'::text AS jenis,
                \'adj_keluar\'::text AS tipe_rekap,
                concat(\'AJK\', adjusmenobatkeluar_r.adjusmenobatkeluar_id) AS sync_id_api,
                6 AS sync_type,
                adjusmenobat_t.no_adjusmen AS origin,
                NULL::text AS admission_id,
                adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
                to_char(adjusmenobat_t.tgl_adjusmen, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'AI\'::text AS trans_type,
                adjusmenobat_t.ruangan_adjusmen_id::character varying AS location_id,
                \'scrap\'::text AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'OBT\', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', adjusmenobatkeluar_r.obatalkes_id) AS product_id,
                concat(adjusmenobat_t.no_adjusmen, \'-\', obatalkes_m.obatalkes_nama) AS name,
                adjusmenobatkeluar_r.satuankecil_id AS product_uom_id,
                adjusmenobatkeluar_r.qty_konversi AS scrap_qty,
                COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost,
                adjusmenobatkeluar_r.qty_konversi::double precision * COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost_total,
                \'done\'::text AS state,
                adjusmenobatkeluar_r.id,
                adjusmenobatkeluar_r.is_sending,
                adjusmenobatkeluar_r.is_sent,
                adjusmenobatkeluar_r.sync_respon,
                    CASE
                        WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL AND adjusmenobatkeluar_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                adjusmenobatkeluar_r.tgl_proses AS tanggal_transaksi
               FROM adjusmenobatkeluar_r
                 JOIN ( SELECT a.adjusmenobat_id,
                        a.no_adjusmen,
                        a.tgl_adjusmen,
                        a.ruangan_adjusmen_id
                       FROM adjusmenobat_t a) adjusmenobat_t ON adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
                 JOIN ( SELECT a.obatalkes_id,
                        a.jenisobatalkes_id,
                        a.obatalkes_nama,
                        a.harganetto
                       FROM obatalkes_m a) obatalkes_m ON adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT a.jenisobatalkes_id,
                        a.servicecategory_id
                       FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 JOIN ( SELECT a.adjusmenobatkeluar_id,
                        a.nobatch
                       FROM stokobatalkes_t a
                      WHERE a.is_deleted = false
                      GROUP BY a.adjusmenobatkeluar_id, a.nobatch) stok ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id
                 LEFT JOIN ( SELECT a.stokobatalkes_id,
                        a.adjusmenobatkeluar_id,
                        logasetobat_r.weighted_avg
                       FROM stokobatalkes_t a
                         LEFT JOIN ( SELECT a1.stokobatalkes_id,
                                a1.weighted_avg
                               FROM logasetobat_r a1) logasetobat_r ON a.stokobatalkes_id = logasetobat_r.stokobatalkes_id) logobat ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = logobat.adjusmenobatkeluar_id
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                \'adj_masuk\'::text AS tipe_rekap,
                concat(\'AJM\', adjusmenobatmasuk_r.adjusmenobatmasuk_id) AS sync_id_api,
                6 AS sync_type,
                adjusmenobat_t.no_adjusmen AS origin,
                NULL::text AS admission_id,
                adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
                to_char(adjusmenobat_t.tgl_adjusmen, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'AR\'::text AS trans_type,
                \'scrap\'::character varying AS location_id,
                adjusmenobat_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'OBT\', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', adjusmenobatmasuk_r.obatalkes_id) AS product_id,
                concat(adjusmenobat_t.no_adjusmen, \'-\', obatalkes_m.obatalkes_nama) AS name,
                adjusmenobatmasuk_r.satuankecil_id AS product_uom_id,
                adjusmenobatmasuk_r.qty_konversi AS scrap_qty,
                COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost,
                adjusmenobatmasuk_r.qty_konversi::double precision * COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost_total,
                \'done\'::text AS state,
                adjusmenobatmasuk_r.id,
                adjusmenobatmasuk_r.is_sending,
                adjusmenobatmasuk_r.is_sent,
                adjusmenobatmasuk_r.sync_respon,
                    CASE
                        WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL AND adjusmenobatmasuk_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                adjusmenobatmasuk_r.tgl_proses AS tanggal_transaksi
               FROM adjusmenobatmasuk_r
                 JOIN ( SELECT b.adjusmenobat_id,
                        b.no_adjusmen,
                        b.tgl_adjusmen,
                        b.ruangan_adjusmen_id
                       FROM adjusmenobat_t b) adjusmenobat_t ON adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
                 JOIN ( SELECT b.obatalkes_id,
                        b.obatalkes_nama,
                        b.jenisobatalkes_id,
                        b.harganetto
                       FROM obatalkes_m b) obatalkes_m ON adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT b.jenisobatalkes_id,
                        b.servicecategory_id
                       FROM jenisobatalkes_m b) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 JOIN ( SELECT b.adjusmenobatmasuk_id,
                        b.nobatch
                       FROM stokobatalkes_t b
                      WHERE b.is_deleted = false
                      GROUP BY b.adjusmenobatmasuk_id, b.nobatch) stok ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id
                 LEFT JOIN ( SELECT b.stokobatalkes_id,
                        b.adjusmenobatmasuk_id,
                        logasetobat_r.weighted_avg
                       FROM stokobatalkes_t b
                         LEFT JOIN ( SELECT b1.stokobatalkes_id,
                                b1.weighted_avg
                               FROM logasetobat_r b1) logasetobat_r ON b.stokobatalkes_id = logasetobat_r.stokobatalkes_id) logobat ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = logobat.adjusmenobatmasuk_id
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                \'pemusnahan_obat\'::text AS tipe_rekap,
                concat(\'PMO\', pemusnahanobatdetail_r.pemusnahanobatdetail_id) AS sync_id_api,
                6 AS sync_type,
                pemusnahanobat_t.nopemusnahan AS origin,
                NULL::text AS admission_id,
                pemusnahanobat_t.tglpemusnahan AS transaction_datetime,
                to_char(pemusnahanobat_t.tglpemusnahan, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'BR\'::text AS trans_type,
                pemusnahanobat_t.ruangan_id::character varying AS location_id,
                \'scrap\'::character varying AS scrap_location_id,
                pemusnahanobatdetail_r.nobatch AS lot_id,
                concat(\'OBT\', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', pemusnahanobatdetail_r.obatalkes_id) AS product_id,
                concat(pemusnahanobat_t.nopemusnahan, \'-\', obatalkes_m.obatalkes_nama) AS name,
                pemusnahanobatdetail_r.satuan_id AS product_uom_id,
                pemusnahanobatdetail_r.jumlah AS scrap_qty,
                COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost,
                pemusnahanobatdetail_r.jumlah * COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost_total,
                \'done\'::text AS state,
                pemusnahanobatdetail_r.id,
                pemusnahanobatdetail_r.is_sending,
                pemusnahanobatdetail_r.is_sent,
                pemusnahanobatdetail_r.sync_respon,
                    CASE
                        WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL AND pemusnahanobatdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                pemusnahanobatdetail_r.tgl_proses AS tanggal_transaksi
               FROM pemusnahanobatdetail_r
                 JOIN ( SELECT c.pemusnahanobat_id,
                        c.nopemusnahan,
                        c.tglpemusnahan,
                        c.ruangan_id
                       FROM pemusnahanobat_t c) pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
                 JOIN ( SELECT c.obatalkes_id,
                        c.obatalkes_nama,
                        c.jenisobatalkes_id,
                        c.harganetto
                       FROM obatalkes_m c) obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT c.jenisobatalkes_id,
                        c.servicecategory_id
                       FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 JOIN ( SELECT c.pemusnahanobatdetail_id
                       FROM stokobatalkes_t c
                      WHERE c.is_deleted = false
                      GROUP BY c.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
                 LEFT JOIN ( SELECT c.stokobatalkes_id,
                        c.pemusnahanobatdetail_id,
                        c.obatalkes_id,
                        c.ruangan_id,
                        logasetobat_r.weighted_avg
                       FROM stokobatalkes_t c
                         LEFT JOIN ( SELECT c1.stokobatalkes_id,
                                c1.weighted_avg,
                                c1.obatalkes_id,
                                c1.ruangan_id
                               FROM logasetobat_r c1) logasetobat_r ON c.obatalkes_id = logasetobat_r.obatalkes_id AND c.ruangan_id = logasetobat_r.ruangan_id
                      WHERE logasetobat_r.stokobatalkes_id < c.stokobatalkes_id
                      ORDER BY c.stokobatalkes_id DESC
                     LIMIT 1) logobat ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = logobat.pemusnahanobatdetail_id
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                \'pemakaian_obat\'::text AS tipe_rekap,
                concat(\'PKO\', pemakaianobatdetail_r.pemakaianobatdetail_id) AS sync_id_api,
                6 AS sync_type,
                pemakaianobat_t.nopemakaian_obat AS origin,
                NULL::text AS admission_id,
                pemakaianobat_t.tglpemakaianobat AS transaction_datetime,
                to_char(pemakaianobat_t.tglpemakaianobat::timestamp with time zone, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'SC\'::text AS trans_type,
                pemakaianobat_t.ruangan_id::character varying AS location_id,
                \'scrap\'::text AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'OBT\', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', pemakaianobatdetail_r.obatalkes_id) AS product_id,
                concat(pemakaianobat_t.nopemakaian_obat, \'-\', obatalkes_m.obatalkes_nama) AS name,
                pemakaianobatdetail_r.satuankecil_id AS product_uom_id,
                pemakaianobatdetail_r.qty_satuanpakai AS scrap_qty,
                COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost,
                pemakaianobatdetail_r.qty_satuanpakai::double precision * COALESCE(logobat.weighted_avg::double precision, obatalkes_m.harganetto) AS cost_total,
                \'done\'::text AS state,
                pemakaianobatdetail_r.id,
                pemakaianobatdetail_r.is_sending,
                pemakaianobatdetail_r.is_sent,
                pemakaianobatdetail_r.sync_respon,
                    CASE
                        WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL AND pemakaianobatdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                pemakaianobatdetail_r.tgl_proses AS tanggal_transaksi
               FROM pemakaianobatdetail_r
                 JOIN ( SELECT d.pemakaianobat_id,
                        d.nopemakaian_obat,
                        d.tglpemakaianobat,
                        d.ruangan_id
                       FROM pemakaianobat_t d) pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
                 JOIN ( SELECT d.obatalkes_id,
                        d.obatalkes_nama,
                        d.jenisobatalkes_id,
                        d.harganetto
                       FROM obatalkes_m d) obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT d.jenisobatalkes_id,
                        d.servicecategory_id
                       FROM jenisobatalkes_m d) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 JOIN ( SELECT d.pemakaianobatdetail_id,
                        d.nobatch
                       FROM stokobatalkes_t d
                      WHERE d.is_deleted = false
                      GROUP BY d.pemakaianobatdetail_id, d.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id
                 LEFT JOIN ( SELECT d.stokobatalkes_id,
                        d.pemakaianobatdetail_id,
                        d.obatalkes_id,
                        d.ruangan_id,
                        logasetobat_r.weighted_avg
                       FROM stokobatalkes_t d
                         LEFT JOIN ( SELECT d1.stokobatalkes_id,
                                d1.weighted_avg,
                                d1.obatalkes_id,
                                d1.ruangan_id
                               FROM logasetobat_r d1) logasetobat_r ON d.obatalkes_id = logasetobat_r.obatalkes_id AND d.ruangan_id = logasetobat_r.ruangan_id
                      WHERE logasetobat_r.stokobatalkes_id < d.stokobatalkes_id
                      ORDER BY d.stokobatalkes_id DESC
                     LIMIT 1) logobat ON pemakaianobatdetail_r.pemakaianobatdetail_id = logobat.pemakaianobatdetail_id
            UNION ALL
             SELECT \'obat\'::text AS jenis,
                \'stokopname_obat\'::text AS tipe_rekap,
                concat(\'STO\', stokopnamedetail_r.stokopnamedetail_id) AS sync_id_api,
                6 AS sync_type,
                stokopname_t.nostokopname AS origin,
                NULL::text AS admission_id,
                stokopname_t.tglstokopname AS transaction_datetime,
                to_char(stokopname_t.tglstokopname::timestamp with time zone, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN \'AI\'::text
                        ELSE \'AR\'::text
                    END AS trans_type,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN stokopname_t.ruangan_id::text
                        ELSE \'scrap\'::text
                    END AS location_id,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN \'scrap\'::text
                        ELSE stokopname_t.ruangan_id::text
                    END AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'OBT\', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', stokopnamedetail_r.obatalkes_id) AS product_id,
                concat(stokopname_t.nostokopname, \'-\', obatalkes_m.obatalkes_nama) AS name,
                obatalkes_m.satuankecil_id AS product_uom_id,
                    CASE
                        WHEN stokopnamedetail_t.revisi_stok IS NULL OR stokopnamedetail_t.revisi_stok = 0::double precision THEN abs(stokopnamedetail_t.selisih_akhir)
                        ELSE abs(stokopnamedetail_t.stok)
                    END AS scrap_qty,
                obatalkes_m.harganetto AS cost,
                    CASE
                        WHEN stokopnamedetail_t.revisi_stok IS NULL OR stokopnamedetail_t.revisi_stok = 0::double precision THEN abs(stokopnamedetail_t.selisih_akhir) * obatalkes_m.harganetto
                        ELSE abs(stokopnamedetail_t.stok) * obatalkes_m.harganetto
                    END AS cost_total,
                \'done\'::text AS state,
                stokopnamedetail_r.id,
                stokopnamedetail_r.is_sending,
                stokopnamedetail_r.is_sent,
                stokopnamedetail_r.sync_respon,
                    CASE
                        WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = false AND stokopnamedetail_r.id_sync_sercon IS NOT NULL AND stokopnamedetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN stokopnamedetail_r.is_sending = false AND stokopnamedetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = false AND stokopnamedetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                stokopnamedetail_r.tgl_proses AS tanggal_transaksi
               FROM stokopnamedetail_r
                 JOIN ( SELECT e.stokopnamedetail_id,
                        e.revisi_stok,
                        e.selisih_akhir,
                        e.revisi_stok - e.volume_sistem AS stok
                       FROM stokopnamedetail_t e) stokopnamedetail_t ON stokopnamedetail_r.stokopnamedetail_id = stokopnamedetail_t.stokopnamedetail_id
                 JOIN ( SELECT e.stokopname_id,
                        e.nostokopname,
                        e.tglstokopname,
                        e.ruangan_id
                       FROM stokopname_t e) stokopname_t ON stokopnamedetail_r.stokopname_id = stokopname_t.stokopname_id
                 JOIN ( SELECT e.obatalkes_id,
                        e.obatalkes_kode,
                        e.obatalkes_nama,
                        e.jenisobatalkes_id,
                        e.satuankecil_id,
                        e.harganetto
                       FROM obatalkes_m e) obatalkes_m ON stokopnamedetail_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT e.jenisobatalkes_id,
                        e.jenisobatalkes_nama,
                        e.servicecategory_id
                       FROM jenisobatalkes_m e) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 JOIN ( SELECT e.stokopnamedetail_id,
                        e.nobatch,
                        sum(e.qtystok_in) AS qty_in
                       FROM stokobatalkes_t e
                      WHERE e.is_deleted = false
                      GROUP BY e.stokopnamedetail_id, e.nobatch) stok ON stokopnamedetail_r.stokopnamedetail_id = stok.stokopnamedetail_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                \'adj_keluar_barang\'::text AS tipe_rekap,
                concat(\'AJK\', adjusmenbarangkeluar_r.adjusmenbarangkeluar_id) AS sync_id_api,
                6 AS sync_type,
                adjusmenbarang_t.no_adjusmen AS origin,
                NULL::text AS admission_id,
                adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
                to_char(adjusmenbarang_t.tgl_adjusmen, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'AI\'::text AS trans_type,
                adjusmenbarang_t.ruangan_adjusmen_id::character varying AS location_id,
                \'scrap\'::text AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'BRG\', kelompokbarang_m.kelompokbarang_id) AS categ_id,
                concat(\'CATEG\', kelompokbarang_m.servicecategory_id) AS service_categ_id,
                concat(\'BRG\', adjusmenbarangkeluar_r.barang_id) AS product_id,
                concat(adjusmenbarang_t.no_adjusmen, \'-\', barang_m.barang_nama) AS name,
                adjusmenbarangkeluar_r.satuankecil_id AS product_uom_id,
                adjusmenbarangkeluar_r.qty_konversi AS scrap_qty,
                barang_m.barang_harganetto AS cost,
                adjusmenbarangkeluar_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
                \'done\'::text AS state,
                adjusmenbarangkeluar_r.id,
                adjusmenbarangkeluar_r.is_sending,
                adjusmenbarangkeluar_r.is_sent,
                adjusmenbarangkeluar_r.sync_respon,
                    CASE
                        WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL AND adjusmenbarangkeluar_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN adjusmenbarangkeluar_r.is_sending = false AND adjusmenbarangkeluar_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                adjusmenbarangkeluar_r.tgl_proses AS tanggal_transaksi
               FROM adjusmenbarangkeluar_r
                 JOIN ( SELECT a.adjusmenbarang_id,
                        a.no_adjusmen,
                        a.tgl_adjusmen,
                        a.ruangan_adjusmen_id
                       FROM adjusmenbarang_t a) adjusmenbarang_t ON adjusmenbarangkeluar_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
                 JOIN ( SELECT a.barang_id,
                        a.barang_nama,
                        a.kelompokbarang_id,
                        a.barang_harganetto
                       FROM barang_m a) barang_m ON adjusmenbarangkeluar_r.barang_id = barang_m.barang_id
                 JOIN ( SELECT a.kelompokbarang_id,
                        a.servicecategory_id
                       FROM kelompokbarang_m a) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                 JOIN ( SELECT a.adjusmenbarangkeluar_id,
                        a.nobatch
                       FROM stokbarang_t a
                      WHERE a.is_deleted = false
                      GROUP BY a.adjusmenbarangkeluar_id, a.nobatch) stok ON adjusmenbarangkeluar_r.adjusmenbarangkeluar_id = stok.adjusmenbarangkeluar_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                \'adj_masuk_barang\'::text AS tipe_rekap,
                concat(\'AJM\', adjusmenbarangmasuk_r.adjusmenbarangmasuk_id) AS sync_id_api,
                6 AS sync_type,
                adjusmenbarang_t.no_adjusmen AS origin,
                NULL::text AS admission_id,
                adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
                to_char(adjusmenbarang_t.tgl_adjusmen, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'AR\'::text AS trans_type,
                \'scrap\'::character varying AS location_id,
                adjusmenbarang_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'BRG\', kelompokbarang_m.kelompokbarang_id) AS categ_id,
                concat(\'CATEG\', kelompokbarang_m.servicecategory_id) AS service_categ_id,
                concat(\'BRG\', adjusmenbarangmasuk_r.barang_id) AS product_id,
                concat(adjusmenbarang_t.no_adjusmen, \'-\', barang_m.barang_nama) AS name,
                adjusmenbarangmasuk_r.satuankecil_id AS product_uom_id,
                adjusmenbarangmasuk_r.qty_konversi AS scrap_qty,
                barang_m.barang_harganetto AS cost,
                adjusmenbarangmasuk_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
                \'done\'::text AS state,
                adjusmenbarangmasuk_r.id,
                adjusmenbarangmasuk_r.is_sending,
                adjusmenbarangmasuk_r.is_sent,
                adjusmenbarangmasuk_r.sync_respon,
                    CASE
                        WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL AND adjusmenbarangmasuk_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN adjusmenbarangmasuk_r.is_sending = false AND adjusmenbarangmasuk_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                adjusmenbarangmasuk_r.tgl_proses AS tanggal_transaksi
               FROM adjusmenbarangmasuk_r
                 JOIN ( SELECT b.adjusmenbarang_id,
                        b.no_adjusmen,
                        b.tgl_adjusmen,
                        b.ruangan_adjusmen_id
                       FROM adjusmenbarang_t b) adjusmenbarang_t ON adjusmenbarangmasuk_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
                 JOIN ( SELECT b.barang_id,
                        b.barang_nama,
                        b.kelompokbarang_id,
                        b.barang_harganetto
                       FROM barang_m b) barang_m ON adjusmenbarangmasuk_r.barang_id = barang_m.barang_id
                 JOIN ( SELECT b.kelompokbarang_id,
                        b.servicecategory_id
                       FROM kelompokbarang_m b) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                 JOIN ( SELECT b.adjusmenbarangmasuk_id,
                        b.nobatch
                       FROM stokbarang_t b
                      WHERE b.is_deleted = false
                      GROUP BY b.adjusmenbarangmasuk_id, b.nobatch) stok ON adjusmenbarangmasuk_r.adjusmenbarangmasuk_id = stok.adjusmenbarangmasuk_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                \'pemusnahan_barang\'::text AS tipe_rekap,
                concat(\'PMB\', pemusnahanbarangdetail_r.pemusnahanbarangdetail_id) AS sync_id_api,
                6 AS sync_type,
                pemusnahanbarang_t.nopemusnahan AS origin,
                NULL::text AS admission_id,
                pemusnahanbarang_t.tglpemusnahan AS transaction_datetime,
                to_char(pemusnahanbarang_t.tglpemusnahan, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'BR\'::text AS trans_type,
                pemusnahanbarang_t.ruangan_id::character varying AS location_id,
                \'scrap\'::character varying AS scrap_location_id,
                pemusnahanbarangdetail_r.nobatch AS lot_id,
                concat(\'BRG\', kelompokbarang_m.kelompokbarang_id) AS categ_id,
                concat(\'CATEG\', kelompokbarang_m.servicecategory_id) AS service_categ_id,
                concat(\'BRG\', pemusnahanbarangdetail_r.barang_id) AS product_id,
                concat(pemusnahanbarang_t.nopemusnahan, \'-\', barang_m.barang_nama) AS name,
                barang_m.satuankecil_id AS product_uom_id,
                pemusnahanbarangdetail_r.jumlah AS scrap_qty,
                barang_m.barang_harganetto AS cost,
                pemusnahanbarangdetail_r.jumlah * barang_m.barang_harganetto AS cost_total,
                \'done\'::text AS state,
                pemusnahanbarangdetail_r.id,
                pemusnahanbarangdetail_r.is_sending,
                pemusnahanbarangdetail_r.is_sent,
                pemusnahanbarangdetail_r.sync_respon,
                    CASE
                        WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL AND pemusnahanbarangdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pemusnahanbarangdetail_r.is_sending = false AND pemusnahanbarangdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                pemusnahanbarangdetail_r.tgl_proses AS tanggal_transaksi
               FROM pemusnahanbarangdetail_r
                 JOIN ( SELECT c.pemusnahanbarang_id,
                        c.nopemusnahan,
                        c.tglpemusnahan,
                        c.ruangan_id
                       FROM pemusnahanbarang_t c) pemusnahanbarang_t ON pemusnahanbarangdetail_r.pemusnahanbarang_id = pemusnahanbarang_t.pemusnahanbarang_id
                 JOIN ( SELECT c.barang_id,
                        c.barang_nama,
                        c.kelompokbarang_id,
                        c.barang_harganetto,
                        c.satuankecil_id
                       FROM barang_m c) barang_m ON pemusnahanbarangdetail_r.barang_id = barang_m.barang_id
                 JOIN ( SELECT c.kelompokbarang_id,
                        c.servicecategory_id
                       FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                 JOIN ( SELECT c.pemusnahanbarangdetail_id
                       FROM stokbarang_t c
                      WHERE c.is_deleted = false
                      GROUP BY c.pemusnahanbarangdetail_id) stok ON pemusnahanbarangdetail_r.pemusnahanbarangdetail_id = stok.pemusnahanbarangdetail_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                \'pemakaian_barang\'::text AS tipe_rekap,
                concat(\'PKB\', pemakaianbarangdetail_r.pemakaianbarangdetail_id) AS sync_id_api,
                6 AS sync_type,
                pemakaianbarang_t.no_pemakaianbarang AS origin,
                NULL::text AS admission_id,
                pemakaianbarang_t.tgl_pemakaianbarang AS transaction_datetime,
                to_char(pemakaianbarang_t.tgl_pemakaianbarang::timestamp with time zone, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                \'SC\'::text AS trans_type,
                pemakaianbarang_t.ruangan_id::character varying AS location_id,
                \'scrap\'::text AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'BRG\', kelompokbarang_m.kelompokbarang_id) AS categ_id,
                concat(\'CATEG\', kelompokbarang_m.servicecategory_id) AS service_categ_id,
                concat(\'BRG\', pemakaianbarangdetail_r.barang_id) AS product_id,
                concat(pemakaianbarang_t.no_pemakaianbarang, \'-\', barang_m.barang_nama) AS name,
                pemakaianbarangdetail_r.satuankecil_id AS product_uom_id,
                pemakaianbarangdetail_r.jumlah_pakai AS scrap_qty,
                barang_m.barang_harganetto AS cost,
                pemakaianbarangdetail_r.jumlah_pakai::double precision * barang_m.barang_harganetto AS cost_total,
                \'done\'::text AS state,
                pemakaianbarangdetail_r.id,
                pemakaianbarangdetail_r.is_sending,
                pemakaianbarangdetail_r.is_sent,
                pemakaianbarangdetail_r.sync_respon,
                    CASE
                        WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL AND pemakaianbarangdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pemakaianbarangdetail_r.is_sending = false AND pemakaianbarangdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                pemakaianbarangdetail_r.tgl_proses AS tanggal_transaksi
               FROM pemakaianbarangdetail_r
                 JOIN ( SELECT d.pemakaianbarang_id,
                        d.no_pemakaianbarang,
                        d.tgl_pemakaianbarang,
                        d.ruangan_id
                       FROM pemakaianbarang_t d) pemakaianbarang_t ON pemakaianbarangdetail_r.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id
                 JOIN ( SELECT d.barang_id,
                        d.barang_nama,
                        d.kelompokbarang_id,
                        d.barang_harganetto,
                        d.satuankecil_id
                       FROM barang_m d) barang_m ON pemakaianbarangdetail_r.barang_id = barang_m.barang_id
                 JOIN ( SELECT d.kelompokbarang_id,
                        d.servicecategory_id
                       FROM kelompokbarang_m d) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                 JOIN ( SELECT d.pemakaianbarangdetail_id,
                        d.nobatch
                       FROM stokbarang_t d
                      WHERE d.is_deleted = false
                      GROUP BY d.pemakaianbarangdetail_id, d.nobatch) stok ON pemakaianbarangdetail_r.pemakaianbarangdetail_id = stok.pemakaianbarangdetail_id
            UNION ALL
             SELECT \'barang\'::text AS jenis,
                \'stokopname_barang\'::text AS tipe_rekap,
                concat(\'STB\', stokopnamebarangdetail_r.stokopnamebarangdetail_id) AS sync_id_api,
                6 AS sync_type,
                stokopnamebarang_t.nostokopname AS origin,
                NULL::text AS admission_id,
                stokopnamebarang_t.tglstokopname AS transaction_datetime,
                to_char(stokopnamebarang_t.tglstokopname::timestamp with time zone, \'YYYY-MM-DD\'::text)::date AS transaction_date,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN \'AI\'::text
                        ELSE \'AR\'::text
                    END AS trans_type,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN stokopnamebarang_t.ruangan_id::text
                        ELSE \'scrap\'::text
                    END AS location_id,
                    CASE
                        WHEN stok.qty_in = 0::double precision THEN \'scrap\'::text
                        ELSE stokopnamebarang_t.ruangan_id::text
                    END AS scrap_location_id,
                stok.nobatch AS lot_id,
                concat(\'BRG\', kelompokbarang_m.kelompokbarang_id) AS categ_id,
                concat(\'CATEG\', kelompokbarang_m.servicecategory_id) AS service_categ_id,
                concat(\'OBT\', stokopnamebarangdetail_r.barang_id) AS product_id,
                concat(stokopnamebarang_t.nostokopname, \'-\', barang_m.barang_nama) AS name,
                barang_m.satuankecil_id AS product_uom_id,
                stokopnamebarangdetail_r.jmlselisihstok AS scrap_qty,
                barang_m.barang_harganetto AS cost,
                stokopnamebarangdetail_r.volume_sistem * barang_m.barang_harganetto AS cost_total,
                \'done\'::text AS state,
                stokopnamebarangdetail_r.id,
                stokopnamebarangdetail_r.is_sending,
                stokopnamebarangdetail_r.is_sent,
                stokopnamebarangdetail_r.sync_respon,
                    CASE
                        WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = false AND stokopnamebarangdetail_r.id_sync_sercon IS NOT NULL AND stokopnamebarangdetail_r.sync_respon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN stokopnamebarangdetail_r.is_sending = false AND stokopnamebarangdetail_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = false AND stokopnamebarangdetail_r.id_sync_sercon IS NOT NULL THEN \'DALAM PROSES\'::text
                        ELSE NULL::text
                    END AS status_proses,
                stokopnamebarangdetail_r.tgl_proses AS tanggal_transaksi
               FROM stokopnamebarangdetail_r
                 JOIN ( SELECT e.stokopnamebarang_id,
                        e.nostokopname,
                        e.tglstokopname,
                        e.ruangan_id
                       FROM stokopnamebarang_t e) stokopnamebarang_t ON stokopnamebarangdetail_r.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
                 JOIN ( SELECT e.barang_id,
                        e.barang_nama,
                        e.kelompokbarang_id,
                        e.satuankecil_id,
                        e.barang_harganetto
                       FROM barang_m e) barang_m ON stokopnamebarangdetail_r.barang_id = barang_m.barang_id
                 JOIN ( SELECT e.kelompokbarang_id,
                        e.servicecategory_id
                       FROM kelompokbarang_m e) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                 JOIN ( SELECT e.stokopnamebarangdetail_id,
                        e.nobatch,
                        sum(e.qtystok_in) AS qty_in
                       FROM stokbarang_t e
                      WHERE e.is_deleted = false
                      GROUP BY e.stokopnamebarangdetail_id, e.nobatch) stok ON stokopnamebarangdetail_r.stokopnamebarangdetail_id = stok.stokopnamebarangdetail_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093713_migrate_odoo_view_int_stockscrap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093713_migrate_odoo_view_int_stockscrap_v cannot be reverted.\n";

        return false;
    }
    */
}
