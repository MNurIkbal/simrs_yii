<?php

use yii\db\Migration;

/**
 * Class m220801_023148_migrate_odoo_trigger_pembayaranpelayanan_batal
 */
class m220801_023148_migrate_odoo_trigger_pembayaranpelayanan_batal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "pembayaranpelayanan_batal" ON "public"."pembayaranpelayanan_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembayaranpelayanan_batal" BEFORE UPDATE ON "public"."pembayaranpelayanan_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembayaranpelayanan_batal"();
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_023148_migrate_odoo_trigger_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_023148_migrate_odoo_trigger_pembayaranpelayanan_batal cannot be reverted.\n";

        return false;
    }
    */
}
