<?php

use yii\db\Migration;

/**
 * Class m220801_022950_migrate_odoo_trigger_pembayaranpelayanan_r_insert
 */
class m220801_022950_migrate_odoo_trigger_pembayaranpelayanan_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "pembayaranpelayanan_r_insert" ON "public"."pembayaranpelayanan_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembayaranpelayanan_r_insert" AFTER INSERT ON "public"."pembayaranpelayanan_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembayaranpelayanan_r_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_022950_migrate_odoo_trigger_pembayaranpelayanan_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_022950_migrate_odoo_trigger_pembayaranpelayanan_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
