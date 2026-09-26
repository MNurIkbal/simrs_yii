<?php

use yii\db\Migration;

/**
 * Class m210817_103836_migrate_oddo_so
 */
class m210817_103836_migrate_oddo_so extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."stokopnamedetail_r" (
  "id" serial8,
  "stokopnamedetail_id" int4,
  "formstokopname_id" int4,
  "satuankecil_id" int4,
  "sumberdana_id" int4,
  "stokopname_id" int4,
  "obatalkes_id" int4,
  "volume_fisik" float8,
  "volume_sistem" float8,
  "hargasatuan" float8,
  "jumlahharga" float8,
  "harganetto" float8,
  "jumlahnetto" float8,
  "tglkadaluarsa" date,
  "kondisibarang" varchar(10) COLLATE "pg_catalog"."default" DEFAULT 9999,
  "tglperiksafisik" timestamp(6),
  "jmlselisihstok" float8,
  "stokobatalkes_id" int4,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool DEFAULT false,
  "is_active" bool DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "revisi_stok" float8,
  "weighted_avg" float8,
  "stok_akhir" float8,
  "selisih_akhir" float8,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "stokopnamedetail_r_pkey" PRIMARY KEY ("id")
)
;');
        $this->execute('ALTER TABLE "public"."stokopnamedetail_r" OWNER TO "postgres";');

        $this->execute('CREATE TABLE if not exists "public"."stokopnamebarangdetail_r" (
  "id" serial8,
  "stokopnamebarangdetail_id" int4,
  "formsobarangdetail_id" int4,
  "satuankecil_id" int4,
  "stokopnamebarang_id" int4,
  "barang_id" int4,
  "volume_fisik" float8,
  "volume_sistem" float8,
  "hargasatuan" float8,
  "jumlahharga" float8,
  "harganetto" float8,
  "jumlahnetto" float8,
  "tglkadaluarsa" date,
  "kondisibarang" varchar COLLATE "pg_catalog"."default" DEFAULT 9999,
  "tglperiksafisik" timestamp(6),
  "jmlselisihstok" float8,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool DEFAULT false,
  "is_active" bool DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "stokopnamebarangdetail_r_pkey" PRIMARY KEY ("id")
)
;');
        $this->execute('ALTER TABLE "public"."stokopnamebarangdetail_r" OWNER TO "postgres";');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"stokopnamedetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history stokopnamedetail_r
        INSERT INTO stokopnamedetail_r (        
                stokopnamedetail_id,
                formstokopname_id,
                satuankecil_id,
                sumberdana_id,
                stokopname_id,
                obatalkes_id,
                volume_fisik,
                volume_sistem,
                hargasatuan,
                jumlahharga,
                harganetto,
                jumlahnetto,
                tglkadaluarsa,
                kondisibarang,
                tglperiksafisik,
                jmlselisihstok,
                stokobatalkes_id,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                revisi_stok,
                weighted_avg,
                stok_akhir,
                selisih_akhir,
        keterangan_rekap
        )VALUES(
        NEW.stokopnamedetail_id,
                NEW.formstokopname_id,
                NEW.satuankecil_id,
                NEW.sumberdana_id,
                NEW.stokopname_id,
                NEW.obatalkes_id,
                NEW.volume_fisik,
                NEW.volume_sistem,
                NEW.hargasatuan,
                NEW.jumlahharga,
                NEW.harganetto,
                NEW.jumlahnetto,
                NEW.tglkadaluarsa,
                NEW.kondisibarang,
                NEW.tglperiksafisik,
                NEW.jmlselisihstok,
                NEW.stokobatalkes_id,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.revisi_stok,
                NEW.weighted_avg,
                NEW.stok_akhir,
                NEW.selisih_akhir,
        'ACCRUAL'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "stokopnamedetail_r_insert" AFTER INSERT ON "public"."stokopnamedetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."stokopnamedetail_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "stokopnamedetail_r_insert" ON "public"."stokopnamedetail_t" IS \'oddo stockscrap\';');
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"stokopnamebarangdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history stokopnamebarangdetail_r
        INSERT INTO stokopnamebarangdetail_r (        
                    stokopnamebarangdetail_id,
                    formsobarangdetail_id,
                    satuankecil_id,
                    stokopnamebarang_id,
                    barang_id,
                    volume_fisik,
                    volume_sistem,
                    hargasatuan,
                    jumlahharga,
                    harganetto,
                    jumlahnetto,
                    tglkadaluarsa,
                    kondisibarang,
                    tglperiksafisik,
                    jmlselisihstok,
                    additional_data,
                    created_date,
                    created_by,
                    modified_count,
                    last_modified_date,
                    last_modified_by,
                    is_deleted,
                    is_active,
                    deleted_date,
                    deleted_by,
                    keterangan_rekap
        )VALUES(
                    NEW.stokopnamebarangdetail_id,
                    NEW.formsobarangdetail_id,
                    NEW.satuankecil_id,
                    NEW.stokopnamebarang_id,
                    NEW.barang_id,
                    NEW.volume_fisik,
                    NEW.volume_sistem,
                    NEW.hargasatuan,
                    NEW.jumlahharga,
                    NEW.harganetto,
                    NEW.jumlahnetto,
                    NEW.tglkadaluarsa,
                    NEW.kondisibarang,
                    NEW.tglperiksafisik,
                    NEW.jmlselisihstok,
                    NEW.additional_data,
                    NEW.created_date,
                    NEW.created_by,
                    NEW.modified_count,
                    NEW.last_modified_date,
                    NEW.last_modified_by,
                    NEW.is_deleted,
                    NEW.is_active,
                    NEW.deleted_date,
                    NEW.deleted_by,
                    'ACCRUAL'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "stokopnamebarangdetail_r_insert" AFTER INSERT ON "public"."stokopnamebarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."stokopnamebarangdetail_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "stokopnamebarangdetail_r_insert" ON "public"."stokopnamebarangdetail_t" IS \'oddo stockscrap\';');
        
        $this->execute('DROP VIEW if exists "public"."int_stockscrap_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'obat'::text AS jenis,
    'adj_keluar'::text AS tipe_rekap,
    concat('AJK', adjusmenobatkeluar_r.adjusmenobatkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AI'::text AS trans_type,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', adjusmenobatkeluar_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenobatkeluar_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatkeluar_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatkeluar_r.id,
    adjusmenobatkeluar_r.is_sending,
    adjusmenobatkeluar_r.is_sent,
    adjusmenobatkeluar_r.sync_respon,
        CASE
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL AND adjusmenobatkeluar_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = false AND adjusmenobatkeluar_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatkeluar_r.is_sending = true AND adjusmenobatkeluar_r.is_sent = false AND adjusmenobatkeluar_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatkeluar_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatkeluar_r
     JOIN ( SELECT a.adjusmenobat_id,
            a.no_adjusmen,
            a.tgl_adjusmen,
            a.ruangan_adjusmen_id
           FROM adjusmenobat_t a) adjusmenobat_t ON adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN ( SELECT b.obatalkes_id,
            b.jenisobatalkes_id,
            b.obatalkes_nama,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.jenisobatalkes_id,
            c.servicecategory_id
           FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT d.adjusmenobatkeluar_id,
            d.nobatch
           FROM stokobatalkes_t d
          WHERE d.is_deleted = false
          GROUP BY d.adjusmenobatkeluar_id, d.nobatch) stok ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'adj_masuk'::text AS tipe_rekap,
    concat('AJM', adjusmenobatmasuk_r.adjusmenobatmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    adjusmenobat_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', adjusmenobatmasuk_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenobatmasuk_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    adjusmenobatmasuk_r.qty_konversi::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    adjusmenobatmasuk_r.id,
    adjusmenobatmasuk_r.is_sending,
    adjusmenobatmasuk_r.is_sent,
    adjusmenobatmasuk_r.sync_respon,
        CASE
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL AND adjusmenobatmasuk_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = false AND adjusmenobatmasuk_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenobatmasuk_r.is_sending = true AND adjusmenobatmasuk_r.is_sent = false AND adjusmenobatmasuk_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenobatmasuk_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenobatmasuk_r
     JOIN ( SELECT a.adjusmenobat_id,
            a.no_adjusmen,
            a.tgl_adjusmen,
            a.ruangan_adjusmen_id
           FROM adjusmenobat_t a) adjusmenobat_t ON adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.jenisobatalkes_id,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.jenisobatalkes_id,
            c.servicecategory_id
           FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT d.adjusmenobatmasuk_id,
            d.nobatch
           FROM stokobatalkes_t d
          WHERE d.is_deleted = false
          GROUP BY d.adjusmenobatmasuk_id, d.nobatch) stok ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'pemusnahan_obat'::text AS tipe_rekap,
    concat('PMO', pemusnahanobatdetail_r.pemusnahanobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanobat_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanobat_t.tglpemusnahan AS transaction_datetime,
    to_char(pemusnahanobat_t.tglpemusnahan, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'BR'::text AS trans_type,
    pemusnahanobat_t.ruangan_id::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanobatdetail_r.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', pemusnahanobatdetail_r.obatalkes_id) AS product_id,
    concat(pemusnahanobat_t.nopemusnahan, '-', obatalkes_m.obatalkes_nama) AS name,
    pemusnahanobatdetail_r.satuan_id AS product_uom_id,
    pemusnahanobatdetail_r.jumlah AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemusnahanobatdetail_r.jumlah * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemusnahanobatdetail_r.id,
    pemusnahanobatdetail_r.is_sending,
    pemusnahanobatdetail_r.is_sent,
    pemusnahanobatdetail_r.sync_respon,
        CASE
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL AND pemusnahanobatdetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = false AND pemusnahanobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemusnahanobatdetail_r.is_sending = true AND pemusnahanobatdetail_r.is_sent = false AND pemusnahanobatdetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    pemusnahanobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemusnahanobatdetail_r
     JOIN ( SELECT a.pemusnahanobat_id,
            a.nopemusnahan,
            a.tglpemusnahan,
            a.ruangan_id
           FROM pemusnahanobat_t a) pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.jenisobatalkes_id,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.jenisobatalkes_id,
            c.servicecategory_id
           FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT d.pemusnahanobatdetail_id
           FROM stokobatalkes_t d
          WHERE d.is_deleted = false
          GROUP BY d.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'pemakaian_obat'::text AS tipe_rekap,
    concat('PKO', pemakaianobatdetail_r.pemakaianobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianobat_t.nopemakaian_obat AS origin,
    NULL::text AS admission_id,
    pemakaianobat_t.tglpemakaianobat AS transaction_datetime,
    to_char(pemakaianobat_t.tglpemakaianobat::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'SC'::text AS trans_type,
    pemakaianobat_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', pemakaianobatdetail_r.obatalkes_id) AS product_id,
    concat(pemakaianobat_t.nopemakaian_obat, '-', obatalkes_m.obatalkes_nama) AS name,
    pemakaianobatdetail_r.satuankecil_id AS product_uom_id,
    pemakaianobatdetail_r.qty_satuanpakai AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    pemakaianobatdetail_r.qty_satuanpakai::double precision * obatalkes_m.harganetto AS cost_total,
    'done'::text AS state,
    pemakaianobatdetail_r.id,
    pemakaianobatdetail_r.is_sending,
    pemakaianobatdetail_r.is_sent,
    pemakaianobatdetail_r.sync_respon,
        CASE
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL AND pemakaianobatdetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianobatdetail_r.is_sending = false AND pemakaianobatdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianobatdetail_r.is_sending = true AND pemakaianobatdetail_r.is_sent = false AND pemakaianobatdetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    pemakaianobatdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemakaianobatdetail_r
     JOIN ( SELECT a.pemakaianobat_id,
            a.nopemakaian_obat,
            a.tglpemakaianobat,
            a.ruangan_id
           FROM pemakaianobat_t a) pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.jenisobatalkes_id,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.jenisobatalkes_id,
            c.servicecategory_id
           FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT d.pemakaianobatdetail_id,
            d.nobatch
           FROM stokobatalkes_t d
          WHERE d.is_deleted = false
          GROUP BY d.pemakaianobatdetail_id, d.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    'stokopname_obat'::text AS tipe_rekap,
    concat('STO', stokopnamedetail_r.stokopnamedetail_id) AS sync_id_api,
    6 AS sync_type,
    stokopname_t.nostokopname AS origin,
    NULL::text AS admission_id,
    stokopname_t.tglstokopname AS transaction_datetime,
    to_char(stokopname_t.tglstokopname::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
        CASE
            WHEN stok.qty_in = 0::double precision THEN 'AI'::text
            ELSE 'AR'::text
        END AS trans_type,
    stokopname_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS categ_id,
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
    concat('OBT', stokopnamedetail_r.obatalkes_id) AS product_id,
    concat(stokopname_t.nostokopname, '-', obatalkes_m.obatalkes_nama) AS name,
    stokopnamedetail_r.satuankecil_id AS product_uom_id,
        CASE
            WHEN stokopnamedetail_t.revisi_stok IS NULL OR stokopnamedetail_t.revisi_stok = 0::double precision THEN abs(stokopnamedetail_t.selisih_akhir)
            ELSE abs(stokopnamedetail_t.stok)
        END AS scrap_qty,
    obatalkes_m.harganetto AS cost,
        CASE
            WHEN stokopnamedetail_t.revisi_stok IS NULL OR stokopnamedetail_t.revisi_stok = 0::double precision THEN abs(stokopnamedetail_t.selisih_akhir) * obatalkes_m.harganetto
            ELSE abs(stokopnamedetail_t.stok) * obatalkes_m.harganetto
        END AS cost_total,
    'done'::text AS state,
    stokopnamedetail_r.id,
    stokopnamedetail_r.is_sending,
    stokopnamedetail_r.is_sent,
    stokopnamedetail_r.sync_respon,
        CASE
            WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = false AND stokopnamedetail_r.id_sync_sercon IS NOT NULL AND stokopnamedetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN stokopnamedetail_r.is_sending = false AND stokopnamedetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN stokopnamedetail_r.is_sending = true AND stokopnamedetail_r.is_sent = false AND stokopnamedetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    stokopnamedetail_r.tgl_proses AS tanggal_transaksi
   FROM stokopnamedetail_r
     JOIN ( SELECT a.stokopnamedetail_id,
            a.revisi_stok,
            a.selisih_akhir,
            a.revisi_stok - a.volume_sistem AS stok
           FROM stokopnamedetail_t a) stokopnamedetail_t ON stokopnamedetail_r.stokopnamedetail_id = stokopnamedetail_t.stokopnamedetail_id
     JOIN ( SELECT a.stokopname_id,
            a.nostokopname,
            a.tglstokopname,
            a.ruangan_id
           FROM stokopname_t a) stokopname_t ON stokopnamedetail_r.stokopname_id = stokopname_t.stokopname_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_kode,
            b.obatalkes_nama,
            b.jenisobatalkes_id,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON stokopnamedetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.jenisobatalkes_id,
            c.jenisobatalkes_nama,
            c.servicecategory_id
           FROM jenisobatalkes_m c) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT d.stokopnamedetail_id,
            d.nobatch,
            sum(d.qtystok_in) AS qty_in
           FROM stokobatalkes_t d
          WHERE d.is_deleted = false
          GROUP BY d.stokopnamedetail_id, d.nobatch) stok ON stokopnamedetail_r.stokopnamedetail_id = stok.stokopnamedetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'adj_keluar_barang'::text AS tipe_rekap,
    concat('AJK', adjusmenbarangkeluar_r.adjusmenbarangkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenbarang_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenbarang_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AI'::text AS trans_type,
    adjusmenbarang_t.ruangan_adjusmen_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', adjusmenbarangkeluar_r.barang_id) AS product_id,
    concat(adjusmenbarang_t.no_adjusmen, '-', barang_m.barang_nama) AS name,
    adjusmenbarangkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenbarangkeluar_r.qty_konversi AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    adjusmenbarangkeluar_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    adjusmenbarangkeluar_r.id,
    adjusmenbarangkeluar_r.is_sending,
    adjusmenbarangkeluar_r.is_sent,
    adjusmenbarangkeluar_r.sync_respon,
        CASE
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL AND adjusmenbarangkeluar_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenbarangkeluar_r.is_sending = false AND adjusmenbarangkeluar_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenbarangkeluar_r.is_sending = true AND adjusmenbarangkeluar_r.is_sent = false AND adjusmenbarangkeluar_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenbarangkeluar_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenbarangkeluar_r
     JOIN ( SELECT a.adjusmenbarang_id,
            a.no_adjusmen,
            a.tgl_adjusmen,
            a.ruangan_adjusmen_id
           FROM adjusmenbarang_t a) adjusmenbarang_t ON adjusmenbarangkeluar_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
     JOIN ( SELECT b.barang_id,
            b.barang_nama,
            b.kelompokbarang_id,
            b.barang_harganetto
           FROM barang_m b) barang_m ON adjusmenbarangkeluar_r.barang_id = barang_m.barang_id
     JOIN ( SELECT c.kelompokbarang_id,
            c.servicecategory_id
           FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT d.adjusmenbarangkeluar_id,
            d.nobatch
           FROM stokbarang_t d
          WHERE d.is_deleted = false
          GROUP BY d.adjusmenbarangkeluar_id, d.nobatch) stok ON adjusmenbarangkeluar_r.adjusmenbarangkeluar_id = stok.adjusmenbarangkeluar_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'adj_masuk_barang'::text AS tipe_rekap,
    concat('AJM', adjusmenbarangmasuk_r.adjusmenbarangmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenbarang_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenbarang_t.tgl_adjusmen AS transaction_datetime,
    to_char(adjusmenbarang_t.tgl_adjusmen, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    adjusmenbarang_t.ruangan_adjusmen_id::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', adjusmenbarangmasuk_r.barang_id) AS product_id,
    concat(adjusmenbarang_t.no_adjusmen, '-', barang_m.barang_nama) AS name,
    adjusmenbarangmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenbarangmasuk_r.qty_konversi AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    adjusmenbarangmasuk_r.qty_konversi::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    adjusmenbarangmasuk_r.id,
    adjusmenbarangmasuk_r.is_sending,
    adjusmenbarangmasuk_r.is_sent,
    adjusmenbarangmasuk_r.sync_respon,
        CASE
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL AND adjusmenbarangmasuk_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = true THEN 'SUKSES'::text
            WHEN adjusmenbarangmasuk_r.is_sending = false AND adjusmenbarangmasuk_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN adjusmenbarangmasuk_r.is_sending = true AND adjusmenbarangmasuk_r.is_sent = false AND adjusmenbarangmasuk_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    adjusmenbarangmasuk_r.tgl_proses AS tanggal_transaksi
   FROM adjusmenbarangmasuk_r
     JOIN ( SELECT a.adjusmenbarang_id,
            a.no_adjusmen,
            a.tgl_adjusmen,
            a.ruangan_adjusmen_id
           FROM adjusmenbarang_t a) adjusmenbarang_t ON adjusmenbarangmasuk_r.adjusmenbarang_id = adjusmenbarang_t.adjusmenbarang_id
     JOIN ( SELECT b.barang_id,
            b.barang_nama,
            b.kelompokbarang_id,
            b.barang_harganetto
           FROM barang_m b) barang_m ON adjusmenbarangmasuk_r.barang_id = barang_m.barang_id
     JOIN ( SELECT c.kelompokbarang_id,
            c.servicecategory_id
           FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT d.adjusmenbarangmasuk_id,
            d.nobatch
           FROM stokbarang_t d
          WHERE d.is_deleted = false
          GROUP BY d.adjusmenbarangmasuk_id, d.nobatch) stok ON adjusmenbarangmasuk_r.adjusmenbarangmasuk_id = stok.adjusmenbarangmasuk_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'pemusnahan_barang'::text AS tipe_rekap,
    concat('PMO', pemusnahanbarangdetail_r.pemusnahanbarangdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanbarang_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanbarang_t.tglpemusnahan AS transaction_datetime,
    to_char(pemusnahanbarang_t.tglpemusnahan, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'BR'::text AS trans_type,
    pemusnahanbarang_t.ruangan_id::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanbarangdetail_r.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', pemusnahanbarangdetail_r.barang_id) AS product_id,
    concat(pemusnahanbarang_t.nopemusnahan, '-', barang_m.barang_nama) AS name,
    barang_m.satuankecil_id AS product_uom_id,
    pemusnahanbarangdetail_r.jumlah AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    pemusnahanbarangdetail_r.jumlah * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    pemusnahanbarangdetail_r.id,
    pemusnahanbarangdetail_r.is_sending,
    pemusnahanbarangdetail_r.is_sent,
    pemusnahanbarangdetail_r.sync_respon,
        CASE
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL AND pemusnahanbarangdetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemusnahanbarangdetail_r.is_sending = false AND pemusnahanbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemusnahanbarangdetail_r.is_sending = true AND pemusnahanbarangdetail_r.is_sent = false AND pemusnahanbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    pemusnahanbarangdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemusnahanbarangdetail_r
     JOIN ( SELECT a.pemusnahanbarang_id,
            a.nopemusnahan,
            a.tglpemusnahan,
            a.ruangan_id
           FROM pemusnahanbarang_t a) pemusnahanbarang_t ON pemusnahanbarangdetail_r.pemusnahanbarang_id = pemusnahanbarang_t.pemusnahanbarang_id
     JOIN ( SELECT b.barang_id,
            b.barang_nama,
            b.kelompokbarang_id,
            b.barang_harganetto,
            b.satuankecil_id
           FROM barang_m b) barang_m ON pemusnahanbarangdetail_r.barang_id = barang_m.barang_id
     JOIN ( SELECT c.kelompokbarang_id,
            c.servicecategory_id
           FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT d.pemusnahanbarangdetail_id
           FROM stokbarang_t d
          WHERE d.is_deleted = false
          GROUP BY d.pemusnahanbarangdetail_id) stok ON pemusnahanbarangdetail_r.pemusnahanbarangdetail_id = stok.pemusnahanbarangdetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'pemakaian_barang'::text AS tipe_rekap,
    concat('PKO', pemakaianbarangdetail_r.pemakaianbarangdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianbarang_t.no_pemakaianbarang AS origin,
    NULL::text AS admission_id,
    pemakaianbarang_t.tgl_pemakaianbarang AS transaction_datetime,
    to_char(pemakaianbarang_t.tgl_pemakaianbarang::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
    'SC'::text AS trans_type,
    pemakaianbarang_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('BRG', pemakaianbarangdetail_r.barang_id) AS product_id,
    concat(pemakaianbarang_t.no_pemakaianbarang, '-', barang_m.barang_nama) AS name,
    pemakaianbarangdetail_r.satuankecil_id AS product_uom_id,
    pemakaianbarangdetail_r.jumlah_pakai AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    pemakaianbarangdetail_r.jumlah_pakai::double precision * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    pemakaianbarangdetail_r.id,
    pemakaianbarangdetail_r.is_sending,
    pemakaianbarangdetail_r.is_sent,
    pemakaianbarangdetail_r.sync_respon,
        CASE
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL AND pemakaianbarangdetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN pemakaianbarangdetail_r.is_sending = false AND pemakaianbarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN pemakaianbarangdetail_r.is_sending = true AND pemakaianbarangdetail_r.is_sent = false AND pemakaianbarangdetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    pemakaianbarangdetail_r.tgl_proses AS tanggal_transaksi
   FROM pemakaianbarangdetail_r
     JOIN pemakaianbarang_t ON pemakaianbarangdetail_r.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id
     JOIN ( SELECT b.barang_id,
            b.barang_nama,
            b.kelompokbarang_id,
            b.barang_harganetto,
            b.satuankecil_id
           FROM barang_m b) barang_m ON pemakaianbarangdetail_r.barang_id = barang_m.barang_id
     JOIN ( SELECT c.kelompokbarang_id,
            c.servicecategory_id
           FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT d.pemakaianbarangdetail_id,
            d.nobatch
           FROM stokbarang_t d
          WHERE d.is_deleted = false
          GROUP BY d.pemakaianbarangdetail_id, d.nobatch) stok ON pemakaianbarangdetail_r.pemakaianbarangdetail_id = stok.pemakaianbarangdetail_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    'stokopname_barang'::text AS tipe_rekap,
    concat('STB', stokopnamebarangdetail_r.stokopnamebarangdetail_id) AS sync_id_api,
    6 AS sync_type,
    stokopnamebarang_t.nostokopname AS origin,
    NULL::text AS admission_id,
    stokopnamebarang_t.tglstokopname AS transaction_datetime,
    to_char(stokopnamebarang_t.tglstokopname::timestamp with time zone, 'YYYY-MM-DD'::text)::date AS transaction_date,
        CASE
            WHEN stok.qty_in = 0::double precision THEN 'AI'::text
            ELSE 'AR'::text
        END AS trans_type,
    stokopnamebarang_t.ruangan_id::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    concat('BRG', kelompokbarang_m.kelompokbarang_id) AS categ_id,
    concat('CATEG', kelompokbarang_m.servicecategory_id) AS service_categ_id,
    concat('OBT', stokopnamebarangdetail_r.barang_id) AS product_id,
    concat(stokopnamebarang_t.nostokopname, '-', barang_m.barang_nama) AS name,
    stokopnamebarangdetail_r.satuankecil_id AS product_uom_id,
    stokopnamebarangdetail_r.jmlselisihstok AS scrap_qty,
    barang_m.barang_harganetto AS cost,
    stokopnamebarangdetail_r.volume_sistem * barang_m.barang_harganetto AS cost_total,
    'done'::text AS state,
    stokopnamebarangdetail_r.id,
    stokopnamebarangdetail_r.is_sending,
    stokopnamebarangdetail_r.is_sent,
    stokopnamebarangdetail_r.sync_respon,
        CASE
            WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = false AND stokopnamebarangdetail_r.id_sync_sercon IS NOT NULL AND stokopnamebarangdetail_r.sync_respon IS NOT NULL THEN 'GAGAL'::text
            WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = true THEN 'SUKSES'::text
            WHEN stokopnamebarangdetail_r.is_sending = false AND stokopnamebarangdetail_r.is_sent = false THEN 'MENUNGGU PROSES'::text
            WHEN stokopnamebarangdetail_r.is_sending = true AND stokopnamebarangdetail_r.is_sent = false AND stokopnamebarangdetail_r.id_sync_sercon IS NOT NULL THEN 'DALAM PROSES'::text
            ELSE NULL::text
        END AS status_proses,
    stokopnamebarangdetail_r.tgl_proses AS tanggal_transaksi
   FROM stokopnamebarangdetail_r
     JOIN ( SELECT a.stokopnamebarang_id,
            a.nostokopname,
            a.tglstokopname,
            a.ruangan_id
           FROM stokopnamebarang_t a) stokopnamebarang_t ON stokopnamebarangdetail_r.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
     JOIN ( SELECT b.barang_id,
            b.barang_nama,
            b.kelompokbarang_id,
            b.barang_harganetto
           FROM barang_m b) barang_m ON stokopnamebarangdetail_r.barang_id = barang_m.barang_id
     JOIN ( SELECT c.kelompokbarang_id,
            c.servicecategory_id
           FROM kelompokbarang_m c) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     JOIN ( SELECT d.stokopnamebarangdetail_id,
            d.nobatch,
            sum(d.qtystok_in) AS qty_in
           FROM stokbarang_t d
          WHERE d.is_deleted = false
          GROUP BY d.stokopnamebarangdetail_id, d.nobatch) stok ON stokopnamebarangdetail_r.stokopnamebarangdetail_id = stok.stokopnamebarangdetail_id;");

        $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210817_103836_migrate_oddo_so cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210817_103836_migrate_oddo_so cannot be reverted.\n";

        return false;
    }
    */
}
