<?php

use yii\db\Migration;

/**
 * Class m210621_144858_migrate_indexing
 */
class m210621_144858_migrate_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP INDEX if exists "public"."hasilpemeriksaanrad_tgl_hasilrad_idx"');

        $this->execute('CREATE INDEX if not exists "hasilpemeriksaanrad_tgl_hasilrad_idx" ON "public"."hasilpemeriksaanrad_t" USING btree (
  tgl_hasilrad "pg_catalog"."timestamp_ops" ASC NULLS LAST
)');

        $this->execute('CREATE INDEX if not exists "int_billing_r_id" ON "public"."int_billing_r" USING btree (
  "id" "pg_catalog"."int8_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_billing_r_pasienadmisi_id" ON "public"."int_billing_r" USING btree (
  "pasienadmisi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_billing_r_pembayaran_id" ON "public"."int_billing_r" USING btree (
  "pembayaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_billing_r_pendaftaran_id" ON "public"."int_billing_r" USING btree (
  "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_freezebill_id" ON "public"."int_freezebill_r" USING btree (
  "id" "pg_catalog"."int8_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_freezebill_invoice_id" ON "public"."int_freezebill_r" USING btree (
  "invoice_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_freezebill_payer_id" ON "public"."int_freezebill_r" USING btree (
  "payer_id" "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "int_freezebill_pendaftaran_id" ON "public"."int_freezebill_r" USING btree (
  "pendaftaran_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kabupaten_m_kabupaten_idx" ON "public"."kabupaten_m" USING btree (
  kabupaten_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kabupaten_m_propinsi_idx" ON "public"."kabupaten_m" USING btree (
  propinsi_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kecamatan_m_kabupaten_id_ix" ON "public"."kecamatan_m" USING btree (
  kabupaten_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kecamatan_m_kecamatan_id_ix" ON "public"."kecamatan_m" USING btree (
  kecamatan_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kelurahan_m_kecamatan_idx" ON "public"."kelurahan_m" USING btree (
  kecamatan_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "kelurahan_m_kelurahan_idx" ON "public"."kelurahan_m" USING btree (
  kelurahan_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "obatalkes_m_obatalkes_idx" ON "public"."obatalkes_m" USING btree (
  obatalkes_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "pasien_m_daerah" ON "public"."pasien_m" USING btree (
  propinsi_id "pg_catalog"."int4_ops" ASC NULLS LAST,
  kabupaten_id "pg_catalog"."int4_ops" ASC NULLS LAST,
  kecamatan_id "pg_catalog"."int4_ops" ASC NULLS LAST,
  kelurahan_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "pasienadmisi_penjamin_idx" ON "public"."pasienadmisi_t" USING btree (
  penjamin_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "propinsi_m_propinsi_idx" ON "public"."propinsi_m" USING btree (
  propinsi_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');
        $this->execute('CREATE INDEX if not exists "tindakanpelayanan_pasienmasukpenunjang_id_ix" ON "public"."tindakanpelayanan_t" USING btree (
  pasienmasukpenunjang_id "pg_catalog"."int4_ops" ASC NULLS LAST
)');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210621_144858_migrate_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210621_144858_migrate_indexing cannot be reverted.\n";

        return false;
    }
    */
}
