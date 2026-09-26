<?php

use yii\db\Migration;

/**
 * Class m220801_022935_migrate_odoo_trigger_pembayaran_r_delete
 */
class m220801_022935_migrate_odoo_trigger_pembayaran_r_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "pembayaran_r_delete" ON "public"."pembayaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembayaran_r_delete" AFTER UPDATE OF "is_deleted" ON "public"."pembayaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembayaran_r_delete"();
        ');

        $this->execute('
           ALTER TABLE "public"."pembayaranpelayanan_t" DISABLE TRIGGER "pembayaran_r_delete";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_022935_migrate_odoo_trigger_pembayaran_r_delete cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_022935_migrate_odoo_trigger_pembayaran_r_delete cannot be reverted.\n";

        return false;
    }
    */
}
