<?php

use yii\db\Migration;

/**
 * Class m220801_023712_migrate_odoo_index_table_int_billing_r
 */
class m220801_023712_migrate_odoo_index_table_int_billing_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "int_billing_r_is_sending";
        ');

        $this->execute('
            CREATE INDEX "int_billing_r_is_sending" ON "public"."int_billing_r" USING btree (
              "is_sending" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "int_billing_r_is_sent";
        ');

        $this->execute('
            CREATE INDEX "int_billing_r_is_sent" ON "public"."int_billing_r" USING btree (
              "is_sent" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');

        $this->execute('
            DROP INDEX IF EXISTS "int_billing_r_tgl_proses";
        ');

        $this->execute('
            CREATE INDEX "int_billing_r_tgl_proses" ON "public"."int_billing_r" USING btree (
              "tgl_proses" "pg_catalog"."timestamp_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023712_migrate_odoo_index_table_int_billing_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023712_migrate_odoo_index_table_int_billing_r cannot be reverted.\n";

        return false;
    }
    */
}
