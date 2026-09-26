<?php

use yii\db\Migration;

/**
 * Class m220801_023732_migrate_odoo_index_table_pendaftaran_r
 */
class m220801_023732_migrate_odoo_index_table_pendaftaran_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "int_billing_r_tgl_proses";
        ');

        $this->execute('
            CREATE INDEX "int_billing_r_tgl_proses" ON "public"."int_billing_r" USING btree (
              "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_instalasi_id";
        ');

        $this->execute('
            CREATE INDEX "ix_instalasi_id" ON "public"."pendaftaran_r" USING btree (
              "instalasi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_is_aps";
        ');

        $this->execute('
            CREATE INDEX "ix_is_aps" ON "public"."pendaftaran_r" USING btree (
              "is_aps" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_pasienpulang_id";
        ');

        $this->execute('
            CREATE INDEX "ix_pasienpulang_id" ON "public"."pendaftaran_r" USING btree (
              "pasienpulang_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_pegawai_id";
        ');

        $this->execute('
            CREATE INDEX "ix_pegawai_id" ON "public"."pendaftaran_r" USING btree (
              "pegawai_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_ruangan_id";
        ');

        $this->execute('
            CREATE INDEX "ix_ruangan_id" ON "public"."pendaftaran_r" USING btree (
              "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "ix_rujukan_id";
        ');

        $this->execute('
            CREATE INDEX "ix_rujukan_id" ON "public"."pendaftaran_r" USING btree (
              "rujukan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023732_migrate_odoo_index_table_pendaftaran_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023732_migrate_odoo_index_table_pendaftaran_r cannot be reverted.\n";

        return false;
    }
    */
}
