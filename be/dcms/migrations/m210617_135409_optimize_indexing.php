<?php

use yii\db\Migration;

/**
 * Class m210617_135409_optimize_indexing
 */
class m210617_135409_optimize_indexing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP INDEX if exists "public"."pendaftaran_no_pendaftaran_idx";');

        $this->execute('CREATE INDEX if not exists "pendaftaran_no_pendaftaran_idx" ON "public"."pendaftaran_t" USING btree (no_pendaftaran COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST);');

        $this->execute('CREATE INDEX if not exists "lookuptransaksi_additional_value" ON "public"."lookuptransaksi_m" USING btree (
  "additional_value" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "lookuptransaksi_kode_id" ON "public"."lookuptransaksi_m" USING btree (
  "kode_id" "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "lookuptransaksi_kode_transaksi" ON "public"."lookuptransaksi_m" USING btree (
  "kode_transaksi" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "pasienadmisi_pasienadmisi_id_idx" ON "public"."pasienadmisi_t" USING btree (
  pasienadmisi_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "pembayaran_t_pembayaran_id" ON "public"."pembayaran_t" USING btree (
  pembayaran_id "pg_catalog"."int8_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "pembayaran_t_pendaftaran_id" ON "public"."pembayaran_t" USING btree (
  pendaftaran_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "pembayaranpelayanan_pembayaran_idx" ON "public"."pembayaranpelayanan_t" USING btree (
  pembayaran_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "pembayaranpelayanan_pembayaranpelayanan_idx" ON "public"."pembayaranpelayanan_t" USING btree (pembayaranpelayanan_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "ix_asuransipasien_id" ON "public"."pendaftaran_r" USING btree (
  asuransipasien_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "ix_pasienadmisi_id" ON "public"."pendaftaran_r" USING btree (
  pasienadmisi_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "rsoap_no_rm_idx" ON "public"."riwayatsoap_r" USING btree (
  no_rekam_medik COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);');

        $this->execute('CREATE INDEX if not exists "rsoap_pasien_id_idx" ON "public"."riwayatsoap_r" USING btree (
  pasien_id "pg_catalog"."int4_ops" ASC NULLS LAST
);');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210617_135409_optimize_indexing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210617_135409_optimize_indexing cannot be reverted.\n";

        return false;
    }
    */
}
