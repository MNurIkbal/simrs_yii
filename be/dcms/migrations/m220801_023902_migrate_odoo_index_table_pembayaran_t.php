<?php

use yii\db\Migration;

/**
 * Class m220801_023902_migrate_odoo_index_table_pembayaran_t
 */
class m220801_023902_migrate_odoo_index_table_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "pembayaran_t_is_deleted";
        ');

        $this->execute('
            CREATE INDEX "pembayaran_t_is_deleted" ON "public"."pembayaran_t" USING btree (
              "is_deleted" "pg_catalog"."bool_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023902_migrate_odoo_index_table_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023902_migrate_odoo_index_table_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
