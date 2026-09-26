<?php

use yii\db\Migration;

/**
 * Class m220801_022846_migrate_odoo_trigger_obatsudahbayar_t_discount_cancel
 */
class m220801_022846_migrate_odoo_trigger_obatsudahbayar_t_discount_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "obatsudahbayar_t_discount_cancel" ON "public"."obatsudahbayar_t";
        ');

        $this->execute('
            CREATE TRIGGER "obatsudahbayar_t_discount_cancel" AFTER UPDATE ON "public"."obatsudahbayar_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."obatsudahbayar_t_discount_cancel"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_022846_migrate_odoo_trigger_obatsudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_022846_migrate_odoo_trigger_obatsudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }
    */
}
