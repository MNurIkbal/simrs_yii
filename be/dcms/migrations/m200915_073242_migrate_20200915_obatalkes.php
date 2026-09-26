<?php

use yii\db\Migration;

/**
 * Class m200915_073242_migrate_20200915_obatalkes
 */
class m200915_073242_migrate_20200915_obatalkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."obatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"obatalkes_v\" AS  SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.ven AS ven_id,
    obatalkes_m.ven,
    obatalkes_m.groupinacbg_id,
    obatalkes_m.lead_time,
    obatalkes_m.avg_usage,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.nilai_ro,
    fgetpersenmargin(obatalkes_m.harganetto) AS margin,
    konfigfarmasi_k.persenppn AS ppn,
    konfigfarmasi_k.persen_diskon AS disc,
    obatalkes_m.hargaterakhir AS hn_last,
    0 AS hn_last_margin,
    0 AS hn_last_diskon,
    0 AS hn_last_margin_diskon,
    0 AS hn_last_ppn,
    0 AS hargajual_last,
    obatalkes_m.hargaminimum AS hn_min,
    0 AS hn_min_margin,
    0 AS hn_min_diskon,
    0 AS hn_min_margin_diskon,
    0 AS hn_min_ppn,
    0 AS hargajual_min,
    obatalkes_m.hargamaksimum AS hn_max,
    0 AS hn_max_margin,
    0 AS hn_max_diskon,
    0 AS hn_max_margin_diskon,
    0 AS hn_max_ppn,
    0 AS hargajual_max,
    obatalkes_m.hargaratarata AS hn_avg,
    0 AS hn_avg_margin,
    0 AS hn_avg_diskon,
    0 AS hn_avg_margin_diskon,
    0 AS hn_avg_ppn,
    0 AS hargajual_avg,
    0 AS hargaygdipakai,
    0 AS harganetto_ygdipakai,
    0 AS harga_sugesstion,
    0 AS selisih,
    0 AS hn_margin,
    0 AS hn_diskon,
    0 AS hn_ppn,
    obatalkes_m.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    jenisobatalkes_m.group_jenisobat,
    obatalkes_m.obatalkes_kode
   FROM (((obatalkes_m
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id)))
  WHERE ((obatalkes_m.is_active = true) AND (obatalkes_m.is_deleted = false));");
        
        $this->execute('ALTER TABLE "public"."obatalkes_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_073242_migrate_20200915_obatalkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_073242_migrate_20200915_obatalkes cannot be reverted.\n";

        return false;
    }
    */
}
