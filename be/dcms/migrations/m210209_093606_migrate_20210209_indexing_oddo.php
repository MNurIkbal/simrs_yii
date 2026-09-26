<?php

use yii\db\Migration;

/**
 * Class m210209_093606_migrate_20210209_indexing_oddo
 */
class m210209_093606_migrate_20210209_indexing_oddo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_is_sending" ON "public"."obatalkespasien_r" USING btree (
                        "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_is_sent" ON "public"."obatalkespasien_r" USING btree (
                        "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_keterangan" ON "public"."obatalkespasien_r" USING btree (
                        "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_obatalkes_id" ON "public"."obatalkespasien_r" USING btree (
                        "obatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_obatalkespasien_id" ON "public"."obatalkespasien_r" USING btree (
                        "obatalkespasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_obatsudahbayar_id" ON "public"."obatalkespasien_r" USING btree (
                        "obatsudahbayar_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_pendaftaran_id" ON "public"."obatalkespasien_r" USING btree (
                        "obatalkespasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_penjualanresep" ON "public"."obatalkespasien_r" USING btree (
                        "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_op_tgl_proses" ON "public"."obatalkespasien_r" USING btree (
                        "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_is_sending" ON "public"."pembayaran_r" USING btree (
                        "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_is_sent" ON "public"."pembayaran_r" USING btree (
                        "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_keterangan" ON "public"."pembayaran_r" USING btree (
                        "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_pembayaran_id" ON "public"."pembayaran_r" USING btree (
                        "pembayaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_pendaftaran_id" ON "public"."pembayaran_r" USING btree (
                        "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pem_tgl_proses" ON "public"."pembayaran_r" USING btree (
                        "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_is_sending" ON "public"."pendaftaran_r" USING btree (
                        "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_is_sent" ON "public"."pendaftaran_r" USING btree (
                        "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_keterangan" ON "public"."pendaftaran_r" USING btree (
                        "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pasien_id" ON "public"."pendaftaran_r" USING btree (
                        "pasien_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_pendaftaran_id" ON "public"."pendaftaran_r" USING btree (
                        "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_saleorder_line" ON "public"."pendaftaran_r" USING btree (
                      "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
                      "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST,
                      "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tgl_pendaftaran" ON "public"."pendaftaran_r" USING btree (
                        "tgl_pendaftaran" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tgl_proses" ON "public"."pendaftaran_r" USING btree (
                        "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "obatalkes_r_obatalkes_id" ON "public"."stokobatalkes_r" USING btree (
                        "obatalkes_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "obatalkes_r_ruangan_id" ON "public"."stokobatalkes_r" USING btree (
                        "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_daftartindakan_id" ON "public"."tindakanpelayanan_r" USING btree (
                        "daftartindakan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_is_sending" ON "public"."tindakanpelayanan_r" USING btree (
                        "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_is_sent" ON "public"."tindakanpelayanan_r" USING btree (
                        "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_keterangan" ON "public"."tindakanpelayanan_r" USING btree (
                        "keterangan" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_pasienmasukpenunjang" ON "public"."tindakanpelayanan_r" USING btree (
                        "pasienmasukpenunjang_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_pendaftaran_id" ON "public"."tindakanpelayanan_r" USING btree (
                        "pendaftaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_tgl_proses" ON "public"."tindakanpelayanan_r" USING btree (
                        "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_tindakanpelayanan_id" ON "public"."tindakanpelayanan_r" USING btree (
                        "tindakanpelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_tindakansudahbayar_id" ON "public"."tindakanpelayanan_r" USING btree (
                        "tindakansudahbayar_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

        $this->execute('CREATE INDEX IF NOT EXISTS "ix_tp_tipepaket_id" ON "public"."tindakanpelayanan_r" USING btree (
                        "tipepaket_id" "pg_catalog"."int4_ops" ASC NULLS LAST)');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210209_093606_migrate_20210209_indexing_oddo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210209_093606_migrate_20210209_indexing_oddo cannot be reverted.\n";

        return false;
    }
    */
}
