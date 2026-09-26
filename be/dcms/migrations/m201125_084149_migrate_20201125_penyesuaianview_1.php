<?php

use yii\db\Migration;

/**
 * Class m201125_084149_migrate_20201125_penyesuaianview_1
 */
class m201125_084149_migrate_20201125_penyesuaianview_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_stockscrap_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'adj_keluar'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
    adjusmenobatkeluar_r.sync_respon
   FROM adjusmenobatkeluar_r
     JOIN adjusmenobat_t ON adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatkeluar_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatkeluar_id, stokobatalkes_t.nobatch) stok ON adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id
UNION ALL
 SELECT 'adj_masuk'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
    adjusmenobatmasuk_r.sync_respon
   FROM adjusmenobatmasuk_r
     JOIN adjusmenobat_t ON adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.adjusmenobatmasuk_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.adjusmenobatmasuk_id, stokobatalkes_t.nobatch) stok ON adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id
UNION ALL
 SELECT 'pemusnahan_obat'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
    pemusnahanobatdetail_r.sync_respon
   FROM pemusnahanobatdetail_r
     JOIN pemusnahanobat_t ON pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN obatalkes_m ON pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemusnahanobatdetail_id
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemusnahanobatdetail_id) stok ON pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id
UNION ALL
 SELECT 'pemakaian_obat'::text AS tipe_rekap,
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
    concat('CATEG', jenisobatalkes_m.servicecategory_id) AS categ_id,
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
    pemakaianobatdetail_r.sync_respon
   FROM pemakaianobatdetail_r
     JOIN pemakaianobat_t ON pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN obatalkes_m ON pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN ( SELECT stokobatalkes_t.pemakaianobatdetail_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.pemakaianobatdetail_id, stokobatalkes_t.nobatch) stok ON pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id;");

        $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if  exists "public"."int_obat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_obat_v\" AS  SELECT concat('OBT', obatalkes_r.obatalkes_id) AS sync_id_api,
    obatalkes_r.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_r.obatalkes_nama AS name,
    concat('OBT', obatalkes_r.jenisobatalkes_id) AS categ_id,
    obatalkes_r.satuanbesar_id AS uom_po_id,
    obatalkes_r.satuankecil_id AS uom_id,
    obatalkes_r.obatalkes_kode AS default_code,
    'product'::text AS type,
    false AS wipro_block,
    obatalkes_r.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    obatalkes_r.kemasan_besar AS conversion_rate,
    6 AS sync_type,
    obatalkes_r.keterangan_rekap,
    obatalkes_r.id,
    obatalkes_r.is_sent,
    obatalkes_r.is_sending
   FROM obatalkes_r;");
        
        $this->execute('ALTER TABLE "public"."int_obat_v" OWNER TO "postgres";');

    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_084149_migrate_20201125_penyesuaianview_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_084149_migrate_20201125_penyesuaianview_1 cannot be reverted.\n";

        return false;
    }
    */
}
