<?php

use yii\db\Migration;

/**
 * Class m220801_023754_migrate_odoo_index_table_tindakanpelayanan_r
 */
class m220801_023754_migrate_odoo_index_table_tindakanpelayanan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_carabayar_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_carabayar_id" ON "public"."tindakanpelayanan_r" USING btree (
              "carabayar_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_dokterpenanggungjawab_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_dokterpenanggungjawab_id" ON "public"."tindakanpelayanan_r" USING btree (
              "dokterpenanggungjawab_id" "pg_catalog"."int8_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_id" ON "public"."tindakanpelayanan_r" USING btree (
              "id" "pg_catalog"."int8_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_id_sync_sercon";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_id_sync_sercon" ON "public"."tindakanpelayanan_r" USING btree (
              "id_sync_sercon" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_instalasi_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_instalasi_id" ON "public"."tindakanpelayanan_r" USING btree (
              "instalasi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_kamarruangan_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_kamarruangan_id" ON "public"."tindakanpelayanan_r" USING btree (
              "kamarruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_kelaspelayanan_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_kelaspelayanan_id" ON "public"."tindakanpelayanan_r" USING btree (
              "kelaspelayanan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_pasienadmisi_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_pasienadmisi_id" ON "public"."tindakanpelayanan_r" USING btree (
              "pasienadmisi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_pembayaran_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_pembayaran_id" ON "public"."tindakanpelayanan_r" USING btree (
              "pembayaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_penjamin_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_penjamin_id" ON "public"."tindakanpelayanan_r" USING btree (
              "penjamin_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_ruangan_id";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_ruangan_id" ON "public"."tindakanpelayanan_r" USING btree (
              "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_tarif_dibayarkan";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_tarif_dibayarkan" ON "public"."tindakanpelayanan_r" USING btree (
              "tarif_dibayarkan" "pg_catalog"."float8_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_tarif_dijamin";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_tarif_dijamin" ON "public"."tindakanpelayanan_r" USING btree (
              "tarif_dijamin" "pg_catalog"."float8_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_tp_tarif_diskon";
        ');

        $this->execute('
            CREATE INDEX "ix_tp_tarif_diskon" ON "public"."tindakanpelayanan_r" USING btree (
              "tarif_diskon" "pg_catalog"."float8_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023754_migrate_odoo_index_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023754_migrate_odoo_index_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }
    */
}
