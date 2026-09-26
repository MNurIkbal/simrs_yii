<?php

use yii\db\Migration;

/**
 * Class m210211_031715_oddo_20210211_indexing
 */
class m210211_031715_oddo_20210211_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS "ix_p_keterangan" ON "public"."pasien_r" USING btree (
  "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_p_no_rekam_medik" ON "public"."pasien_r" USING btree (
  "no_rekam_medik" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_p_pasien_id" ON "public"."pasien_r" USING btree (
  "pasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_p_sent_sending" ON "public"."pasien_r" USING btree (
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_p_tgl_proses" ON "public"."pasien_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pno_penerimaanobat_id" ON "public"."penerimaanobat_r" USING btree (
  "penerimaanobat_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pno_sent_sending" ON "public"."penerimaanobat_r" USING btree (
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pno_status_rekap" ON "public"."penerimaanobat_r" USING btree (
  "status_rekap" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pno_tgl_proses" ON "public"."penerimaanobat_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pnod_penerimaanobat_id" ON "public"."penerimaanobatdetail_r" USING btree (
  "penerimaanobat_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pnod_penerimaanobatdetail_id" ON "public"."penerimaanobatdetail_r" USING btree (
  "penerimaanobatdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pnod_sent_sending" ON "public"."penerimaanobatdetail_r" USING btree (
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pnod_status_rekap" ON "public"."penerimaanobatdetail_r" USING btree (
  "status_rekap" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pnod_tgl_proses" ON "public"."penerimaanobatdetail_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_ps_penerimaansupp_id" ON "public"."penerimaansupp_r" USING btree (
  "penerimaansupp_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_ps_sent_sending" ON "public"."penerimaansupp_r" USING btree (
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');
        
        $this->execute('CREATE INDEX IF NOT EXISTS "ix_ps_status_rekap" ON "public"."penerimaansupp_r" USING btree (
  "status_rekap" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_ps_tgl_proses" ON "public"."penerimaansupp_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_psd_penerimaansupp_id" ON "public"."penerimaansuppdetail_r" USING btree (
  "penerimaansupp_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_psd_penerimaansuppdetail_id" ON "public"."penerimaansuppdetail_r" USING btree (
  "penerimaansuppdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_psd_sent_sending" ON "public"."penerimaansuppdetail_r" USING btree (
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_psd_status_rekap" ON "public"."penerimaansuppdetail_r" USING btree (
  "status_rekap" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_psd_tgl_proses" ON "public"."penerimaansuppdetail_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pr_jenispenjualan" ON "public"."penjualanresep_r" USING btree (
  "jenispenjualan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pr_keterangan" ON "public"."penjualanresep_r" USING btree (
  "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pr_penjualanresep" ON "public"."penjualanresep_r" USING btree (
  "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pr_sent_sending" ON "public"."penjualanresep_r" USING btree (
  "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST,
  "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pr_tgl_proses" ON "public"."penjualanresep_r" USING btree (
  "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210211_031715_oddo_20210211_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_031715_oddo_20210211_indexing cannot be reverted.\n";

        return false;
    }
    */
}
