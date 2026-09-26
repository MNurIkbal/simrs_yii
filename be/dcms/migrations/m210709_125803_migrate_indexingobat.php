<?php

use yii\db\Migration;

/**
 * Class m210709_125803_migrate_indexingobat
 */
class m210709_125803_migrate_indexingobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('CREATE INDEX IF NOT exists "formstokopname_formstokopname_idx" ON "public"."formstokopname_t" USING btree (
  "formstokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formstokopname_ruangan_idx" ON "public"."formstokopname_t" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formstokopname_stokopnamedetail_idx" ON "public"."formstokopname_t" USING btree (
  "stokopnamedetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formulirstokopname_created_date_x" ON "public"."formulirstokopname_t" USING btree (
  "created_date" "pg_catalog"."timestamp_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formulirstokopname_formulirstokopname_idx" ON "public"."formulirstokopname_t" USING btree (
  "formulirstokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formulirstokopname_ruangan_idx" ON "public"."formulirstokopname_t" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "formulirstokopname_stokopname_idx" ON "public"."formulirstokopname_t" USING btree (
  "stokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargin_konfigmargin_id" ON "public"."konfigmargin_k" USING btree (
  "konfigmargin_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargin_tgl_berlaku" ON "public"."konfigmargin_k" USING btree (
  "tgl_berlaku" "pg_catalog"."date_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargindetail_harga_max" ON "public"."konfigmargindetail_k" USING btree (
  "harga_max" "pg_catalog"."float8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargindetail_harga_min" ON "public"."konfigmargindetail_k" USING btree (
  "harga_min" "pg_catalog"."float8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargindetail_konfigmargin_id" ON "public"."konfigmargindetail_k" USING btree (
  "konfigmargin_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigmargindetail_konfigmargindetail_id" ON "public"."konfigmargindetail_k" USING btree (
  "konfigmargindetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigrak_obatalkes_idx" ON "public"."konfigrak_m" USING btree (
  "obatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigrak_rakobat_idx" ON "public"."konfigrak_m" USING btree (
  "rakobat_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigrak_ruangan_idx" ON "public"."konfigrak_m" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "konfigrak_stokobatr_idx" ON "public"."konfigrak_m" USING btree (
  "stokobatr_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "obatalkes_harganetto" ON "public"."obatalkes_m" USING btree (
  "harganetto" "pg_catalog"."float8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "rakobat_parentrakobat_idx" ON "public"."rakobat_m" USING btree (
  "parentrakobat_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "rakobat_rakobat_idx" ON "public"."rakobat_m" USING btree (
  "rakobat_id" "pg_catalog"."int8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "rakobat_ruangan_idx" ON "public"."rakobat_m" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "satuankonversi_obatalkes_idx" ON "public"."satuankonversi_m" USING btree (
  "obatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "satuankonversi_satuanbesar_idx" ON "public"."satuankonversi_m" USING btree (
  "satuanbesar_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "satuankonversi_satuankecil_idx" ON "public"."satuankonversi_m" USING btree (
  "satuankecil_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "satuankonversi_satuankonversi_idx" ON "public"."satuankonversi_m" USING btree (
  "satuankonversi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_adjusmenobatkeluar_idx" ON "public"."stokobatalkes_t" USING btree (
  "adjusmenobatkeluar_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_adjusmenobatmasuk_idx" ON "public"."stokobatalkes_t" USING btree (
  "adjusmenobatmasuk_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_created_date_x" ON "public"."stokobatalkes_t" USING btree (
  "created_date" "pg_catalog"."timestamp_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_mutasiobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "mutasiobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_obatalkespasien_idx" ON "public"."stokobatalkes_t" USING btree (
  "obatalkespasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_pemakaianobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "pemakaianobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_pembatalanresep_idx" ON "public"."stokobatalkes_t" USING btree (
  "pembatalanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_pemusnahanobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "pemusnahanobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_penerimaanobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "penerimaanobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_penerimaansuppdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "penerimaansuppdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_qtystok_in_x" ON "public"."stokobatalkes_t" USING btree (
  "qtystok_in" "pg_catalog"."float8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_qtystok_out_x" ON "public"."stokobatalkes_t" USING btree (
  "qtystok_out" "pg_catalog"."float8_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_returpenerimaanobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "returpenerimaanobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_returresepdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "returresepdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_ruangan_idx" ON "public"."stokobatalkes_t" USING btree (
  "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_stokobatalkes_idx" ON "public"."stokobatalkes_t" USING btree (
  "stokobatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_stokobatalkesasal_idx" ON "public"."stokobatalkes_t" USING btree (
  "stokobatalkesasal_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_stokopnamedetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "stokopnamedetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_storexpiredobatdetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "storexpiredobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokobatalkes_terimamutasidetail_idx" ON "public"."stokobatalkes_t" USING btree (
  "terimamutasidetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokopname_formulirstokopname_idx" ON "public"."stokopname_t" USING btree (
  "formulirstokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokopname_stokopname_idx" ON "public"."stokopname_t" USING btree (
  "stokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokopnamedetail_formstokopname_idx" ON "public"."stokopnamedetail_t" USING btree (
  "formstokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokopnamedetail_stokopname_idx" ON "public"."stokopnamedetail_t" USING btree (
  "stokopname_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

$this->execute('CREATE INDEX IF NOT exists "stokopnamedetail_stokopnamedetail_idx" ON "public"."stokopnamedetail_t" USING btree (
  "stokopnamedetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210709_125803_migrate_indexingobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210709_125803_migrate_indexingobat cannot be reverted.\n";

        return false;
    }
    */
}
