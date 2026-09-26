<?php

use yii\db\Migration;

/**
 * Class m211117_043802_migrate_configrak_v
 */
class m211117_043802_migrate_configrak_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.konfigrak_v;');
         
        $this->execute("
            CREATE VIEW \"public\".\"konfigrak_v\" AS  SELECT konfigrak_m.konfigrak_id,
    konfigrak_m.ruangan_id,
    ruangan_m.ruangan_nama,
    konfigrak_m.rakobat_id,
    rakobat_m.rakobat_nama,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    konfigrak_m.min_stok,
    konfigrak_m.max_stok,
    COALESCE(
        CASE
            WHEN (rakobat_m.parentrakobat_id IS NULL) THEN rakobat_m.rakobat_nama
            WHEN (rakobat_m.parentrakobat_id IS NOT NULL) THEN rak.rakobat_nama
            ELSE ''::character varying
        END, '-'::character varying) AS nama_rak,
    concat_ws(' / '::text, ('Rak :'::text || (
        CASE
            WHEN (rak.rakobat_id IS NULL) THEN rakobat_m.rakobat_nama
            ELSE rak.rakobat_nama
        END)::text), ('Locator :'::text || (
        CASE
            WHEN (rak.rakobat_id IS NOT NULL) THEN rakobat_m.rakobat_nama
            ELSE NULL::character varying
        END)::text)) AS nama_rak_laci,
    COALESCE(
        CASE
            WHEN (rak.rakobat_id IS NOT NULL) THEN rakobat_m.rakobat_nama
            ELSE NULL::character varying
        END, '-'::character varying) AS nama_laci
   FROM (((((konfigrak_m
     JOIN ruangan_m ON ((konfigrak_m.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN rakobat_m ON (((konfigrak_m.rakobat_id = rakobat_m.rakobat_id) AND (rakobat_m.is_deleted = false))))
     JOIN obatalkes_m ON ((konfigrak_m.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m sat_kecil ON ((obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id)))
     LEFT JOIN rakobat_m rak ON (((rakobat_m.parentrakobat_id = rak.rakobat_id) AND (rak.is_deleted = false))))
  WHERE (konfigrak_m.is_deleted = false);
        ;");

        $this->execute('
        ALTER TABLE "public"."konfigrak_v" OWNER TO "postgres";
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211117_043802_migrate_configrak_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211117_043802_migrate_configrak_v cannot be reverted.\n";

        return false;
    }
    */
}
