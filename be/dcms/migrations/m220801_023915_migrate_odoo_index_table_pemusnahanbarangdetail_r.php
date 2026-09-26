<?php

use yii\db\Migration;

/**
 * Class m220801_023915_migrate_odoo_index_table_pemusnahanbarangdetail_r
 */
class m220801_023915_migrate_odoo_index_table_pemusnahanbarangdetail_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_barang_id";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_barang_id" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "barang_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_id_sync_sercon";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_id_sync_sercon" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "id_sync_sercon" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_is_sending";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_is_sending" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_is_sent";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_is_sent" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_pemusnahanbarang_id";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_pemusnahanbarang_id" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "pemusnahanbarang_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_pemusnahanbarangdetail_id";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_pemusnahanbarangdetail_id" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "pemusnahanbarangdetail_id" "pg_catalog"."int4_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_sync_respon";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_sync_respon" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "sync_respon" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "pemusnahanbarangdetail_r_tgl_proses";
        ');

        $this->execute('
            CREATE INDEX "pemusnahanbarangdetail_r_tgl_proses" ON "public"."pemusnahanbarangdetail_r" USING btree (
              "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023915_migrate_odoo_index_table_pemusnahanbarangdetail_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023915_migrate_odoo_index_table_pemusnahanbarangdetail_r cannot be reverted.\n";

        return false;
    }
    */
}
